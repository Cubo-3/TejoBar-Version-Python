from django.contrib import messages
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.http import HttpRequest, HttpResponse, JsonResponse
from django.shortcuts import get_object_or_404, redirect, render
from django.views.decorators.http import require_POST
from django.utils import timezone
from django.conf import settings
from django.urls import reverse
import json
import mercadopago

from .forms import (
    EquipoForm,
    LoginForm,
    PersonaForm,
    ProductoForm,
    RegistroForm,
    PartidoForm,
    CanchaForm,
    CategoriaForm,
    JugadorEquipoForm,
)
from .models import Apartado, Equipo, Historial, Jugador, Persona, Producto, JugadorEquipo, Novedad, Partido, Cancha, Categoria, NovedadJugador
from .media_utils import cloudinary_status_message


def admin_required(view_func):
    def wrapper(request, *args, **kwargs):
        if not request.user.is_authenticated:
            return redirect("tejobar_app:login")
        persona = getattr(request.user, "persona", None)
        if not persona or persona.rol != Persona.ROL_ADMIN:
            messages.error(request, "No tienes permisos para acceder a esta área.")
            return redirect("tejobar_app:home")
        return view_func(request, *args, **kwargs)
    return wrapper


def home(request: HttpRequest) -> HttpResponse:
    Producto.actualizar_stock_vencidos()
    Apartado.liberar_carritos_abandonados(horas_limite=2)
    productos = Producto.objects.disponibles()
    
    categoria_id = request.GET.get('categoria_id')
    if categoria_id:
        productos = productos.filter(categoria_id=categoria_id)
        
    productos = productos[:6]
    categorias = Categoria.objects.filter(estado=True)
    
    context = {
        "productos": productos,
        "categorias": categorias,
        "categoria_seleccionada": int(categoria_id) if categoria_id else None
    }
    return render(request, "home.html", context)


def login_view(request: HttpRequest) -> HttpResponse:
    if request.user.is_authenticated:
        return redirect("tejobar_app:dashboard")

    if request.method == "POST":
        form = LoginForm(request, data=request.POST)
        if form.is_valid():
            user = form.get_user()
            login(request, user)
            messages.success(request, f"Bienvenido, {user.get_full_name() or user.username}")
            next_url = request.GET.get("next") or "tejobar_app:dashboard"
            return redirect(next_url)
    else:
        form = LoginForm(request)

    return render(request, "auth/login.html", {"form": form})


def logout_view(request: HttpRequest) -> HttpResponse:
    logout(request)
    messages.success(request, "Sesión cerrada correctamente")
    return redirect("tejobar_app:home")


def register_view(request: HttpRequest) -> HttpResponse:
    if request.user.is_authenticated:
        return redirect("tejobar_app:dashboard")

    if request.method == "POST":
        form = RegistroForm(request.POST)
        if form.is_valid():
            nombre = form.cleaned_data["nombre"]
            rol = "jugador"
            correo = form.cleaned_data["correo"]
            numero = form.cleaned_data["numero"]
            password = form.cleaned_data["password"]

            user = User.objects.create_user(
                username=correo,
                email=correo,
                password=password,
                first_name=nombre,
            )

            persona = Persona.objects.create(
                user=user,
                nombre=nombre,
                correo=correo,
                numero=numero,
                rol=rol,
            )

            if rol in ("jugador", "capitan"):
                Jugador.objects.create(persona=persona, estado=True, rut=f"RUT{persona.pk}")

            messages.success(request, "Usuario registrado correctamente. Ahora puedes iniciar sesión.")
            return redirect("tejobar_app:login")
    else:
        form = RegistroForm()

    return render(request, "auth/register.html", {"form": form})


def product_list(request: HttpRequest) -> HttpResponse:
    Producto.actualizar_stock_vencidos()
    Apartado.liberar_carritos_abandonados(horas_limite=2)
    productos = Producto.objects.disponibles()
    
    categoria_id = request.GET.get('categoria_id')
    if categoria_id:
        productos = productos.filter(categoria_id=categoria_id)
        
    categorias = Categoria.objects.filter(estado=True)
    
    context = {
        "productos": productos,
        "categorias": categorias,
        "categoria_seleccionada": int(categoria_id) if categoria_id else None
    }
    return render(request, "productos/index.html", context)


def product_detail(request: HttpRequest, pk: int) -> HttpResponse:
    producto = get_object_or_404(Producto, pk=pk)
    return render(request, "productos/show.html", {"producto": producto})


@login_required
def apartar_producto(request: HttpRequest, pk: int) -> HttpResponse:
    from django.db import transaction

    if request.method == "POST":
        try:
            cantidad = int(request.POST.get("cantidad", "1"))
        except ValueError:
            cantidad = 1

        if cantidad <= 0:
            messages.error(request, "La cantidad debe ser mayor que cero.")
            return redirect("tejobar_app:productos_show", pk=pk)

        persona = getattr(request.user, "persona", None)
        if not persona:
            messages.error(request, "No tienes un perfil de persona asociado.")
            return redirect("tejobar_app:productos_show", pk=pk)

        # Buscar si el jugador tiene un partido activo hoy
        partido_asociado = None
        equipo_asociado = None
        if hasattr(persona, 'jugador'):
            from .models import Partido
            from django.db import models
            equipos_del_jugador = persona.jugador.jugador_equipos.values_list('equipo_id', flat=True)
            if equipos_del_jugador:
                partido = Partido.objects.filter(
                    (models.Q(equipo1_id__in=equipos_del_jugador) | models.Q(equipo2_id__in=equipos_del_jugador)),
                    estado=Partido.ESTADO_CONFIRMADA,
                    fecha=timezone.localdate()
                ).first()
                if partido:
                    partido_asociado = partido
                    if partido.equipo1_id in equipos_del_jugador:
                        equipo_asociado = partido.equipo1
                    else:
                        equipo_asociado = partido.equipo2

        try:
            with transaction.atomic():
                producto = get_object_or_404(Producto.objects.select_for_update(), pk=pk)

                if producto.stock < cantidad:
                    messages.error(request, f"Lo sentimos, stock insuficiente. Disponible: {producto.stock} unidades.")
                    return redirect("tejobar_app:productos_show", pk=producto.pk)

                if producto.fecha_vencimiento and producto.fecha_vencimiento < timezone.now().date():
                    messages.error(request, "Este producto está expirado y no puede ser apartado.")
                    return redirect("tejobar_app:productos_show", pk=producto.pk)

                if partido_asociado and equipo_asociado:
                    apartado = Apartado.objects.filter(persona=persona, producto=producto, estado='pendiente', partido=partido_asociado, equipo=equipo_asociado).first()
                else:
                    apartado = Apartado.objects.filter(persona=persona, producto=producto, estado='pendiente', partido__isnull=True).first()

                if apartado:
                    apartado.cantidad += cantidad
                    apartado.save()
                else:
                    Apartado.objects.create(
                        persona=persona,
                        producto=producto,
                        cantidad=cantidad,
                        estado="pendiente",
                        partido=partido_asociado,
                        equipo=equipo_asociado
                    )
                
                Novedad.objects.create(
                    producto=producto,
                    tipo_novedad=Novedad.TIPO_VENDIDO,
                    cantidad=cantidad,
                    descripcion="Separado/Vendido por sistema"
                )
                
                from .models import MovimientoInventario
                MovimientoInventario.objects.create(
                    producto=producto,
                    tipo_movimiento=MovimientoInventario.TIPO_VENTA,
                    cantidad=cantidad,
                    motivo="Apartado online",
                    usuario=request.user
                )

            messages.success(request, f"¡{cantidad}x {producto.nombre} añadido al carrito con éxito!")
            next_url = request.POST.get("next")
            if next_url:
                return redirect(next_url)
            return redirect("tejobar_app:productos_show", pk=producto.pk)
            
        except Exception as e:
            messages.error(request, "Ocurrió un error de concurrencia al agregar el producto. Inténtalo nuevamente.")
            return redirect("tejobar_app:productos_show", pk=pk)

    next_url = request.GET.get("next")
    if next_url:
        return redirect(next_url)
    return redirect("tejobar_app:productos_show", pk=producto.pk)


@login_required
def dashboard(request: HttpRequest) -> HttpResponse:
    Producto.actualizar_stock_vencidos()
    Apartado.liberar_carritos_abandonados(horas_limite=2)
    persona = getattr(request.user, "persona", None)
    if not persona:
        messages.error(request, "No tienes un perfil de persona asociado.")
        return redirect("tejobar_app:home")

    rol = persona.rol

    context: dict = {"usuario": persona, "rol": rol}

    fecha_inicio = request.GET.get("fecha_inicio")
    fecha_fin = request.GET.get("fecha_fin")

    if rol == Persona.ROL_ADMIN:
        apartados = Apartado.objects.select_related("persona", "producto").order_by("-fecha_apartado").all()
        if fecha_inicio:
            apartados = apartados.filter(fecha_apartado__gte=fecha_inicio)
        if fecha_fin:
            apartados = apartados.filter(fecha_apartado__lte=fecha_fin)
            
        pedidos_por_entregar = Historial.objects.filter(estado="por_entregar").select_related("persona", "producto").order_by("fecha_entrega")

        from django.db.models import Sum
        from .models import MovimientoInventario
        hoy = timezone.localdate()
        movimientos_hoy = MovimientoInventario.objects.filter(fecha__date=hoy)
        ventas_hoy = movimientos_hoy.filter(tipo_movimiento=MovimientoInventario.TIPO_VENTA).aggregate(Sum('cantidad'))['cantidad__sum'] or 0
        perdidas_hoy = movimientos_hoy.filter(tipo_movimiento=MovimientoInventario.TIPO_PERDIDA).aggregate(Sum('cantidad'))['cantidad__sum'] or 0

        context.update(
            {
                "total_productos_stock": Producto.objects.aggregate(Sum('stock'))['stock__sum'] or 0,
                "ventas_hoy": ventas_hoy,
                "perdidas_hoy": perdidas_hoy,
                "total_productos": Producto.objects.count(),
                "productos_bajo_stock": Producto.objects.filter(stock__lt=10).count(),
                "total_categorias": Categoria.objects.count(),
                "total_equipos": Equipo.objects.count(),
                "total_jugadores": Jugador.objects.count(),
                "total_partidos": Partido.objects.count(),
                "total_canchas": Cancha.objects.count(),
                "total_usuarios": User.objects.filter(is_active=True).count(),
                "apartados": apartados,
                "pedidos_por_entregar": pedidos_por_entregar,
            }
        )
    else:
        from django.db.models import Sum, Q
        
        # 1. Miembros y Equipo
        mi_equipo_rel = JugadorEquipo.objects.filter(jugador__persona=persona).select_related('equipo').first()
        mi_equipo = mi_equipo_rel.equipo if mi_equipo_rel else None
        
        mis_compañeros_count = 0
        mis_partidos_count = 0
        if mi_equipo:
            mis_compañeros_count = mi_equipo.equipo_jugadores.count()
            mis_partidos_count = Partido.objects.filter(
                Q(equipo1=mi_equipo) | Q(equipo2=mi_equipo)
            ).exclude(estado=Partido.ESTADO_CANCELADA).count()

        # 2. Apartados y Gastos
        mis_apartados = Apartado.objects.filter(persona=persona).select_related("producto").order_by("-fecha_apartado")
        if fecha_inicio:
            mis_apartados = mis_apartados.filter(fecha_apartado__gte=fecha_inicio)
        if fecha_fin:
            mis_apartados = mis_apartados.filter(fecha_apartado__lte=fecha_fin)
            
        total_carrito = sum(a.producto.precio * a.cantidad for a in mis_apartados if a.estado == 'pendiente')
        mis_items_pendientes = mis_apartados.filter(estado='pendiente').count()
        
        total_gastado = Historial.objects.filter(persona=persona).aggregate(Sum('total'))['total__sum'] or 0

        # 3. Próximo Partido
        proximo_partido = None
        if mi_equipo:
            proximo_partido = Partido.objects.filter(
                Q(equipo1=mi_equipo) | Q(equipo2=mi_equipo),
                fecha__gte=timezone.localdate()
            ).exclude(estado=Partido.ESTADO_CANCELADA).order_by('fecha', 'hora').first()

        context.update(
            {
                "mi_equipo_nombre": mi_equipo.nombre_equipo if mi_equipo else "Sin Equipo",
                "mi_equipo_pk": mi_equipo.pk if mi_equipo else None,
                "mis_compañeros_count": mis_compañeros_count,
                "mis_partidos_count": mis_partidos_count,
                "mis_items_pendientes": mis_items_pendientes,
                "total_gastado": total_gastado,
                "proximo_partido": proximo_partido,
                "mis_apartados": mis_apartados,
                "total_carrito": total_carrito,
            }
        )

    context["fecha_inicio"] = fecha_inicio or ""
    context["fecha_fin"] = fecha_fin or ""

    return render(request, "dashboard/index.html", context)


@login_required
def dashboard_historial(request: HttpRequest) -> HttpResponse:
    persona = getattr(request.user, "persona", None)
    if not persona:
        messages.error(request, "No tienes un perfil de persona asociado.")
        return redirect("tejobar_app:home")

    fecha_inicio = request.GET.get("fecha_inicio")
    fecha_fin = request.GET.get("fecha_fin")

    if persona.rol == Persona.ROL_ADMIN:
        apartados_pendientes = Apartado.objects.pendientes().select_related(
            "persona", "producto"
        )
        apartados_entregados = Historial.objects.select_related(
            "persona", "producto"
        ).order_by("-fecha_entrega")
    else:
        apartados_pendientes = Apartado.objects.pendientes().filter(persona=persona)
        apartados_entregados = Historial.objects.filter(persona=persona).order_by(
            "-fecha_entrega"
        )
        
    if fecha_inicio:
        apartados_pendientes = apartados_pendientes.filter(fecha_apartado__gte=fecha_inicio)
        apartados_entregados = apartados_entregados.filter(fecha_entrega__gte=fecha_inicio)
    
    if fecha_fin:
        apartados_pendientes = apartados_pendientes.filter(fecha_apartado__lte=fecha_fin)
        apartados_entregados = apartados_entregados.filter(fecha_entrega__lte=fecha_fin)

    if fecha_inicio:
        apartados_pendientes = apartados_pendientes.filter(fecha_apartado__gte=fecha_inicio)
        apartados_entregados = apartados_entregados.filter(fecha_entrega__gte=fecha_inicio)
    if fecha_fin:
        from datetime import datetime, time
        # Allow same day filtering by extending time to end of day
        try:
            fin_dt = datetime.strptime(fecha_fin, "%Y-%m-%d").date()
            dt_end = timezone.make_aware(datetime.combine(fin_dt, time.max))
            apartados_pendientes = apartados_pendientes.filter(fecha_apartado__lte=dt_end)
            apartados_entregados = apartados_entregados.filter(fecha_entrega__lte=dt_end)
        except ValueError:
            apartados_pendientes = apartados_pendientes.filter(fecha_apartado__lte=fecha_fin)
            apartados_entregados = apartados_entregados.filter(fecha_entrega__lte=fecha_fin)

    context = {
        "usuario": persona,
        "rol": persona.rol,
        "apartados_pendientes": apartados_pendientes,
        "apartados_entregados": apartados_entregados,
        "fecha_inicio": fecha_inicio or "",
        "fecha_fin": fecha_fin or "",
    }
    return render(request, "dashboard/historial.html", context)


@login_required
def dashboard_reporte_pdf(request: HttpRequest) -> HttpResponse:
    persona = getattr(request.user, "persona", None)
    if not persona or persona.rol != Persona.ROL_ADMIN:
        messages.error(request, "No tienes un perfil de persona asociado o no eres admin.")
        return redirect("tejobar_app:dashboard")

    assert persona is not None

    from django.template.loader import render_to_string
    import xhtml2pdf.pisa as pisa
    from io import BytesIO

    fecha_inicio = request.GET.get("fecha_inicio")
    fecha_fin = request.GET.get("fecha_fin")

    apartados = Apartado.objects.select_related("persona", "producto").order_by("-fecha_apartado").all()
    if fecha_inicio:
        apartados = apartados.filter(fecha_apartado__gte=fecha_inicio)
        
    from datetime import datetime, time
    
    if fecha_fin:
        try:
            fin_dt = datetime.strptime(fecha_fin, "%Y-%m-%d").date()
            dt_end = timezone.make_aware(datetime.combine(fin_dt, time.max))
            apartados = apartados.filter(fecha_apartado__lte=dt_end)
        except ValueError:
            apartados = apartados.filter(fecha_apartado__lte=fecha_fin)
            
    context = {
        "total_productos": Producto.objects.count(),
        "productos_bajo_stock": Producto.objects.filter(stock__lt=10).count(),
        "total_categorias": Categoria.objects.count(),
        "total_equipos": Equipo.objects.count(),
        "total_jugadores": Jugador.objects.count(),
        "total_partidos": Partido.objects.count(),
        "total_canchas": Cancha.objects.count(),
        "total_usuarios": User.objects.filter(is_active=True).count(),
        "apartados": apartados,
        "fecha_inicio": fecha_inicio or "",
        "fecha_fin": fecha_fin or "",
        "generado_por": persona.nombre,
    }

    # Render html
    html_string = render_to_string("dashboard/reporte_pdf.html", context)
    
    # Create PDF
    result = BytesIO()
    pdf = pisa.pisaDocument(BytesIO(html_string.encode("UTF-8")), result)
    
    if not pdf.err:
        response = HttpResponse(result.getvalue(), content_type='application/pdf')
        filename = f"reporte_dashboard_{timezone.now().strftime('%Y%m%d')}.pdf"
        response['Content-Disposition'] = f'attachment; filename="{filename}"'
        return response
    return HttpResponse("Error Rendering PDF", status=400)


@login_required
@admin_required
def persona_list(request: HttpRequest) -> HttpResponse:
    personas = Persona.objects.all()
    return render(request, "dashboard/personas.html", {"personas": personas})


@login_required
@admin_required
def persona_create(request: HttpRequest) -> HttpResponse:
    if request.method == "POST":
        form = PersonaForm(request.POST)
        if form.is_valid():
            # 1. Create Persona (initially without user to avoid integrity issues)
            persona = form.save()
            
            # 2. Check if a User with this email already exists
            user_exists = User.objects.filter(email=persona.correo).first()
            
            if not user_exists:
                # 3. Create Django User
                from django.utils.crypto import get_random_string
                # Generate a consistent but secure-ish initial username from name
                base_username = persona.nombre.lower().replace(" ", "")[:15]
                username = f"{base_username}_{get_random_string(4)}"
                
                # Create the user
                new_user = User.objects.create_user(
                    username=username,
                    email=persona.correo,
                    password="TejoBarUser123!" # Default initial password
                )
                persona.user = new_user
                persona.save()
                messages.info(request, f"Se ha creado una cuenta de acceso para {persona.nombre}. Usuario: {username}, Pass: TejoBarUser123!")
            else:
                persona.user = user_exists
                persona.save()
                messages.info(request, f"Se ha vinculado la Persona al usuario preexistente: {user_exists.username}")

            # 4. If role is player or captain, create Jugador record
            if persona.rol in (Persona.ROL_JUGADOR, Persona.ROL_CAPITAN):
                from .models import Jugador
                Jugador.objects.get_or_create(persona=persona)

            messages.success(request, "Persona y perfil configurados correctamente.")
            return redirect("tejobar_app:personas_index")
    else:
        form = PersonaForm()
    return render(request, "personas/form.html", {"form": form})


@login_required
@admin_required
def persona_update(request: HttpRequest, pk: int) -> HttpResponse:
    persona = get_object_or_404(Persona, pk=pk)
    if request.method == "POST":
        form = PersonaForm(request.POST, instance=persona)
        if form.is_valid():
            persona = form.save()
            
            # Sync role with Jugador existence
            if persona.rol in (Persona.ROL_JUGADOR, Persona.ROL_CAPITAN):
                from .models import Jugador
                Jugador.objects.get_or_create(persona=persona)
            
            messages.success(request, "Persona actualizada correctamente")
            return redirect("tejobar_app:personas_index")
    else:
        form = PersonaForm(instance=persona)
    return render(request, "personas/form.html", {"form": form, "persona": persona})


@login_required
@admin_required
def persona_delete(request: HttpRequest, pk: int) -> HttpResponse:
    persona = get_object_or_404(Persona, pk=pk)
    
    if not persona.can_be_deleted:
        messages.error(request, "No se puede eliminar la persona porque tiene partidos activos o productos apartados.")
        return redirect("tejobar_app:personas_index")
        
    if request.method == "POST":
        persona.delete()
        messages.success(request, "Persona eliminada correctamente")
        return redirect("tejobar_app:personas_index")
    return render(request, "personas/confirm_delete.html", {"persona": persona})


@login_required
def equipo_list(request: HttpRequest) -> HttpResponse:
    persona = getattr(request.user, "persona", None)
    if not persona:
        messages.error(request, "No tienes un perfil válido.")
        return redirect("tejobar_app:home")

    if persona.rol == Persona.ROL_ADMIN:
        equipos = Equipo.objects.all()
        return render(request, "equipos/index.html", {"equipos": equipos})
    
    # Check if user is already in a team
    try:
        if persona.rol in (Persona.ROL_JUGADOR, Persona.ROL_CAPITAN) and hasattr(persona, "jugador"):
            miembro = JugadorEquipo.objects.filter(jugador=persona.jugador).first()
            if miembro:
                return redirect("tejobar_app:equipos_show", pk=miembro.equipo.pk)
    except Exception:
        pass

    # For players without a team, show available teams
    from django.db.models import Count
    equipos = Equipo.objects.annotate(_num_jugadores=Count('equipo_jugadores')).filter(_num_jugadores__lt=5)
    return render(request, "equipos/index.html", {"equipos": equipos})


@login_required
def equipo_detail(request: HttpRequest, pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)
    
    if not persona:
        messages.error(request, "Perfil no válido.")
        return redirect("tejobar_app:home")

    es_miembro = False
    es_capitan = False
    puede_unirse = False
    if hasattr(persona, "jugador"):
        miembro = JugadorEquipo.objects.filter(jugador=persona.jugador, equipo=equipo).first()
        if miembro:
            es_miembro = True
            es_capitan = miembro.es_capitan
        else:
            if not JugadorEquipo.objects.filter(jugador=persona.jugador).exists():
                if equipo.equipo_jugadores.count() < 5:
                    puede_unirse = True

    jugadores_equipo = equipo.equipo_jugadores.select_related("jugador__persona").all()
    
    # Obtener historial de miembros pasados (registrados)
    from .models import HistorialEquipo
    miembros_actuales_ids = jugadores_equipo.filter(jugador__isnull=False).values_list('jugador_id', flat=True)
    historial_pasado = HistorialEquipo.objects.filter(
        equipo=equipo, 
        fecha_salida__isnull=False
    ).exclude(
        jugador_id__in=miembros_actuales_ids
    ).select_related('jugador__persona').order_by('-fecha_salida')

    context = {
        "equipo": equipo,
        "jugadores_equipo": jugadores_equipo,
        "historial_pasado": historial_pasado,
        "es_miembro": es_miembro,
        "es_capitan": es_capitan,
        "es_admin": persona.rol == Persona.ROL_ADMIN,
        "puede_unirse": puede_unirse,
        "add_member_form": JugadorEquipoForm(equipo=equipo) if (es_capitan or persona.rol == Persona.ROL_ADMIN) else None,
    }
    return render(request, "equipos/show.html", context)


@login_required
def equipo_create(request: HttpRequest) -> HttpResponse:
    persona = getattr(request.user, "persona", None)
    if not persona:
        messages.error(request, "No tienes un perfil de persona asociado.")
        return redirect("tejobar_app:equipos_index")

    # Admin puede crear equipos sin necesidad de tener un Jugador
    es_admin = persona.rol == Persona.ROL_ADMIN

    if not es_admin:
        if not hasattr(persona, "jugador"):
            messages.error(request, "Solo los jugadores pueden crear equipos.")
            return redirect("tejobar_app:equipos_index")
        if JugadorEquipo.objects.filter(jugador=persona.jugador).exists():
            messages.error(request, "Ya perteneces a un equipo.")
            return redirect("tejobar_app:equipos_index")

    if request.method == "POST":
        form = EquipoForm(request.POST)
        if form.is_valid():
            equipo = form.save()
            if not es_admin:
                # Para jugadores normales, crear el vínculo de capitán
                JugadorEquipo.objects.create(jugador=persona.jugador, equipo=equipo, es_capitan=True)
                persona.rol = Persona.ROL_CAPITAN
                persona.save()
                messages.success(request, "Equipo creado correctamente. Ahora eres el capitán.")
            else:
                messages.success(request, f"Equipo '{equipo.nombre_equipo}' creado correctamente.")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)
    else:
        form = EquipoForm()
    return render(request, "equipos/form.html", {"form": form})


@login_required
def equipo_update(request: HttpRequest, pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)

    es_capitan = False
    if persona and hasattr(persona, "jugador"):
        es_capitan = JugadorEquipo.objects.filter(jugador=persona.jugador, equipo=equipo, es_capitan=True).exists()

    if not persona or (persona.rol != Persona.ROL_ADMIN and not es_capitan):
        messages.error(request, "No tienes permiso para editar este equipo.")
        return redirect("tejobar_app:equipos_index")

    if request.method == "POST":
        form = EquipoForm(request.POST, instance=equipo)
        if form.is_valid():
            form.save()
            messages.success(request, "Equipo actualizado correctamente")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)
    else:
        form = EquipoForm(instance=equipo)
    return render(request, "equipos/form.html", {"form": form, "equipo": equipo})


@login_required
def equipo_delete(request: HttpRequest, pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)

    es_capitan = False
    if persona and hasattr(persona, "jugador"):
        es_capitan = JugadorEquipo.objects.filter(jugador=persona.jugador, equipo=equipo, es_capitan=True).exists()

    if not persona or (persona.rol != Persona.ROL_ADMIN and not es_capitan):
        messages.error(request, "No tienes permiso para eliminar este equipo.")
        return redirect("tejobar_app:equipos_index")

    if request.method == "POST":
        # Check who the captain was to reset their role if they delete it
        capitanes = JugadorEquipo.objects.filter(equipo=equipo, es_capitan=True)
        for capitan_rel in capitanes:
            p = capitan_rel.jugador.persona
            if p.rol != Persona.ROL_ADMIN:
                p.rol = Persona.ROL_JUGADOR
                p.save()
        
        # When team is deleted, JugadorEquipo cascade deletes automatically (which is good)
        equipo.delete()
        messages.success(request, "Equipo eliminado correctamente. Los miembros han quedado sin equipo.")
        return redirect("tejobar_app:equipos_index")
    return render(request, "equipos/confirm_delete.html", {"equipo": equipo})


@login_required
def equipo_join(request: HttpRequest, pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)
    
    if not persona or not hasattr(persona, "jugador"):
        messages.error(request, "Solo los jugadores pueden unirse a equipos.")
        return redirect("tejobar_app:equipos_index")

    if JugadorEquipo.objects.filter(jugador=persona.jugador).exists():
        messages.error(request, "Ya perteneces a un equipo.")
        return redirect("tejobar_app:equipos_index")

    if equipo.equipo_jugadores.count() >= 5:
        messages.error(request, "El equipo está lleno (límite 5 jugadores).")
        return redirect("tejobar_app:equipos_index")

    if request.method == "POST":
        JugadorEquipo.objects.create(jugador=persona.jugador, equipo=equipo, es_capitan=False)
        persona.rol = Persona.ROL_JUGADOR
        persona.save()
        messages.success(request, f"Te has unido al equipo {equipo.nombre_equipo} exitosamente.")
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)
    
    return redirect("tejobar_app:equipos_index")


@login_required
def equipo_leave(request: HttpRequest, pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)
    
    if not persona or not hasattr(persona, "jugador"):
        messages.error(request, "Perfil no válido.")
        return redirect("tejobar_app:equipos_index")

    miembro = JugadorEquipo.objects.filter(jugador=persona.jugador, equipo=equipo).first()
    if not miembro:
        messages.error(request, "No perteneces a este equipo.")
        return redirect("tejobar_app:equipos_index")

    if request.method == "POST":
        if miembro.es_capitan:
            messages.error(request, "No puedes salir porque eres el capitán. Debes eliminar el equipo o asignar otro capitan (no habilitado actualmente).")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)

        miembro.delete()
        messages.success(request, f"Has salido del equipo {equipo.nombre_equipo}.")
        return redirect("tejobar_app:equipos_index")
        
    return redirect("tejobar_app:equipos_show", pk=equipo.pk)


@login_required
def equipo_reactivate(request: HttpRequest, pk: int) -> HttpResponse:
    from django.core.exceptions import ValidationError
    
    if request.method == "POST":
        persona = getattr(request.user, "persona", None)
        
        if not persona or getattr(persona, "rol", "") != Persona.ROL_JUGADOR or not hasattr(persona, "jugador"):
            messages.error(request, "Solo los jugadores pueden unirse a equipos.")
            return redirect("tejobar_app:equipos_index")

        try:
            nuevo_vinculo = JugadorEquipo.reactivar_jugador_en_equipo(
                jugador=persona.jugador, 
                equipo_destino_id=pk
            )
            if nuevo_vinculo.es_capitan:
                msg = f"¡Te has reintegrado a {nuevo_vinculo.equipo.nombre_equipo} asumiendo la capitanía!"
            else:
                msg = f"¡Te has reintegrado a {nuevo_vinculo.equipo.nombre_equipo} como jugador!"
            messages.success(request, msg)
            return redirect("tejobar_app:equipos_show", pk=pk)
            
        except ValidationError as e:
            error_msg = str(list(e.message_dict.values())[0][0] if hasattr(e, 'message_dict') else e.messages[0] if hasattr(e, 'messages') else e)
            messages.error(request, error_msg)
        except Exception as e:
            messages.error(request, "Ha ocurrido un error inesperado al reactivar el equipo.")

    return redirect("tejobar_app:equipos_index")

@login_required
def equipo_add_member(request: HttpRequest, pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)
    
    es_capitan = False
    if persona and hasattr(persona, "jugador"):
        es_capitan = JugadorEquipo.objects.filter(jugador=persona.jugador, equipo=equipo, es_capitan=True).exists()

    if not persona or (persona.rol != Persona.ROL_ADMIN and not es_capitan):
        messages.error(request, "No tienes permiso para agregar jugadores a este equipo.")
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)
        
    if equipo.equipo_jugadores.count() >= 5:
        messages.error(request, "El equipo está lleno (límite 5 jugadores).")
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)

    if request.method == "POST":
        form = JugadorEquipoForm(request.POST, equipo=equipo)
        if form.is_valid():
            nuevo_miembro = form.save(commit=False)
            nuevo_miembro.equipo = equipo
            nuevo_miembro.es_capitan = False
            nuevo_miembro.save()
            messages.success(request, f"Se ha agregado un nuevo jugador al equipo.")
        else:
            for field, errors in form.errors.items():
                for error in errors:
                    messages.error(request, f"Error: {error}")
                    
    return redirect("tejobar_app:equipos_show", pk=equipo.pk)


@login_required
def equipo_remove_member(request: HttpRequest, pk: int, jugador_pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)
    
    es_capitan = False
    if persona and hasattr(persona, "jugador"):
        es_capitan = JugadorEquipo.objects.filter(jugador=persona.jugador, equipo=equipo, es_capitan=True).exists()

    if not persona or (persona.rol != Persona.ROL_ADMIN and not es_capitan):
        messages.error(request, "No tienes permiso para expulsar jugadores de este equipo.")
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)

    assert persona is not None

    if request.method == "POST":
        razon = request.POST.get("razon", "").strip()
        if not razon or len(razon) < 5:
            messages.error(request, "Debes indicar una razón de expulsión (mínimo 5 caracteres).")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)

        is_invitado = request.POST.get("is_invitado") == "1"

        try:
            if is_invitado:
                # Jugador invitado: el pk enviado es el pk de JugadorEquipo
                miembro_a_expulsar = get_object_or_404(JugadorEquipo, equipo=equipo, pk=jugador_pk)
            else:
                # Jugador registrado: el pk enviado es el pk de Persona
                miembro_a_expulsar = get_object_or_404(JugadorEquipo, equipo=equipo, jugador__persona__pk=jugador_pk)
        except Exception:
            messages.error(request, "No se encontró al jugador en este equipo.")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)
        
        # Prevent captain from removing themselves through this view, they should use delete team
        if miembro_a_expulsar.es_capitan and persona.rol != Persona.ROL_ADMIN:
            messages.error(request, "El capitán no puede ser expulsado. Elimina el equipo si deseas salir.")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)

        nombre_expulsado = miembro_a_expulsar.get_nombre()
        miembro_a_expulsar.delete()
        messages.success(
            request,
            f"Jugador '{nombre_expulsado}' expulsado del equipo. Razón: {razon}"
        )
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)

    return redirect("tejobar_app:equipos_show", pk=equipo.pk)


@admin_required
def admin_venta_directa(request: HttpRequest) -> HttpResponse:
    from django.db import transaction

    productos = Producto.objects.filter(stock__gt=0).select_related("categoria").order_by("categoria__nombre", "nombre")
    canchas = Cancha.objects.filter(estado=True).order_by("disponibilidad")

    if request.method == "POST":
        producto_ids = request.POST.getlist("producto_id[]")
        cantidades = request.POST.getlist("cantidad[]")
        cliente_nombre = request.POST.get("cliente_nombre", "").strip()
        cancha_id = request.POST.get("cancha_id", "").strip()

        cancha = None
        if cancha_id:
            cancha = Cancha.objects.filter(pk=cancha_id).first()

        if not producto_ids or not cantidades or len(producto_ids) != len(cantidades):
            messages.error(request, "No hay productos en la lista de venta.")
        else:
            try:
                with transaction.atomic():
                    total_articulos = 0
                    total_precio = 0
                    cancha_label = f"Cancha: {cancha.disponibilidad}" if cancha else ""
                    for pid, cant_str in zip(producto_ids, cantidades):
                        cantidad = int(cant_str)
                        if cantidad <= 0: raise ValueError("Cantidad inválida.")
                        
                        producto = get_object_or_404(Producto.objects.select_for_update(), pk=pid)
                        if producto.stock < cantidad:
                            raise ValueError(f"Stock insuficiente para {producto.nombre}")
                        
                        partes_motivo = ["Venta Directa POS"]
                        if cliente_nombre:
                            partes_motivo.append(f"Cliente: {cliente_nombre}")
                        if cancha_label:
                            partes_motivo.append(cancha_label)

                        # Registrar Movimiento
                        MovimientoInventario.objects.create(
                            producto=producto,
                            tipo_movimiento=MovimientoInventario.TIPO_VENTA,
                            cantidad=cantidad,
                            motivo=" | ".join(partes_motivo),
                            usuario=request.user
                        )
                        
                        # Registrar Novedad
                        Novedad.objects.create(
                            producto=producto,
                            tipo_novedad=Novedad.TIPO_VENDIDO,
                            cantidad=cantidad,
                            descripcion=" | ".join(partes_motivo)
                        )
                        total_articulos += cantidad
                        total_precio += (producto.precio * cantidad)
                    
                    exito_msg = f"Venta procesada con éxito: {total_articulos} artículos por ${total_precio:,.0f}"
                    if cancha:
                        exito_msg += f" — {cancha_label}"
                    messages.success(request, exito_msg)
                    return redirect("tejobar_app:admin_venta_directa")
            except ValueError as e:
                messages.error(request, str(e))
            except Exception:
                messages.error(request, "Error interno al procesar la venta.")

    return render(request, "ventas/directa.html", {
        "productos": productos,
        "canchas": canchas,
    })


@admin_required
@require_POST
def api_crear_categoria(request: HttpRequest) -> JsonResponse:
    try:
        data = json.loads(request.body)
        nombre = data.get('nombre', '').strip()
        desc = data.get('descripcion', '').strip()
        
        if not nombre:
            return JsonResponse({'success': False, 'error': 'El nombre es obligatorio'})
            
        if Categoria.objects.filter(nombre__iexact=nombre).exists():
            return JsonResponse({'success': False, 'error': 'Ya existe una categoría con este nombre'})
            
        categoria = Categoria.objects.create(nombre=nombre, descripcion=desc, estado=True)
        return JsonResponse({
            'success': True, 
            'categoria': {'id': categoria.id, 'nombre': categoria.nombre}
        })
    except Exception as e:
        return JsonResponse({'success': False, 'error': str(e)})


@admin_required
def admin_product_template_download(request: HttpRequest) -> HttpResponse:
    import csv
    response = HttpResponse(content_type='text/csv; charset=utf-8')
    response['Content-Disposition'] = 'attachment; filename="plantilla_productos.csv"'
    
    # Write UTF-8 BOM so Excel opens special characters correctly
    response.write('\ufeff')
    
    writer = csv.writer(response)
    # Header columns in lowercase exactly as required by services.py
    writer.writerow(['nombre', 'precio', 'stock', 'categoria', 'descripcion'])
    
    # Elegant sample rows to guide the user
    writer.writerow(['Cerveza Club Colombia', '3500', '24', 'Bebidas', 'Lager premium nacional de 330ml'])
    writer.writerow(['Aguardiente Antioqueño', '45000', '10', 'Licores', 'Media botella de aguardiente sin azúcar'])
    
    return response


@admin_required
def admin_product_list(request: HttpRequest) -> HttpResponse:
    Producto.actualizar_stock_vencidos()
    productos = Producto.objects.select_related('categoria').all()
    categorias = Categoria.objects.all()

    # Filtros
    q = request.GET.get('q', '').strip()
    categoria_id = request.GET.get('categoria', '')
    estado = request.GET.get('estado', '')
    vencimiento = request.GET.get('vencimiento', '')
    orden = request.GET.get('orden', '')

    if q:
        productos = productos.filter(nombre__icontains=q)
    
    if categoria_id:
        productos = productos.filter(categoria_id=categoria_id)

    if estado == 'activo':
        productos = productos.filter(activo=True)
    elif estado == 'inactivo':
        productos = productos.filter(activo=False)

    if vencimiento:
        from datetime import date
        today = date.today()
        if vencimiento == 'vencidos':
            productos = productos.filter(fecha_vencimiento__lt=today)
        elif vencimiento == 'vigentes':
            productos = productos.filter(fecha_vencimiento__gte=today)
        elif vencimiento == 'sin_fecha':
            productos = productos.filter(fecha_vencimiento__isnull=True)

    # Ordenamiento
    if orden == 'nombre_asc':
        productos = productos.order_by('nombre')
    elif orden == 'nombre_desc':
        productos = productos.order_by('-nombre')
    elif orden == 'precio_asc':
        productos = productos.order_by('precio')
    elif orden == 'precio_desc':
        productos = productos.order_by('-precio')
    elif orden == 'stock_asc':
        productos = productos.order_by('stock')
    elif orden == 'stock_desc':
        productos = productos.order_by('-stock')
    else:
        # Por defecto
        productos = productos.order_by('-id')

    context = {
        "productos": productos,
        "categorias": categorias,
        "filtros": {
            "q": q,
            "categoria": int(categoria_id) if categoria_id.isdigit() else '',
            "estado": estado,
            "vencimiento": vencimiento,
            "orden": orden
        }
    }
    return render(request, "productos/admin_index.html", context)


@admin_required
def admin_product_create(request: HttpRequest) -> HttpResponse:
    if request.method == "POST":
        form = ProductoForm(request.POST, request.FILES)
        if form.is_valid():
            try:
                nuevo_prod = form.save()
            except Exception as exc:
                messages.error(
                    request,
                    f"No se pudo guardar la imagen del producto: {exc}. {cloudinary_status_message()}",
                )
                return render(request, "productos/form.html", {"form": form})
            Novedad.objects.create(
                producto=nuevo_prod,
                tipo_novedad=Novedad.TIPO_AGREGADO,
                cantidad=nuevo_prod.stock,
                descripcion="Nuevo producto o lote agregado"
            )
            messages.success(request, "Producto creado correctamente")
            return redirect("tejobar_app:admin_productos_index")
    else:
        form = ProductoForm()
    return render(request, "productos/form.html", {"form": form})


@admin_required
def admin_product_update(request: HttpRequest, pk: int) -> HttpResponse:
    producto = get_object_or_404(Producto, pk=pk)
    stock_anterior = producto.stock
    
    if request.method == "POST":
        form = ProductoForm(request.POST, request.FILES, instance=producto)
        if form.is_valid():
            try:
                prod_actualizado = form.save()
            except Exception as exc:
                messages.error(
                    request,
                    f"Producto actualizado, pero la imagen no se guardó: {exc}. {cloudinary_status_message()}",
                )
                return render(request, "productos/form.html", {"form": form, "producto": producto})
            if prod_actualizado.stock > stock_anterior:
                Novedad.objects.create(
                    producto=prod_actualizado,
                    tipo_novedad=Novedad.TIPO_AGREGADO,
                    cantidad=(prod_actualizado.stock - stock_anterior),
                    descripcion="Stock adicional agregado manualmente"
                )
            elif prod_actualizado.stock < stock_anterior:
                Novedad.objects.create(
                    producto=prod_actualizado,
                    tipo_novedad=Novedad.TIPO_PERDIDA,
                    cantidad=(stock_anterior - prod_actualizado.stock),
                    descripcion="Stock reducido manualmente (accidente/pérdida)"
                )
            messages.success(request, "Producto actualizado correctamente")
            return redirect("tejobar_app:admin_productos_index")
    else:
        form = ProductoForm(instance=producto)
    return render(request, "productos/form.html", {"form": form, "producto": producto})


@admin_required
def admin_product_delete(request: HttpRequest, pk: int) -> HttpResponse:
    producto = get_object_or_404(Producto, pk=pk)
    
    esta_activo = producto.activo
    tiene_historial = producto.historial.exists()
    tiene_apartados = producto.apartados.exists()
    
    # Solo bloqueamos la eliminación si está activo, o si tiene dependencias de historial.
    # Si está inactivo y tiene stock, permitimos borrarlo (el POST registrará la pérdida del stock).
    if esta_activo or tiene_historial:
        motivos = []
        if esta_activo:
            motivos.append("está activo")
        if producto.stock > 0 and esta_activo:
            motivos.append("tiene stock disponible")
        if tiene_historial:
            motivos.append("tiene historial de ventas")
            
        motivos_str = " y ".join([", ".join(motivos[:-1]), motivos[-1]] if len(motivos) > 1 else motivos)
        
        msg = f"No se puede eliminar el producto porque {motivos_str}."
        if esta_activo:
            msg += " Si desea inhabilitar el producto, por favor cambie su estado usando el botón correspondiente en la tabla."
            
        messages.error(request, msg)
        return redirect("tejobar_app:admin_productos_index")

    if request.method == "POST":
        if producto.stock > 0:
            Novedad.objects.create(
                producto=None,
                tipo_novedad=Novedad.TIPO_PERDIDA,
                cantidad=producto.stock,
                descripcion=f"Producto eliminado con stock restante: {producto.nombre}"
            )
        producto.delete()
        messages.success(request, "Producto eliminado correctamente")
        return redirect("tejobar_app:admin_productos_index")
    return render(request, "productos/confirm_delete.html", {"producto": producto})


@admin_required
def admin_product_toggle_active(request: HttpRequest, pk: int) -> HttpResponse:
    producto = get_object_or_404(Producto, pk=pk)
    producto.activo = not producto.activo
    producto.save()
    estado_str = "activado" if producto.activo else "desactivado"
    messages.success(request, f"Producto {estado_str} correctamente.")
    return redirect("tejobar_app:admin_productos_index")


@login_required
def crear_preferencia_carrito(request):
    persona = getattr(request.user, "persona", None)
    if not persona:
        messages.error(request, "Perfil no encontrado")
        return redirect("tejobar_app:dashboard")

    apartados_pendientes = Apartado.objects.filter(persona=persona, estado=Apartado.ESTADO_PENDIENTE)
    if not apartados_pendientes.exists():
        messages.warning(request, "Tu carrito está vacío.")
        return redirect("tejobar_app:dashboard")

    sdk = mercadopago.SDK(settings.MERCADOPAGO_ACCESS_TOKEN)
    items = []
    
    for ap in apartados_pendientes:
        items.append({
            "id": str(ap.producto.pk),
            "title": f"Producto: {ap.producto.nombre}",
            "quantity": int(ap.cantidad),
            "currency_id": "COP",
            "unit_price": float(ap.producto.precio)
        })

    back_urls = {
        "success": "https://tejobar-version-python-production.up.railway.app/pago-exitoso/",
        "failure": "https://tejobar-version-python-production.up.railway.app/pago-fallido/",
        "pending": "https://tejobar-version-python-production.up.railway.app/pago-pendiente/"
    }

    preference_data = {
        "items": items,
        "back_urls": back_urls,
        "auto_return": "approved",
        "external_reference": f"carrito_{persona.pk}"
    }

    preference_response = sdk.preference().create(preference_data)
    
    if preference_response.get("status") in (200, 201) and "init_point" in preference_response.get("response", {}):
        return redirect(preference_response["response"]["init_point"])
    else:
        error_msg = preference_response.get("response", "Error desconocido en MercadoPago")
        import logging
        logger = logging.getLogger(__name__)
        logger.error(f"Error creando preferencia MercadoPago: {preference_response}")
        messages.error(request, f"Error al conectar con MercadoPago: Revise los datos. Detalles: {error_msg}")
        return redirect("tejobar_app:dashboard")


@admin_required
def admin_novedades_index(request: HttpRequest) -> HttpResponse:
    fecha_inicio = request.GET.get("fecha_inicio")
    fecha_fin = request.GET.get("fecha_fin")
    tipo_novedad = request.GET.get("tipo_novedad")
    producto_id = request.GET.get("producto_id")
    categoria_id = request.GET.get("categoria_id")

    novedades = Novedad.objects.select_related("producto", "producto__categoria").order_by("-fecha")

    if fecha_inicio:
        novedades = novedades.filter(fecha__gte=fecha_inicio)
    
    if fecha_fin:
        from datetime import datetime, time
        from django.utils import timezone
        try:
            fin_dt = datetime.strptime(fecha_fin, "%Y-%m-%d").date()
            dt_end = timezone.make_aware(datetime.combine(fin_dt, time.max))
            novedades = novedades.filter(fecha__lte=dt_end)
        except ValueError:
            novedades = novedades.filter(fecha__lte=fecha_fin)
            
    if tipo_novedad:
        novedades = novedades.filter(tipo_novedad=tipo_novedad)
    if producto_id:
        novedades = novedades.filter(producto_id=producto_id)
    if categoria_id:
        novedades = novedades.filter(producto__categoria_id=categoria_id)

    productos_filter = Producto.objects.all().order_by('nombre')
    categorias_filter = Categoria.objects.filter(estado=True).order_by('nombre')

    # Totales por filtros activos
    from django.db.models import Sum
    total_registros = novedades.count()
    tipos_entrada = [Novedad.TIPO_AGREGADO]
    tipos_salida = [Novedad.TIPO_VENDIDO, Novedad.TIPO_VENCIDO, Novedad.TIPO_PERDIDA]
    total_entradas = novedades.filter(tipo_novedad__in=tipos_entrada).aggregate(t=Sum('cantidad'))['t'] or 0
    total_salidas = novedades.filter(tipo_novedad__in=tipos_salida).aggregate(t=Sum('cantidad'))['t'] or 0
    total_neto = total_entradas - total_salidas

    context = {
        "novedades": novedades,
        "fecha_inicio": fecha_inicio or "",
        "fecha_fin": fecha_fin or "",
        "tipo_novedad": tipo_novedad or "",
        "producto_id": producto_id or "",
        "categoria_id": categoria_id or "",
        "productos_filter": productos_filter,
        "categorias_filter": categorias_filter,
        "tipos_choices": Novedad.TIPO_CHOICES,
        "total_registros": total_registros,
        "total_entradas": total_entradas,
        "total_salidas": total_salidas,
        "total_neto": total_neto,
        "total_productos": Producto.objects.count(),
        "productos_bajo_stock": Producto.objects.filter(stock__lt=10).count(),
        "total_categorias": Categoria.objects.count(),
        "total_equipos": Equipo.objects.count(),
        "total_jugadores": Jugador.objects.count(),
        "total_partidos": Partido.objects.count(),
        "total_canchas": Cancha.objects.count(),
        "total_usuarios": User.objects.filter(is_active=True).count(),
        "lista_equipos": Equipo.objects.all(),
        "lista_usuarios": Persona.objects.all(),
        "lista_apartados": Apartado.objects.select_related("persona", "producto").order_by("-fecha_apartado").all(),
    }
    return render(request, "novedades/index.html", context)


@admin_required
def admin_partidos_index(request: HttpRequest) -> HttpResponse:
    partidos = Partido.objects.select_related('equipo1', 'equipo2', 'cancha').order_by('-fecha', '-hora')
    productos = Producto.objects.filter(stock__gt=0).select_related("categoria").order_by("categoria__nombre", "nombre")
    productos_activos = Producto.objects.filter(activo=True, stock__gt=0).order_by('nombre')

    # Para cada partido, pre-cargar jugadores de ambos equipos (para el modal de consumo)
    partidos_data = []
    for p in partidos:
        jugadores_e1 = []
        jugadores_e2 = []
        if p.equipo1:
            jugadores_e1 = list(JugadorEquipo.objects.filter(equipo=p.equipo1).select_related('jugador__persona'))
        if p.equipo2:
            jugadores_e2 = list(JugadorEquipo.objects.filter(equipo=p.equipo2).select_related('jugador__persona'))
        partidos_data.append({
            'partido': p,
            'jugadores_e1': jugadores_e1,
            'jugadores_e2': jugadores_e2,
        })

    return render(request, "partidos/admin_index.html", {
        "partidos": partidos,
        "partidos_data": partidos_data,
        "productos_activos": productos_activos,
        "productos": productos
    })


@admin_required
def admin_partidos_create(request: HttpRequest) -> HttpResponse:
    if request.method == "POST":
        form = PartidoForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, "Partido creado exitosamente.")
            return redirect("tejobar_app:admin_partidos_index")
    else:
        form = PartidoForm()
    return render(request, "partidos/form.html", {"form": form})


@admin_required
def admin_partidos_update(request: HttpRequest, pk: int) -> HttpResponse:
    partido = get_object_or_404(Partido, pk=pk)
    
    if partido.hora_inicio:
        messages.error(request, "No se puede editar un partido que ya está en curso o ha finalizado.")
        return redirect("tejobar_app:admin_partidos_index")
        
    if request.method == "POST":
        form = PartidoForm(request.POST, instance=partido)
        if form.is_valid():
            form.save()
            messages.success(request, "Partido actualizado exitosamente.")
            return redirect("tejobar_app:admin_partidos_index")
    else:
        form = PartidoForm(instance=partido)
    return render(request, "partidos/form.html", {"form": form, "partido": partido})


@admin_required
def admin_partidos_delete(request: HttpRequest, pk: int) -> HttpResponse:
    partido = get_object_or_404(Partido, pk=pk)
    if request.method == "POST":
        motivo = request.POST.get("motivo", "").strip()
        if len(motivo) > 255:
            messages.error(request, "El motivo no puede superar los 255 caracteres.")
            return render(
                request,
                "partidos/confirm_delete.html",
                {"partido": partido, "motivo": motivo},
            )
        equipo1 = partido.equipo1.nombre_equipo if partido.equipo1 else "—"
        equipo2 = partido.equipo2.nombre_equipo if partido.equipo2 else "—"
        resumen = (
            f"Partido #{partido.pk} eliminado: {equipo1} vs {equipo2} "
            f"({partido.fecha.strftime('%d/%m/%Y')} {partido.hora})"
        )
        if motivo:
            Novedad.objects.create(
                producto=None,
                tipo_novedad=Novedad.TIPO_CANCHA,
                cantidad=0,
                descripcion=f"{resumen}. Motivo: {motivo}"[:255],
            )
        partido.delete()
        msg = "Partido eliminado correctamente."
        if motivo:
            msg += f" Motivo: {motivo}"
        messages.success(request, msg)
        return redirect("tejobar_app:admin_partidos_index")
    return render(request, "partidos/confirm_delete.html", {"partido": partido})


def _notificar_capitanes_partido(partido, mensaje):
    from .models import Notificacion, JugadorEquipo
    for e_id in [partido.equipo1_id, partido.equipo2_id]:
        if e_id:
            je = JugadorEquipo.objects.filter(equipo_id=e_id, es_capitan=True).select_related('jugador__persona__user').first()
            if je and je.jugador.persona.user:
                Notificacion.objects.create(
                    usuario=je.jugador.persona.user,
                    mensaje=mensaje,
                    tipo=Notificacion.TIPO_PARTIDO,
                    enlace="/partidos/"
                )

@admin_required
def iniciar_partido(request: HttpRequest, pk: int) -> HttpResponse:
    partido = get_object_or_404(Partido, pk=pk)
    hoy = timezone.localdate()
    if partido.fecha != hoy:
        messages.error(
            request,
            f"No puedes iniciar este partido. Está programado para el {partido.fecha.strftime('%d/%m/%Y')}, no para hoy ({hoy.strftime('%d/%m/%Y')})."
        )
        return redirect("tejobar_app:admin_partidos_index")
    if not partido.hora_inicio:
        partido.hora_inicio = timezone.now()
        partido.estado = Partido.ESTADO_CONFIRMADA
        partido.save()
        messages.success(request, "Cronómetro del partido iniciado.")
        _notificar_capitanes_partido(partido, f"¡El Partido #{partido.pk} ha comenzado!")
    else:
        messages.warning(request, "Este partido ya había iniciado.")
    return redirect("tejobar_app:admin_partidos_index")


@admin_required
def finalizar_partido(request: HttpRequest, pk: int) -> HttpResponse:
    partido = get_object_or_404(Partido, pk=pk)
    if partido.hora_inicio and not partido.hora_fin:
        now = timezone.now()
        partido.hora_fin = now
        partido.estado = Partido.ESTADO_FINALIZADO
        # Actualizar hora_reserva_fin a la hora real de fin para liberar la agenda
        partido.hora_reserva_fin = timezone.localtime(now).strftime("%H:%M")
        partido.save()
        messages.success(request, "Partido finalizado. Ya puede ver el total a pagar.")
        _notificar_capitanes_partido(partido, f"El Partido #{partido.pk} ha finalizado. Puedes revisar el total a pagar.")
    else:
        messages.warning(request, "No se puede finalizar este partido.")
    return redirect("tejobar_app:admin_partidos_index")


def _usuario_puede_pagar_partido(request: HttpRequest, partido: Partido) -> bool:
    persona = getattr(request.user, "persona", None)
    if not persona:
        return False
    if persona.rol == Persona.ROL_ADMIN:
        return True
    mi_equipo_rel = JugadorEquipo.objects.filter(jugador__persona=persona).first()
    if not mi_equipo_rel:
        return False
    equipo_id = mi_equipo_rel.equipo_id
    return partido.equipo1_id == equipo_id or partido.equipo2_id == equipo_id


def _partido_listo_para_pago(partido: Partido) -> bool:
    return bool(
        partido.hora_inicio
        and partido.hora_fin
        and not (partido.pago_cancha_equipo1 and partido.pago_cancha_equipo2)
        and partido.total_por_equipo >= 0
    )


def _registrar_pago_cancha_efectivo(partido: Partido, equipo_paga: str = "ambos", descripcion_extra: str = "") -> bool:
    if not _partido_listo_para_pago(partido):
        return False
        
    monto_pagado = 0
    procesado = False
    if equipo_paga in ["equipo1", "ambos"] and not partido.pago_cancha_equipo1:
        partido.pago_cancha_equipo1 = True
        monto_pagado += partido.gran_total_equipo1
        partido.consumos.filter(equipo=partido.equipo1, estado='pendiente').update(estado='comprado')
        procesado = True
        
    if equipo_paga in ["equipo2", "ambos"] and not partido.pago_cancha_equipo2:
        partido.pago_cancha_equipo2 = True
        monto_pagado += partido.gran_total_equipo2
        partido.consumos.filter(equipo=partido.equipo2, estado='pendiente').update(estado='comprado')
        procesado = True
        
    if not procesado:
        return False

    if partido.pago_cancha_equipo1 and partido.pago_cancha_equipo2:
        partido.pago_cancha = True
        partido.estado = Partido.ESTADO_FINALIZADO  # Forzar liberación

    partido.save()
    
    descripcion = f"Pago de cancha para el partido #{partido.pk} - Monto pagado: ${monto_pagado}"
    if descripcion_extra:
        descripcion = f"{descripcion} ({descripcion_extra})"
        
    Novedad.objects.create(
        producto=None,
        tipo_novedad=Novedad.TIPO_CANCHA,
        cantidad=1,
        descripcion=descripcion,
    )
    
    # Notificar a los capitanes involucrados
    from .models import Notificacion
    capitanes_notificados = []
    if equipo_paga in ["equipo1", "ambos"] and partido.equipo1 and partido.equipo1.capitan_nombre:
        # Aquí idealmente tendríamos el User del capitán, pero actualmente 'capitan_nombre' es char. 
        # Si el capitán tiene un User en Jugador.persona.usuario, podríamos usarlo.
        # Por simplificación y dada la estructura, buscaremos si existe un User con ese nombre
        pass # The current data model might not strictly link a team to a user account directly easily here, I'll attempt to find the user via JugadorEquipo.

    for e_id, e_paga_str in [(partido.equipo1_id, "equipo1"), (partido.equipo2_id, "equipo2")]:
        if equipo_paga in [e_paga_str, "ambos"]:
            from .models import JugadorEquipo, User
            # Buscar el capitán real de este equipo si tiene cuenta de usuario
            je = JugadorEquipo.objects.filter(equipo_id=e_id, es_capitan=True).select_related('jugador__persona__user').first()
            if je and je.jugador.persona.user:
                Notificacion.objects.create(
                    usuario=je.jugador.persona.user,
                    mensaje=f"El administrador confirmó el pago de ${partido.total_por_equipo:,.0f} por la cancha del Partido #{partido.pk}",
                    tipo=Notificacion.TIPO_PAGO,
                    enlace="/partidos/"
                )
                
    return True

@admin_required
def pagar_partido(request: HttpRequest, pk: int) -> HttpResponse:
    if request.method != "POST":
        return redirect("tejobar_app:admin_partidos_index")
    partido = get_object_or_404(Partido, pk=pk)

    equipo_paga = request.POST.get("equipo_paga", "ambos")
    notas = request.POST.get("notas", "").strip()

    # ── Procesar consumos enviados desde el modal ────────────────────────────
    producto_ids = request.POST.getlist("consumo_producto_id")
    cantidades   = request.POST.getlist("consumo_cantidad")
    asignados    = request.POST.getlist("consumo_asignado")  # jugador_equipo_id | "equipo1" | "equipo2" | "cancha"

    consumos_ok = []
    consumos_err = []
    for prod_id, cant_str, asignado in zip(producto_ids, cantidades, asignados):
        try:
            cantidad = int(cant_str or 0)
            if cantidad <= 0:
                continue
            producto = Producto.objects.get(pk=prod_id, activo=True)
            if producto.stock < cantidad:
                consumos_err.append(f"{producto.nombre}: stock insuficiente (hay {producto.stock})")
                continue

            # Determinar equipo y persona asignada
            equipo_obj = None
            jugador_equipo_obj = None
            if asignado == "equipo1":
                equipo_obj = partido.equipo1
            elif asignado == "equipo2":
                equipo_obj = partido.equipo2
            elif asignado == "cancha":
                # Sin equipo específico, se registra contra el partido sin equipo
                equipo_obj = None
            else:
                # Número: es un jugador_equipo_id
                try:
                    jugador_equipo_obj = JugadorEquipo.objects.get(pk=int(asignado))
                    equipo_obj = jugador_equipo_obj.equipo
                except (JugadorEquipo.DoesNotExist, ValueError):
                    consumos_err.append(f"Jugador no encontrado para {prod_id}")
                    continue

            apartado = Apartado.objects.create(
                persona=None,
                producto=producto,
                cantidad=cantidad,
                partido=partido,
                equipo=equipo_obj,
                estado=Apartado.ESTADO_COMPRADO,
            )
            from .models import MovimientoInventario
            MovimientoInventario.objects.create(
                producto=producto,
                tipo_movimiento=MovimientoInventario.TIPO_VENTA,
                cantidad=cantidad,
                motivo=f"Consumo partido #{partido.pk} - cobrado en caja",
                usuario=request.user,
            )
            consumos_ok.append(f"{cantidad}x {producto.nombre}")
        except (Producto.DoesNotExist, ValueError):
            consumos_err.append(f"Producto ID {prod_id} no válido")

    if consumos_ok:
        messages.info(request, f"Consumos registrados: {', '.join(consumos_ok)}.")
    for err in consumos_err:
        messages.warning(request, f"Error en consumo: {err}")

    # ── Registrar pago cancha ────────────────────────────────────────────────
    desc_extra = f"Efectivo en caja. Equipo(s): {equipo_paga}"
    if notas:
        desc_extra += f". Notas: {notas}"

    if _registrar_pago_cancha_efectivo(partido, equipo_paga=equipo_paga, descripcion_extra=desc_extra):
        messages.success(request, "Pago de cancha registrado exitosamente.")
    else:
        messages.error(request, "No se pudo registrar el pago. Puede que el equipo ya haya pagado o el partido no esté listo.")
    return redirect("tejobar_app:admin_partidos_index")


@admin_required
def admin_canchas_index(request: HttpRequest) -> HttpResponse:
    canchas = Cancha.objects.all().order_by('id')
    return render(request, "canchas/admin_index.html", {"canchas": canchas})


@admin_required
def admin_canchas_create(request: HttpRequest) -> HttpResponse:
    if request.method == "POST":
        form = CanchaForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, "Cancha registrada correctamente.")
            return redirect("tejobar_app:admin_canchas_index")
    else:
        form = CanchaForm()
    return render(request, "canchas/form.html", {"form": form})


@admin_required
def admin_canchas_update(request: HttpRequest, pk: int) -> HttpResponse:
    cancha = get_object_or_404(Cancha, pk=pk)
    if request.method == "POST":
        form = CanchaForm(request.POST, instance=cancha)
        if form.is_valid():
            form.save()
            messages.success(request, "Datos de cancha actualizados.")
            return redirect("tejobar_app:admin_canchas_index")
    else:
        form = CanchaForm(instance=cancha)
    return render(request, "canchas/form.html", {"form": form, "cancha": cancha})


@admin_required
def admin_canchas_delete(request: HttpRequest, pk: int) -> HttpResponse:
    cancha = get_object_or_404(Cancha, pk=pk)
    partidos_futuros = cancha.partidos_futuros_programados()

    if not cancha.can_be_deleted:
        if request.method == "POST":
            messages.error(
                request,
                "No se puede eliminar la cancha porque tiene partidos futuros programados.",
            )
            return redirect("tejobar_app:admin_canchas_index")
        return render(
            request,
            "canchas/confirm_delete.html",
            {
                "cancha": cancha,
                "puede_eliminar": False,
                "partidos_futuros": partidos_futuros,
            },
        )

    if request.method == "POST":
        cancha.delete()
        messages.success(request, "Cancha eliminada.")
        return redirect("tejobar_app:admin_canchas_index")
    return render(
        request,
        "canchas/confirm_delete.html",
        {"cancha": cancha, "puede_eliminar": True, "partidos_futuros": partidos_futuros},
    )


def _partido_es_proximo_o_en_curso(partido: Partido) -> bool:
    """Partido visible arriba: en curso o con fecha/hora aún por jugar."""
    if partido.esta_finalizado:
        return False
    if partido.hora_inicio:
        return True
    hoy = timezone.localdate()
    ahora = timezone.localtime().strftime("%H:%M")
    if partido.fecha > hoy:
        return True
    if partido.fecha == hoy:
        hora = (partido.hora or "00:00").strip().zfill(5)[:5]
        return hora >= ahora[:5]
    return False


def _partidos_publicos_separados():
    """Próximos (izq→der: el que sigue primero) y finalizados (más reciente primero)."""
    base = list(
        Partido.objects.select_related("equipo1", "equipo2", "cancha")
        .exclude(estado=Partido.ESTADO_CANCELADA)
    )
    proximos = [p for p in base if _partido_es_proximo_o_en_curso(p)]
    finalizados = [p for p in base if p.esta_finalizado]
    return (
        Partido.ordenar_por_fecha_hora(proximos, descendente=False),
        Partido.ordenar_por_fecha_hora(finalizados, descendente=True),
    )


def partido_list(request: HttpRequest) -> HttpResponse:
    partidos_proximos, partidos_finalizados = _partidos_publicos_separados()
    mi_equipo_id = None
    if request.user.is_authenticated:
        persona = getattr(request.user, "persona", None)
        if persona:
            mi_equipo_rel = JugadorEquipo.objects.filter(jugador__persona=persona).first()
            mi_equipo_id = mi_equipo_rel.equipo_id if mi_equipo_rel else None
    return render(
        request,
        "partidos/index.html",
        {
            "partidos_proximos": partidos_proximos,
            "partidos_finalizados": partidos_finalizados,
            "mi_equipo_id": mi_equipo_id,
        },
    )


from django.http import JsonResponse

@login_required
def api_disponibilidad_partido(request: HttpRequest) -> JsonResponse:
    fecha = request.GET.get('fecha')
    hora_inicio = request.GET.get('hora')
    hora_reserva_fin = request.GET.get('hora_reserva_fin')
    partido_id = request.GET.get('partido_id')

    if not fecha or not hora_inicio:
        return JsonResponse({"canchas_ocupadas": {}, "equipos_ocupados": {}})

    # Traer partidos del mismo día para evaluar empalme (excluyendo cancelados, finalizados y pagados)
    qs = Partido.objects.filter(fecha=fecha).exclude(estado__in=[Partido.ESTADO_CANCELADA, Partido.ESTADO_FINALIZADO]).exclude(pago_cancha=True)
    if partido_id:
        qs = qs.exclude(pk=partido_id)
        
    canchas_ocupadas = {}
    equipos_ocupados = {}

    for p in qs:
        start_b = p.hora
        if p.hora_reserva_fin:
            end_b = p.hora_reserva_fin
        else:
            try:
                h, m = map(int, start_b.split(':'))
                h = min(23, h + 2)
                end_b = f"{h:02d}:{m:02d}"
            except:
                end_b = "23:59"
        
        start_a = hora_inicio
        end_a = hora_reserva_fin if hora_reserva_fin else "23:59"

        if start_a < end_b and start_b < end_a:
            # Hay cruce, registrar motivo
            motivo = f"hasta las {end_b}"
            
            if p.cancha_id:
                canchas_ocupadas[p.cancha_id] = motivo
            if p.equipo1_id:
                equipos_ocupados[p.equipo1_id] = motivo
            if p.equipo2_id:
                equipos_ocupados[p.equipo2_id] = motivo

    return JsonResponse({
        "canchas_ocupadas": canchas_ocupadas,
        "equipos_ocupados": equipos_ocupados
    })


# ==========================================
# CATEGORIAS CRUD (Admin)
# ==========================================

@login_required
@admin_required
def admin_categorias_index(request):
    categorias = Categoria.objects.all()
    return render(request, "categorias/admin_index.html", {"categorias": categorias})

@login_required
@admin_required
def admin_categorias_create(request):
    if request.method == "POST":
        form = CategoriaForm(request.POST)
        if form.is_valid():
            try:
                form.save()
                messages.success(request, "Categoría creada con éxito.")
                return redirect("tejobar_app:admin_categorias_index")
            except Exception as e:
                messages.error(request, f"Error al guardar la categoría: {e}")
    else:
        form = CategoriaForm()

    return render(request, "categorias/form.html", {"form": form})

@login_required
@admin_required
def admin_categorias_update(request, pk):
    categoria = get_object_or_404(Categoria, pk=pk)

    if request.method == "POST":
        form = CategoriaForm(request.POST, instance=categoria)
        if form.is_valid():
            try:
                form.save()
                messages.success(request, "Categoría editada con éxito.")
                return redirect("tejobar_app:admin_categorias_index")
            except Exception as e:
                messages.error(request, f"Error al actualizar la categoría: {e}")
    else:
        form = CategoriaForm(instance=categoria)

    return render(request, "categorias/form.html", {"form": form, "categoria": categoria})

@login_required
@admin_required
def admin_categorias_delete(request, pk):
    categoria = get_object_or_404(Categoria, pk=pk)

    if request.method == "POST":
        try:
            categoria.delete()
            messages.success(request, "Categoría eliminada.")
        except Exception as e:
            messages.error(request, f"No se puede eliminar esta categoría porque tiene productos asociados: {e}")
        return redirect("tejobar_app:admin_categorias_index")

    return render(request, "categorias/confirm_delete.html", {"categoria": categoria})


# ==========================================
# MERCADOPAGO PAGOS
# ==========================================

@login_required
def crear_preferencia_apartado(request, pk):
    apartado = get_object_or_404(Apartado, pk=pk, persona=getattr(request.user, "persona", None), estado=Apartado.ESTADO_PENDIENTE)
    
    sdk = mercadopago.SDK(settings.MERCADOPAGO_ACCESS_TOKEN)
    
    precio_unitario = float(apartado.producto.precio)
    
    preference_data = {
        "items": [
            {
                "id": str(apartado.producto.pk),
                "title": f"Apartado: {apartado.producto.nombre}",
                "quantity": int(apartado.cantidad),
                "currency_id": "COP",
                "unit_price": precio_unitario
            }
        ],
        "back_urls": {
            "success": "https://tejobar-version-python-production.up.railway.app/pago-exitoso/",
            "failure": "https://tejobar-version-python-production.up.railway.app/pago-fallido/",
            "pending": "https://tejobar-version-python-production.up.railway.app/pago-pendiente/"
        },
        "auto_return": "approved",
        "external_reference": f"apartado_{apartado.pk}"
    }

    preference_response = sdk.preference().create(preference_data)
    
    if preference_response.get("status") in (200, 201) and "init_point" in preference_response.get("response", {}):
        return redirect(preference_response["response"]["init_point"])
    else:
        error_msg = preference_response.get("response", "Error desconocido")
        messages.error(request, f"Error MercadoPago: {error_msg}")
        return redirect("tejobar_app:dashboard")

@login_required
def pago_cancha_efectivo_jugador(request: HttpRequest, pk: int) -> HttpResponse:
    """Indica al jugador que complete el pago en caja; el admin confirma con pagar_partido."""
    if request.method != "POST":
        return redirect("tejobar_app:partidos_index")
    partido = get_object_or_404(Partido.objects.select_related("cancha", "equipo1", "equipo2"), pk=pk)
    if not _usuario_puede_pagar_partido(request, partido):
        messages.error(request, "No tienes permiso para gestionar el pago de este partido.")
        return redirect("tejobar_app:partidos_index")
    if not _partido_listo_para_pago(partido):
        messages.error(request, "Este partido no está listo para cobrar la cancha.")
        return redirect("tejobar_app:partidos_index")
    # Notificar a los administradores
    from .models import Notificacion
    capitan_nombre = getattr(request.user.persona, "nombre", request.user.username) if hasattr(request.user, "persona") else request.user.username
    Notificacion.objects.create(
        usuario=None, # Global para Admins
        mensaje=f"El capitán {capitan_nombre} avisa pago en caja del Partido #{partido.pk}",
        tipo=Notificacion.TIPO_PAGO,
        enlace=f"/partidos/" # Podría ser un link directo al partido
    )
    
    messages.info(
        request,
        f"Acércate a la caja del bar para pagar ${partido.total_cancha:,.0f} en efectivo "
        f"por la cancha «{partido.cancha}». El personal confirmará tu pago.",
    )
    return redirect("tejobar_app:partidos_index")


@login_required
def crear_preferencia_cancha(request, pk):
    partido = get_object_or_404(Partido.objects.select_related("cancha"), pk=pk)

    if not _usuario_puede_pagar_partido(request, partido):
        messages.error(request, "No tienes permiso para pagar la cancha de este partido.")
        return redirect("tejobar_app:partidos_index")

    if partido.pago_cancha:
        messages.warning(request, "Este partido ya está pagado.")
        return redirect("tejobar_app:partidos_index")

    if not _partido_listo_para_pago(partido):
        messages.warning(request, "El partido aún no está finalizado o no tiene costo de cancha.")
        return redirect("tejobar_app:partidos_index")

    # Determinar a qué equipo pertenece el jugador actual
    persona = getattr(request.user, "persona", None)
    equipo_del_jugador = None
    if persona:
        mi_equipo_rel = JugadorEquipo.objects.filter(jugador__persona=persona).first()
        if mi_equipo_rel:
            if partido.equipo1_id == mi_equipo_rel.equipo_id:
                equipo_del_jugador = "equipo1"
            elif partido.equipo2_id == mi_equipo_rel.equipo_id:
                equipo_del_jugador = "equipo2"
                
    if not equipo_del_jugador:
        messages.error(request, "No se pudo determinar a qué equipo perteneces para procesar tu pago.")
        return redirect("tejobar_app:partidos_index")
        
    # Verificar si su equipo ya pagó
    if (equipo_del_jugador == "equipo1" and partido.pago_cancha_equipo1) or \
       (equipo_del_jugador == "equipo2" and partido.pago_cancha_equipo2):
        messages.warning(request, "Tu equipo ya ha pagado su parte de la cancha.")
        return redirect("tejobar_app:partidos_index")

    monto_total = partido.total_por_equipo
    
    if monto_total <= 0:
        messages.warning(request, "No se puede pagar por MercadoPago porque el monto a cobrar es $0.")
        return redirect("tejobar_app:partidos_index")
        
    sdk = mercadopago.SDK(settings.MERCADOPAGO_ACCESS_TOKEN)
    
    preference_data = {
        "items": [
            {
                "id": f"{partido.pk}_{equipo_del_jugador}",
                "title": f"Cancha (Tu parte) - Partido #{partido.pk}",
                "quantity": 1,
                "currency_id": "COP",
                "unit_price": float(monto_total)
            }
        ],
        "back_urls": {
            "success": "https://tejobar-version-python-production.up.railway.app/pago-exitoso/",
            "failure": "https://tejobar-version-python-production.up.railway.app/pago-fallido/",
            "pending": "https://tejobar-version-python-production.up.railway.app/pago-pendiente/"
        },
        "auto_return": "approved",
        "external_reference": f"cancha_{partido.pk}_{equipo_del_jugador}"
    }

    preference_response = sdk.preference().create(preference_data)

    if preference_response.get("status") in (200, 201) and "init_point" in preference_response.get("response", {}):
        return redirect(preference_response["response"]["init_point"])

    error_msg = preference_response.get("response", "Error desconocido en MercadoPago")
    messages.error(request, f"Error al conectar con MercadoPago: {error_msg}")
    return redirect("tejobar_app:partidos_index")

@login_required
def pago_exitoso(request):
    payment_id = request.GET.get("payment_id")
    status = request.GET.get("status")
    external_reference = request.GET.get("external_reference")
    
    if status == "approved" and external_reference:
        if external_reference.startswith("apartado_"):
            apartado_id = external_reference.split("_")[1]
            apartado = get_object_or_404(Apartado, pk=apartado_id)
            if apartado.estado != Apartado.ESTADO_COMPRADO:
                apartado.estado = Apartado.ESTADO_COMPRADO
                apartado.save()
                
                Historial.objects.create(
                    persona=apartado.persona,
                    producto=apartado.producto,
                    cantidad=apartado.cantidad,
                    precio=apartado.producto.precio,
                    total=apartado.cantidad * apartado.producto.precio,
                    estado="por_entregar"
                )
                
                Novedad.objects.create(
                    producto=apartado.producto,
                    tipo_novedad=Novedad.TIPO_VENDIDO,
                    cantidad=apartado.cantidad,
                    descripcion=f"Pago exitoso MercadoPago (ID: {payment_id})"
                )
                messages.success(request, f"Pago de apartado exitoso. Se ha registrado en el sistema.")
                
        elif external_reference.startswith("carrito_"):
            persona_id = external_reference.split("_")[1]
            apartados_pendientes = Apartado.objects.filter(persona_id=persona_id, estado=Apartado.ESTADO_PENDIENTE)
            
            for apartado in apartados_pendientes:
                apartado.estado = Apartado.ESTADO_COMPRADO
                apartado.save()
                
                Historial.objects.create(
                    persona=apartado.persona,
                    producto=apartado.producto,
                    cantidad=apartado.cantidad,
                    precio=apartado.producto.precio,
                    total=apartado.cantidad * apartado.producto.precio,
                    estado="por_entregar"
                )
                
                Novedad.objects.create(
                    producto=apartado.producto,
                    tipo_novedad=Novedad.TIPO_VENDIDO,
                    cantidad=apartado.cantidad,
                    descripcion=f"Pago carrito MP (ID: {payment_id})"
                )
            
            messages.success(request, f"Pago de carrito exitoso. ¡Gracias por tu compra!")
                
        elif external_reference.startswith("cancha_"):
            parts = external_reference.split("_")
            partido_id = parts[1]
            equipo_paga = parts[2] if len(parts) > 2 else "ambos"
            
            partido = get_object_or_404(Partido, pk=partido_id)
            
            # Use the existing function to process the team's payment
            desc = f"MercadoPago (ID: {payment_id})"
            if _registrar_pago_cancha_efectivo(partido, equipo_paga=equipo_paga, descripcion_extra=desc):
                messages.success(request, f"Pago de cancha exitoso ({equipo_paga}). Se ha registrado en novedades.")
            else:
                messages.warning(request, "Pago verificado, pero el sistema indica que ya estaba pagado o hubo un error al registrarlo.")
    return redirect("tejobar_app:dashboard")

@login_required
def pago_fallido(request):
    messages.error(request, "El pago a través de MercadoPago ha fallado o fue cancelado.")
    return redirect("tejobar_app:dashboard")

@login_required
def pago_pendiente(request):
    messages.info(request, "El pago se encuentra pendiente. Te notificaremos cuando se apruebe.")
    return redirect("tejobar_app:dashboard")


@admin_required
def admin_apartado_pagar_efectivo(request: HttpRequest, pk: int) -> HttpResponse:
    if request.method == "POST":
        apartado = get_object_or_404(Apartado, pk=pk, estado=Apartado.ESTADO_PENDIENTE)
        
        apartado.estado = Apartado.ESTADO_COMPRADO
        apartado.save()
        
        Historial.objects.create(
            persona=apartado.persona,
            producto=apartado.producto,
            cantidad=apartado.cantidad,
            precio=apartado.producto.precio,
            total=apartado.cantidad * apartado.producto.precio,
            estado="por_entregar"
        )
        
        Novedad.objects.create(
            producto=apartado.producto,
            tipo_novedad=Novedad.TIPO_VENDIDO,
            cantidad=apartado.cantidad,
            descripcion=f"Pago en efectivo procesado por Admin: {request.user.username}"
        )
        messages.success(request, f"Pago en efectivo de {apartado.producto.nombre} registrado correctamente.")
        
    return redirect(request.META.get('HTTP_REFERER', 'tejobar_app:dashboard'))


@admin_required
def admin_apartado_cancelar(request: HttpRequest, pk: int) -> HttpResponse:
    if request.method == "POST":
        apartado = get_object_or_404(Apartado, pk=pk, estado=Apartado.ESTADO_PENDIENTE)
        
        # Restore stock
        apartado.producto.stock += apartado.cantidad
        apartado.producto.save()
        
        apartado.estado = Apartado.ESTADO_CANCELADO
        apartado.save()

        Novedad.objects.create(
            producto=apartado.producto,
            tipo_novedad=Novedad.TIPO_AGREGADO,
            cantidad=apartado.cantidad,
            descripcion=f"Cancelación administrativa y devolución de stock. Admin: {request.user.username}"
        )
        messages.success(request, f"Apartado cancelado. Se devolvieron {apartado.cantidad} cajas al inventario.")
        
    return redirect(request.META.get('HTTP_REFERER', 'tejobar_app:dashboard'))


@admin_required
def admin_despachar_pedido(request: HttpRequest, pk: int) -> HttpResponse:
    if request.method == "POST":
        historial = get_object_or_404(Historial, pk=pk, estado="por_entregar")
        historial.estado = "entregado"
        historial.save()
        messages.success(request, f"¡Pedido de {historial.producto.nombre} despachado con éxito!")
    return redirect(request.META.get('HTTP_REFERER', 'tejobar_app:dashboard'))


@login_required
def editar_item_carrito(request: HttpRequest, pk: int) -> HttpResponse:
    is_ajax = request.headers.get('X-Requested-With') == 'XMLHttpRequest'

    if request.method == "POST":
        persona = getattr(request.user, "persona", None)
        apartado = get_object_or_404(
            Apartado.objects.select_related("producto"),
            pk=pk,
            persona=persona,
            estado=Apartado.ESTADO_PENDIENTE,
        )
        try:
            nueva_cantidad = int(request.POST.get("cantidad", ""))
        except (TypeError, ValueError):
            if is_ajax:
                return JsonResponse({'success': False, 'error': 'Ingresa una cantidad válida.'}, status=400)
            messages.error(request, "Ingresa una cantidad válida.")
            return redirect("tejobar_app:dashboard")

        max_cantidad = apartado.cantidad + apartado.producto.stock

        if nueva_cantidad < 1:
            if is_ajax:
                return JsonResponse({'success': False, 'error': 'La cantidad mínima es 1.'}, status=400)
            messages.error(request, "La cantidad mínima es 1.")
            return redirect("tejobar_app:dashboard")

        if nueva_cantidad > max_cantidad:
            msg = f"La cantidad no puede superar el stock disponible ({max_cantidad} unidades)."
            if is_ajax:
                return JsonResponse({'success': False, 'error': msg}, status=400)
            messages.error(request, msg)
            return redirect("tejobar_app:dashboard")

        diferencia = nueva_cantidad - apartado.cantidad
        if diferencia > 0 and apartado.producto.stock < diferencia:
            msg = f"Stock insuficiente. Disponible adicional: {apartado.producto.stock}"
            if is_ajax:
                return JsonResponse({'success': False, 'error': msg}, status=400)
            messages.error(request, msg)
            return redirect("tejobar_app:dashboard")

        apartado.producto.stock -= diferencia
        apartado.producto.save()
        apartado.cantidad = nueva_cantidad
        apartado.save()

        if is_ajax:
            subtotal = float(apartado.producto.precio) * nueva_cantidad
            # Recalculate cart total
            apartados_pendientes = Apartado.objects.filter(persona=persona, estado=Apartado.ESTADO_PENDIENTE).select_related('producto')
            total_carrito = sum(float(a.producto.precio) * a.cantidad for a in apartados_pendientes)
            return JsonResponse({
                'success': True,
                'nueva_cantidad': nueva_cantidad,
                'subtotal': f"{subtotal:,.0f}",
                'total_carrito': f"{total_carrito:,.0f}",
            })

        messages.success(request, "Apartado actualizado correctamente.")

    return redirect("tejobar_app:dashboard")


@login_required
def eliminar_item_carrito(request: HttpRequest, pk: int) -> HttpResponse:
    is_ajax = request.headers.get('X-Requested-With') == 'XMLHttpRequest'

    if request.method == "POST":
        persona = getattr(request.user, "persona", None)
        apartado = get_object_or_404(Apartado, pk=pk, persona=persona, estado=Apartado.ESTADO_PENDIENTE)

        # Restore stock
        apartado.producto.stock += apartado.cantidad
        apartado.producto.save()
        apartado.delete()

        if is_ajax:
            apartados_pendientes = Apartado.objects.filter(persona=persona, estado=Apartado.ESTADO_PENDIENTE).select_related('producto')
            total_carrito = sum(float(a.producto.precio) * a.cantidad for a in apartados_pendientes)
            return JsonResponse({
                'success': True,
                'total_carrito': f"{total_carrito:,.0f}",
                'num_items': apartados_pendientes.count(),
            })

        messages.success(request, "Producto eliminado del carrito.")

    return redirect("tejobar_app:dashboard")


@login_required
def carrito_resumen(request: HttpRequest) -> JsonResponse:
    """API JSON: retorna el contenido completo del carrito del usuario autenticado."""
    persona = getattr(request.user, "persona", None)
    if not persona:
        return JsonResponse({'success': False, 'items': [], 'total': '0', 'num_items': 0})

    apartados = Apartado.objects.filter(
        persona=persona, estado=Apartado.ESTADO_PENDIENTE
    ).select_related('producto', 'producto__categoria')

    items = []
    total = 0.0
    for ap in apartados:
        precio = float(ap.producto.precio)
        subtotal = precio * ap.cantidad
        total += subtotal
        items.append({
            'id': ap.pk,
            'nombre': ap.producto.nombre,
            'precio_unitario': f"{precio:,.0f}",
            'cantidad': ap.cantidad,
            'subtotal': f"{subtotal:,.0f}",
            'imagen_url': ap.producto.imagen_url,
            'max_cantidad': ap.cantidad + ap.producto.stock,
            'editar_url': f"/carrito/editar/{ap.pk}/",
            'eliminar_url': f"/carrito/eliminar/{ap.pk}/",
        })

    return JsonResponse({
        'success': True,
        'items': items,
        'total': f"{total:,.0f}",
        'num_items': len(items),
    })


@login_required
@admin_required
def admin_carga_masiva(request: HttpRequest) -> HttpResponse:
    from tejobar_app.forms import CargaMasivaProductosForm
    from tejobar_app.services import procesar_archivo_productos
    
    if request.method == 'POST':
        form = CargaMasivaProductosForm(request.POST, request.FILES)
        if form.is_valid():
            archivo = request.FILES['archivo']
            resumen = procesar_archivo_productos(archivo)
            
            if resumen['creados'] > 0:
                messages.success(request, f"{resumen['creados']} productos creados exitosamente.")
            if resumen['actualizados'] > 0:
                messages.info(request, f"{resumen['actualizados']} productos actualizados.")
            
            for error in resumen['errores']:
                messages.error(request, error)
                
            return redirect('tejobar_app:admin_productos_index')
    else:
        form = CargaMasivaProductosForm()

    return render(request, 'productos/carga_masiva.html', {'form': form})


from .models import MovimientoInventario
from .forms import MovimientoIngresoForm, MovimientoPerdidaForm

@login_required
@admin_required
def inventario_movimientos(request: HttpRequest) -> HttpResponse:
    movimientos = MovimientoInventario.objects.select_related("producto", "usuario").order_by("-fecha")
    fecha_inicio = request.GET.get("fecha_inicio")
    fecha_fin = request.GET.get("fecha_fin")
    tipo = request.GET.get("tipo")
    
    if fecha_inicio:
        movimientos = movimientos.filter(fecha__gte=fecha_inicio)
    if fecha_fin:
        from datetime import datetime, time
        from django.utils import timezone
        try:
            fin_dt = datetime.strptime(fecha_fin, "%Y-%m-%d").date()
            dt_end = timezone.make_aware(datetime.combine(fin_dt, time.max))
            movimientos = movimientos.filter(fecha__lte=dt_end)
        except ValueError:
            pass
            
    if tipo:
        movimientos = movimientos.filter(tipo_movimiento=tipo)

    context = {
        "movimientos": movimientos,
        "fecha_inicio": fecha_inicio or "",
        "fecha_fin": fecha_fin or "",
        "tipo": tipo or "",
    }
    return render(request, "inventario/movimientos.html", context)


@login_required
@admin_required
def inventario_ingreso(request: HttpRequest) -> HttpResponse:
    from django.db import transaction
    productos = Producto.objects.all().select_related("categoria").order_by("categoria__nombre", "nombre")
    error = None
    success = None

    if request.method == "POST":
        producto_ids = request.POST.getlist("producto_id[]")
        cantidades = request.POST.getlist("cantidad[]")
        origen = request.POST.get("origen", "").strip()
        detalle = request.POST.get("detalle", "").strip()

        if not producto_ids or not cantidades or len(producto_ids) != len(cantidades):
            messages.error(request, "No agregaste ningún producto a la lista de ingresos.")
        elif not origen:
            messages.error(request, "Debes especificar el origen del ingreso.")
        else:
            try:
                with transaction.atomic():
                    total_productos = 0
                    for pid, cant_str in zip(producto_ids, cantidades):
                        cantidad = int(cant_str)
                        if cantidad <= 0: raise ValueError("Cantidad inválida.")
                        producto = get_object_or_404(Producto.objects.select_for_update(), pk=pid)
                        
                        mov = MovimientoInventario.objects.create(
                            producto=producto,
                            tipo_movimiento=MovimientoInventario.TIPO_INGRESO,
                            cantidad=cantidad,
                            origen=origen,
                            detalle=detalle,
                            usuario=request.user
                        )
                        # Para compatibilidad con vistas que usen 'motivo'
                        texto_motivo = mov.get_origen_display()
                        if detalle:
                            texto_motivo += f" - {detalle}"
                        mov.motivo = texto_motivo
                        mov.save()

                        Novedad.objects.create(
                            producto=producto,
                            tipo_novedad=Novedad.TIPO_AGREGADO,
                            cantidad=cantidad,
                            descripcion=texto_motivo
                        )
                        total_productos += cantidad
                    
                    messages.success(request, f"Se sumaron {total_productos} unidades correctamente.")
                    return redirect("tejobar_app:inventario_movimientos")
            except ValueError as e:
                messages.error(request, str(e))
            except Exception as e:
                messages.error(request, "Ocurrió un error al procesar el ingreso.")

    return render(request, "inventario/ingreso_form.html", {
        "productos": productos,
    })


@login_required
@admin_required
def inventario_perdida(request: HttpRequest) -> HttpResponse:
    from django.db import transaction
    productos = Producto.objects.filter(stock__gt=0).select_related("categoria").order_by("categoria__nombre", "nombre")
    error = None
    success = None

    if request.method == "POST":
        producto_ids = request.POST.getlist("producto_id[]")
        cantidades = request.POST.getlist("cantidad[]")
        motivo = request.POST.get("motivo", "").strip()

        if not producto_ids or not cantidades or len(producto_ids) != len(cantidades):
            messages.error(request, "No agregaste ningún producto a la lista de pérdidas.")
        elif not motivo:
            messages.error(request, "Debes especificar un motivo general para la pérdida.")
        else:
            try:
                with transaction.atomic():
                    total_productos = 0
                    for pid, cant_str in zip(producto_ids, cantidades):
                        cantidad = int(cant_str)
                        if cantidad <= 0: raise ValueError("Cantidad inválida.")
                        producto = get_object_or_404(Producto.objects.select_for_update(), pk=pid)
                        
                        if producto.stock < cantidad:
                            raise ValueError(f"Stock insuficiente para {producto.nombre}. Disponible: {producto.stock} unidades.")

                        MovimientoInventario.objects.create(
                            producto=producto,
                            tipo_movimiento=MovimientoInventario.TIPO_PERDIDA,
                            cantidad=cantidad,
                            motivo=motivo,
                            usuario=request.user
                        )
                        Novedad.objects.create(
                            producto=producto,
                            tipo_novedad=Novedad.TIPO_PERDIDA,
                            cantidad=cantidad,
                            descripcion=f"Pérdida registrada: {motivo}"
                        )
                        total_productos += cantidad
                    
                    messages.success(request, f"Se registró la pérdida de {total_productos} unidades.")
                    return redirect("tejobar_app:inventario_movimientos")
            except ValueError as e:
                messages.error(request, str(e))
            except Exception as e:
                messages.error(request, "Ocurrió un error al procesar la pérdida.")

    return render(request, "inventario/perdida_form.html", {
        "productos": productos,
    })

@login_required
def historial_equipo_jugador(request: HttpRequest) -> HttpResponse:
    persona = getattr(request.user, "persona", None)
    if not persona or not hasattr(persona, "jugador"):
        messages.error(request, "Perfil de jugador no encontrado.")
        return redirect("tejobar_app:dashboard")

    from .models import HistorialEquipo
    historial = HistorialEquipo.objects.filter(jugador=persona.jugador).select_related('equipo').order_by('-fecha_ingreso')
    
    return render(request, "equipos/historial_jugador.html", {
        "usuario": persona,
        "rol": persona.rol,
        "historial": historial
    })

@admin_required
def historial_equipo_admin(request: HttpRequest) -> HttpResponse:
    from .models import HistorialEquipo, Persona
    from django.db.models import Q
    
    q_jugador = request.GET.get("q_jugador", "")
    q_equipo = request.GET.get("q_equipo", "")

    historial = HistorialEquipo.objects.select_related('jugador__persona', 'equipo').order_by('-fecha_ingreso')

    if q_jugador:
        historial = historial.filter(jugador__persona__nombre__icontains=q_jugador)
    if q_equipo:
        historial = historial.filter(equipo__nombre_equipo__icontains=q_equipo)

    persona = getattr(request.user, "persona", None)
    
    context = {
        "usuario": persona,
        "rol": Persona.ROL_ADMIN,
        "historial": historial,
        "q_jugador": q_jugador,
        "q_equipo": q_equipo
    }
    return render(request, "equipos/historial_admin.html", context)


@login_required
def equipo_reinvite_member(request: HttpRequest, pk: int, jugador_pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)
    
    es_capitan = False
    if persona and hasattr(persona, "jugador"):
        es_capitan = JugadorEquipo.objects.filter(jugador=persona.jugador, equipo=equipo, es_capitan=True).exists()

    if not persona or (persona.rol != Persona.ROL_ADMIN and not es_capitan):
        messages.error(request, "No tienes permiso para invitar jugadores a este equipo.")
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)
        
    if equipo.equipo_jugadores.count() >= 5:
        messages.error(request, "El equipo está lleno (límite 5 jugadores).")
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)

    if request.method == "POST":
        jugador_a_invitar = get_object_or_404(Jugador, persona__pk=jugador_pk)
        
        # Verificar si ya pertenece a otro equipo
        if JugadorEquipo.objects.filter(jugador=jugador_a_invitar).exists():
            messages.error(request, f"El jugador {jugador_a_invitar.persona.nombre} ya pertenece a otro equipo. Debe salir de su equipo actual antes de ser invitado.")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)
            
        try:
            JugadorEquipo.objects.create(
                jugador=jugador_a_invitar,
                equipo=equipo,
                es_capitan=False,
                tipo_usuario=JugadorEquipo.TIPO_REGISTRADO
            )
            messages.success(request, f"¡{jugador_a_invitar.persona.nombre} ha sido reincorporado al equipo!")
        except Exception as e:
            messages.error(request, f"Error al reincorporar al jugador: {str(e)}")
            
    return redirect("tejobar_app:equipos_show", pk=equipo.pk)

def obtener_metricas_reporte(dt_start=None, dt_end=None, categoria_id=None):
    from django.db.models import Sum, Q
    from django.db.models.functions import TruncMonth
    from datetime import date, timedelta
    import datetime
    from django.utils import timezone
    from .models import Producto, MovimientoInventario, Apartado, Categoria, Equipo, Jugador, Partido, Cancha
    from django.contrib.auth.models import User

    # 1. Base querysets with date filter if applicable
    movimientos_base = MovimientoInventario.objects.all()
    if dt_start:
        movimientos_base = movimientos_base.filter(fecha__gte=dt_start)
    if dt_end:
        movimientos_base = movimientos_base.filter(fecha__lte=dt_end)
    if categoria_id:
        movimientos_base = movimientos_base.filter(producto__categoria_id=categoria_id)

    apartados_base = Apartado.objects.all()
    if dt_start:
        apartados_base = apartados_base.filter(fecha_apartado__gte=dt_start)
    if dt_end:
        apartados_base = apartados_base.filter(fecha_apartado__lte=dt_end)
    if categoria_id:
        apartados_base = apartados_base.filter(producto__categoria_id=categoria_id)

    # 2. General metrics
    total_ventas_cant = movimientos_base.filter(tipo_movimiento=MovimientoInventario.TIPO_VENTA).aggregate(Sum('cantidad'))['cantidad__sum'] or 0
    total_perdidas_cant = movimientos_base.filter(tipo_movimiento=MovimientoInventario.TIPO_PERDIDA).aggregate(Sum('cantidad'))['cantidad__sum'] or 0
    total_ingresos_cant = movimientos_base.filter(tipo_movimiento=MovimientoInventario.TIPO_INGRESO).aggregate(Sum('cantidad'))['cantidad__sum'] or 0
    
    total_movimientos = total_ventas_cant + total_perdidas_cant
    tasa_perdida_global = round((total_perdidas_cant / total_movimientos * 100), 1) if total_movimientos > 0 else 0

    # Physical vs Online sales
    ventas_fisicas_qs = movimientos_base.filter(
        tipo_movimiento=MovimientoInventario.TIPO_VENTA
    ).filter(
        Q(motivo__icontains="Venta Directa") | Q(motivo__icontains="POS")
    )
    total_ventas_fisicas_cant = ventas_fisicas_qs.aggregate(Sum('cantidad'))['cantidad__sum'] or 0
    total_ventas_fisicas_monto = sum(item.cantidad * item.producto.precio for item in ventas_fisicas_qs.select_related('producto'))

    ventas_online_qs = movimientos_base.filter(
        tipo_movimiento=MovimientoInventario.TIPO_VENTA
    ).filter(
        motivo__icontains="Apartado online"
    )
    total_ventas_online_cant = ventas_online_qs.aggregate(Sum('cantidad'))['cantidad__sum'] or 0
    total_ventas_online_monto = sum(item.cantidad * item.producto.precio for item in ventas_online_qs.select_related('producto'))

    # 3. Product performance ranking
    productos_ranking = []
    productos = Producto.objects.all()
    if categoria_id:
        productos = productos.filter(categoria_id=categoria_id)
        
    for p in productos:
        p_ventas = movimientos_base.filter(producto=p, tipo_movimiento=MovimientoInventario.TIPO_VENTA).aggregate(Sum('cantidad'))['cantidad__sum'] or 0
        p_perdidas = movimientos_base.filter(producto=p, tipo_movimiento=MovimientoInventario.TIPO_PERDIDA).aggregate(Sum('cantidad'))['cantidad__sum'] or 0
        
        p_total = p_ventas + p_perdidas
        p_tasa_perdida = round((p_perdidas / p_total * 100), 1) if p_total > 0 else 0
        
        # Monthly comparison: current vs previous month
        hoy = timezone.now().date()
        mes_actual = hoy.month
        ano_actual = hoy.year
        
        primer_dia_actual = date(ano_actual, mes_actual, 1)
        if mes_actual == 1:
            primer_dia_prev = date(ano_actual - 1, 12, 1)
            ultimo_dia_prev = date(ano_actual, 1, 1) - timedelta(days=1)
        else:
            primer_dia_prev = date(ano_actual, mes_actual - 1, 1)
            ultimo_dia_prev = primer_dia_actual - timedelta(days=1)
            
        sales_actual = p.movimientos.filter(
            tipo_movimiento=MovimientoInventario.TIPO_VENTA,
            fecha__date__gte=primer_dia_actual
        ).aggregate(Sum('cantidad'))['cantidad__sum'] or 0
        
        sales_prev = p.movimientos.filter(
            tipo_movimiento=MovimientoInventario.TIPO_VENTA,
            fecha__date__gte=primer_dia_prev,
            fecha__date__lte=ultimo_dia_prev
        ).aggregate(Sum('cantidad'))['cantidad__sum'] or 0
        
        diferencia = sales_actual - sales_prev
        
        productos_ranking.append({
            'id': p.id,
            'nombre': p.nombre,
            'precio': float(p.precio),
            'stock': p.stock,
            'ventas': p_ventas,
            'perdidas': p_perdidas,
            'tasa_perdida': p_tasa_perdida,
            'sales_actual': sales_actual,
            'sales_prev': sales_prev,
            'diferencia': diferencia,
            'crecio': sales_actual > sales_prev
        })

    # Sort rankings
    productos_mas_vendidos = sorted([p for p in productos_ranking if p['ventas'] > 0], key=lambda x: x['ventas'], reverse=True)
    productos_menos_vendidos = sorted(productos_ranking, key=lambda x: x['ventas'])
    
    productos_mas_perdidos = sorted([p for p in productos_ranking if p['perdidas'] > 0], key=lambda x: x['perdidas'], reverse=True)
    producto_mas_perdido = productos_mas_perdidos[0] if productos_mas_perdidos else None

    # 4. Monthly sales trend (last 6 months)
    MESES = {
        1: "Ene", 2: "Feb", 3: "Mar", 4: "Abr", 5: "May", 6: "Jun",
        7: "Jul", 8: "Ago", 9: "Sep", 10: "Oct", 11: "Nov", 12: "Dic"
    }
    
    ventas_qs = movimientos_base.filter(
        tipo_movimiento=MovimientoInventario.TIPO_VENTA
    ).values('fecha', 'cantidad')
    
    monthly_sales = {}
    for v in ventas_qs:
        if v['fecha']:
            key = (v['fecha'].year, v['fecha'].month)
            monthly_sales[key] = monthly_sales.get(key, 0) + (v['cantidad'] or 0)
            
    ventas_mensuales_formateadas = []
    for key in sorted(monthly_sales.keys()):
        y_num, m_num = key
        ventas_mensuales_formateadas.append({
            'label': f"{MESES[m_num]} {y_num}",
            'cantidad': monthly_sales[key]
        })
            
    if not ventas_mensuales_formateadas:
        hoy = timezone.now().date()
        ventas_mensuales_formateadas.append({
            'label': f"{MESES[hoy.month]} {hoy.year}",
            'cantidad': 0
        })

    return {
        'total_ventas_cant': total_ventas_cant,
        'total_perdidas_cant': total_perdidas_cant,
        'total_ingresos_cant': total_ingresos_cant,
        'tasa_perdida_global': tasa_perdida_global,
        'total_ventas_fisicas_cant': total_ventas_fisicas_cant,
        'total_ventas_fisicas_monto': total_ventas_fisicas_monto,
        'total_ventas_online_cant': total_ventas_online_cant,
        'total_ventas_online_monto': total_ventas_online_monto,
        'productos_mas_vendidos': productos_mas_vendidos[:10],
        'productos_menos_vendidos': productos_menos_vendidos[:10],
        'producto_mas_perdido': producto_mas_perdido,
        'productos_perdidas': productos_mas_perdidos[:10],
        'ventas_mensuales': ventas_mensuales_formateadas[-6:],
        'productos_todos': productos_ranking
    }

@login_required
@admin_required
def generar_reportes(request: HttpRequest) -> HttpResponse:
    from .utils import generar_pdf
    from datetime import datetime, time
    from django.utils import timezone
    
    fecha_inicio = request.GET.get("fecha_inicio") or request.POST.get("fecha_inicio")
    fecha_fin = request.GET.get("fecha_fin") or request.POST.get("fecha_fin")
    categoria_id = request.GET.get("categoria") or request.POST.get("categoria")
    
    dt_start = None
    dt_end = None
    if fecha_inicio:
        try:
            ini_dt = datetime.strptime(fecha_inicio, "%Y-%m-%d").date()
            dt_start = timezone.make_aware(datetime.combine(ini_dt, time.min))
        except ValueError:
            pass
    if fecha_fin:
        try:
            fin_dt = datetime.strptime(fecha_fin, "%Y-%m-%d").date()
            dt_end = timezone.make_aware(datetime.combine(fin_dt, time.max))
        except ValueError:
            pass
            
    categorias = Categoria.objects.all()
    if request.method == "POST":
        tipo = request.POST.get("tipo")
        modulo = request.POST.get("modulo")
        
        persona = getattr(request.user, "persona", None)
        generado_por = persona.nombre if persona else "Administrador"

        base_context = {
            "total_productos": Producto.objects.count(),
            "productos_bajo_stock": Producto.objects.filter(stock__lt=10).count(),
            "total_categorias": Categoria.objects.count(),
            "total_equipos": Equipo.objects.count(),
            "total_jugadores": Jugador.objects.count(),
            "total_partidos": Partido.objects.count(),
            "total_canchas": Cancha.objects.count(),
            "total_usuarios": User.objects.filter(is_active=True).count(),
            "fecha_inicio": fecha_inicio or "",
            "fecha_fin": fecha_fin or "",
            "generado_por": generado_por,
        }
        metrics = obtener_metricas_reporte(dt_start, dt_end, categoria_id)
        base_context.update(metrics)

        if tipo == "General":
            apartados = Apartado.objects.select_related("persona", "producto").order_by("-fecha_apartado").all()
            if dt_start:
                apartados = apartados.filter(fecha_apartado__gte=dt_start)
            if dt_end:
                apartados = apartados.filter(fecha_apartado__lte=dt_end)
            
            context = base_context.copy()
            context["tipo_reporte"] = "General"
            context["apartados"] = apartados
            
            return generar_pdf(context, "dashboard/reporte_pdf.html", "reporte_general")

        elif tipo == "Especifico":
            context = {
                "tipo_reporte": "Especifico",
                "modulo_nombre": modulo,
                "fecha_inicio": fecha_inicio or "",
                "fecha_fin": fecha_fin or "",
                "generado_por": generado_por,
            }
            datos = []
            columnas = []
            
            if modulo == "Productos":
                columnas = ["ID", "Nombre", "Precio", "Stock", "Vencimiento"]
                qs = Producto.objects.all().order_by("nombre")
                if categoria_id:
                    qs = qs.filter(categoria_id=categoria_id)
                for item in qs:
                    datos.append([item.pk, item.nombre, f"${item.precio}", item.stock, item.fecha_vencimiento or "N/A"])
                    
            elif modulo == "Categorias":
                columnas = ["ID", "Nombre", "Estado"]
                qs = Categoria.objects.all().order_by("nombre")
                for item in qs:
                    datos.append([item.pk, item.nombre, "Activa" if item.estado else "Inactiva"])
                    
            elif modulo in ["Ventas", "Perdidas"]:
                from .models import MovimientoInventario
                columnas = ["Fecha", "Producto", "Cant", "Motivo", "Usuario"]
                t_mov = MovimientoInventario.TIPO_VENTA if modulo == "Ventas" else MovimientoInventario.TIPO_PERDIDA
                qs = MovimientoInventario.objects.filter(tipo_movimiento=t_mov).order_by("-fecha")
                if dt_start: qs = qs.filter(fecha__gte=dt_start)
                if dt_end: qs = qs.filter(fecha__lte=dt_end)
                if categoria_id:
                    qs = qs.filter(producto__categoria_id=categoria_id)
                for item in qs:
                    u_nombre = item.usuario.username if item.usuario else "Sistema"
                    datos.append([item.fecha.strftime('%Y-%m-%d %H:%M'), item.producto.nombre, item.cantidad, item.motivo, u_nombre])

            elif modulo == "Novedades":
                from .models import Novedad
                columnas = ["Fecha", "Tipo", "Movimiento", "Producto", "Cantidad", "Descripción"]
                qs = Novedad.objects.select_related("producto").order_by("-fecha")
                if dt_start: qs = qs.filter(fecha__gte=dt_start)
                if dt_end: qs = qs.filter(fecha__lte=dt_end)
                if categoria_id:
                    qs = qs.filter(producto__categoria_id=categoria_id)
                for item in qs:
                    prod_nombre = item.producto.nombre if item.producto else "N/A"
                    datos.append([
                        item.fecha.strftime('%Y-%m-%d %H:%M'),
                        item.get_tipo_novedad_display(),
                        item.movimiento,
                        prod_nombre,
                        item.cantidad,
                        item.descripcion or "—"
                    ])

            elif modulo == "Partidos":
                columnas = ["Fecha", "Hora", "Equipos", "Cancha", "Estado"]
                qs = Partido.objects.all().order_by("-fecha", "-hora")
                if dt_start: qs = qs.filter(fecha__gte=dt_start)
                if dt_end: qs = qs.filter(fecha__lte=dt_end)
                for item in qs:
                    eqs = f"{item.equipo1.nombre_equipo} vs {item.equipo2.nombre_equipo}"
                    f_str = item.fecha.strftime('%Y-%m-%d') if hasattr(item.fecha, 'strftime') else str(item.fecha)
                    h_str = item.hora.strftime('%H:%M') if hasattr(item.hora, 'strftime') else str(item.hora)
                    cancha_name = str(item.cancha) if item.cancha else "N/A"
                    datos.append([f_str, h_str, eqs, cancha_name, item.get_estado_display()])

            elif modulo == "Canchas":
                columnas = ["ID", "Descripción", "Estado", "Precio/Hora"]
                qs = Cancha.objects.all().order_by("id")
                for item in qs:
                    datos.append([item.pk, str(item), "Habilitada" if item.estado else "Deshabilitada", f"${item.precio_por_hora}"])
            
            else:
                columnas = ["Aviso"]
                datos = [["Módulo en desarrollo o no soportado."]]
                
            context["columnas"] = columnas
            context["datos"] = datos
            return generar_pdf(context, "dashboard/reporte_pdf.html", f"reporte_{modulo.lower()}")


    metrics = obtener_metricas_reporte(dt_start, dt_end, categoria_id)
    return render(request, "dashboard/generar_reportes.html", {
        "metrics": metrics,
        "fecha_inicio": fecha_inicio or "",
        "fecha_fin": fecha_fin or "",
        "categorias": categorias,
        "categoria_seleccionada": int(categoria_id) if categoria_id and categoria_id.isdigit() else ""
    })


@login_required
@require_POST
def descartar_notificacion(request, notif_id):
    dismissed = request.session.get('dismissed_notifications', [])
    print("SESSION DISMISSED NOTIFS IN VIEW BEFORE:", dismissed)
    if notif_id not in dismissed:
        # Convert to list if it was something else, just to be safe
        if not isinstance(dismissed, list):
            dismissed = list(dismissed)
        dismissed.append(notif_id)
        request.session['dismissed_notifications'] = dismissed
        request.session.modified = True
    print("SESSION DISMISSED NOTIFS IN VIEW AFTER:", request.session.get('dismissed_notifications'))
    return JsonResponse({"success": True})

@login_required
@require_POST
def marcar_notificaciones_leidas(request):
    """Marca como leídas las notificaciones del usuario actual.
    Si es admin y no tiene un objeto Persona asociado que restrinja, 
    marcará las notificaciones generales (usuario=None)."""
    from .models import Notificacion
    
    if request.user.is_superuser or getattr(request.user, "persona", None) and request.user.persona.rol == 'admin':
        # Admin: mark general notifications as read
        Notificacion.objects.filter(usuario=None, leida=False).update(leida=True)
        # Also mark their own personal notifications as read if they have any
        Notificacion.objects.filter(usuario=request.user, leida=False).update(leida=True)
    else:
        # Player: mark only their notifications as read
        Notificacion.objects.filter(usuario=request.user, leida=False).update(leida=True)
        
    return JsonResponse({"success": True})


# ─── Novedades de Jugadores por Partido ─────────────────────────────────────

@admin_required
def admin_partido_novedades(request: HttpRequest, pk: int) -> HttpResponse:
    """Vista principal que lista las novedades de un partido y muestra el formulario para agregar."""
    partido = get_object_or_404(Partido, pk=pk)
    novedades = partido.novedades_jugadores.select_related("jugador_equipo__equipo", "jugador_equipo__jugador__persona", "registrado_por").order_by("fecha_registro")

    # Construir lista de jugadores de ambos equipos para el selector
    jugadores_equipo1 = []
    jugadores_equipo2 = []
    if partido.equipo1:
        jugadores_equipo1 = JugadorEquipo.objects.filter(equipo=partido.equipo1).select_related("jugador__persona")
    if partido.equipo2:
        jugadores_equipo2 = JugadorEquipo.objects.filter(equipo=partido.equipo2).select_related("jugador__persona")

    context = {
        "partido": partido,
        "novedades": novedades,
        "jugadores_equipo1": jugadores_equipo1,
        "jugadores_equipo2": jugadores_equipo2,
        "tipos_novedad": NovedadJugador.TIPO_CHOICES,
    }
    return render(request, "partidos/novedades_jugadores.html", context)


@admin_required
@require_POST
def admin_partido_novedad_crear(request: HttpRequest, pk: int) -> HttpResponse:
    """Crea una nueva novedad para un jugador en un partido."""
    partido = get_object_or_404(Partido, pk=pk)

    jugador_equipo_id = request.POST.get("jugador_equipo_id")
    nombre_libre = request.POST.get("nombre_jugador_libre", "").strip()
    tipo_novedad = request.POST.get("tipo_novedad")
    descripcion = request.POST.get("descripcion", "").strip()

    if not tipo_novedad:
        messages.error(request, "El tipo de novedad es requerido.")
        return redirect("tejobar_app:admin_partido_novedades", pk=pk)

    jugador_equipo = None
    if jugador_equipo_id:
        try:
            jugador_equipo = JugadorEquipo.objects.get(pk=jugador_equipo_id)
        except JugadorEquipo.DoesNotExist:
            messages.error(request, "Jugador no encontrado.")
            return redirect("tejobar_app:admin_partido_novedades", pk=pk)

    if not jugador_equipo and not nombre_libre:
        messages.error(request, "Debes seleccionar un jugador o ingresar un nombre.")
        return redirect("tejobar_app:admin_partido_novedades", pk=pk)

    NovedadJugador.objects.create(
        partido=partido,
        jugador_equipo=jugador_equipo,
        nombre_jugador_libre=nombre_libre if not jugador_equipo else None,
        tipo_novedad=tipo_novedad,
        descripcion=descripcion or None,
        registrado_por=request.user,
    )
    messages.success(request, "Novedad registrada correctamente.")
    return redirect("tejobar_app:admin_partido_novedades", pk=pk)


@admin_required
@require_POST
def admin_partido_novedad_eliminar(request: HttpRequest, novedad_pk: int) -> HttpResponse:
    """Elimina una novedad de jugador."""
    novedad = get_object_or_404(NovedadJugador, pk=novedad_pk)
    partido_pk = novedad.partido_id
    novedad.delete()
    messages.success(request, "Novedad eliminada.")
    return redirect("tejobar_app:admin_partido_novedades", pk=partido_pk)


# ─── Consumos en Partido ─────────────────────────────────────────────────────

@admin_required
@require_POST
def admin_partido_agregar_consumo(request: HttpRequest, pk: int) -> HttpResponse:
    """Añade un producto a la cuenta del partido (equipo o jugador específico).
    Accesible desde el modal de consumo durante el partido activo.
    """
    partido = get_object_or_404(Partido, pk=pk)

    if not partido.hora_inicio:
        messages.error(request, "El partido no ha iniciado aún.")
        return redirect("tejobar_app:admin_partidos_index")

    producto_id = request.POST.get("producto_id")
    cantidad_str = request.POST.get("cantidad", "1")
    asignado_a = request.POST.get("asignado_a", "cancha")

    try:
        cantidad = max(1, int(cantidad_str))
        producto = get_object_or_404(Producto, pk=producto_id, activo=True)
    except (ValueError, TypeError):
        messages.error(request, "Producto o cantidad no válidos.")
        return redirect("tejobar_app:admin_partidos_index")

    if producto.stock < cantidad:
        messages.error(request, f"Stock insuficiente para '{producto.nombre}' (disponible: {producto.stock}).")
        return redirect("tejobar_app:admin_partidos_index")

    # Determinar equipo y/o jugador
    equipo_obj = None
    if asignado_a == "equipo1":
        equipo_obj = partido.equipo1
    elif asignado_a == "equipo2":
        equipo_obj = partido.equipo2
    elif asignado_a == "cancha":
        equipo_obj = None  # cuenta general del partido
    elif asignado_a.startswith("jugador_"):
        try:
            je_id = int(asignado_a.replace("jugador_", ""))
            je = JugadorEquipo.objects.get(pk=je_id)
            equipo_obj = je.equipo
        except (JugadorEquipo.DoesNotExist, ValueError):
            messages.error(request, "Jugador no encontrado.")
            return redirect("tejobar_app:admin_partidos_index")

    Apartado.objects.create(
        persona=None,
        producto=producto,
        cantidad=cantidad,
        partido=partido,
        equipo=equipo_obj,
        estado=Apartado.ESTADO_PENDIENTE,  # Pendiente: se cobra al finalizar
    )

    from .models import MovimientoInventario
    MovimientoInventario.objects.create(
        producto=producto,
        tipo_movimiento=MovimientoInventario.TIPO_VENTA,
        cantidad=cantidad,
        motivo=f"Consumo en partido #{partido.pk}",
        usuario=request.user,
    )

    etiqueta = equipo_obj.nombre_equipo if equipo_obj else "cuenta general"
    messages.success(request, f"{cantidad}× '{producto.nombre}' añadido a la cuenta de {etiqueta}.")
    return redirect("tejobar_app:admin_partidos_index")




@admin_required
def admin_pedido_cancha(request: HttpRequest, pk: int) -> HttpResponse:
    from django.db import transaction
    
    partido = get_object_or_404(Partido, pk=pk)
    
    if not partido.hora_inicio or partido.hora_fin:
        messages.error(request, "Solo se pueden agregar pedidos a partidos en curso.")
        return redirect("tejobar_app:partidos_index_admin")
        
    if request.method == "POST":
        producto_id = request.POST.get("producto_id", "").strip()
        cantidad_str = request.POST.get("cantidad", "").strip()
        notas = request.POST.get("notas", "").strip()
        
        if not producto_id or not cantidad_str:
            messages.error(request, "Faltan datos para el pedido.")
            return redirect("tejobar_app:partidos_index_admin")
            
        try:
            cantidad = int(cantidad_str)
            if cantidad <= 0: raise ValueError("Cantidad inválida.")
            
            with transaction.atomic():
                producto = get_object_or_404(Producto.objects.select_for_update(), pk=producto_id)
                if producto.stock < cantidad:
                    raise ValueError(f"Stock insuficiente para {producto.nombre}. Disponible: {producto.stock}")
                
                # Crear PedidoPartido
                PedidoPartido.objects.create(
                    partido=partido,
                    producto=producto,
                    cantidad=cantidad,
                    notas=notas,
                    registrado_por=request.user
                )
                
                motivo = f"Pedido en Cancha: {partido.cancha.disponibilidad} (Partido #{partido.pk})"
                
                # Actualizar Stock y Registrar Movimiento
                MovimientoInventario.objects.create(
                    producto=producto,
                    tipo_movimiento=MovimientoInventario.TIPO_VENTA,
                    cantidad=cantidad,
                    motivo=motivo,
                    usuario=request.user
                )
                
                # Novedad
                Novedad.objects.create(
                    producto=producto,
                    tipo_novedad=Novedad.TIPO_VENDIDO,
                    cantidad=cantidad,
                    descripcion=motivo
                )
                
                messages.success(request, f"Pedido de {cantidad}x {producto.nombre} agregado al partido correctamente.")
        except ValueError as e:
            messages.error(request, str(e))
        except Exception:
            messages.error(request, "Error al procesar el pedido a la cancha.")
            
    return redirect("tejobar_app:partidos_index_admin")

