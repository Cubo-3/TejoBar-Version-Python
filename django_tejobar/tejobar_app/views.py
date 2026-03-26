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
from .models import Apartado, Equipo, Historial, Jugador, Persona, Producto, JugadorEquipo, Novedad, Partido, Cancha, Categoria


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

        try:
            with transaction.atomic():
                producto = get_object_or_404(Producto.objects.select_for_update(), pk=pk)

                if producto.stock < cantidad:
                    messages.error(request, f"Lo sentimos, stock insuficiente. Disponible: {producto.stock} unidades.")
                    return redirect("tejobar_app:productos_show", pk=producto.pk)

                from django.utils import timezone
                if producto.fecha_vencimiento and producto.fecha_vencimiento < timezone.now().date():
                    messages.error(request, "Este producto está expirado y no puede ser apartado.")
                    return redirect("tejobar_app:productos_show", pk=producto.pk)

                apartado = Apartado.objects.filter(persona=persona, producto=producto, estado='pendiente').first()
                if apartado:
                    apartado.cantidad += cantidad
                    apartado.save()
                else:
                    Apartado.objects.create(
                        persona=persona,
                        producto=producto,
                        cantidad=cantidad,
                        estado="pendiente",
                    )
                
                Novedad.objects.create(
                    producto=producto,
                    tipo_novedad=Novedad.TIPO_VENDIDO,
                    cantidad=cantidad,
                    descripcion="Separado/Vendido por sistema"
                )
                
                producto.stock -= cantidad
                producto.save()

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

        context.update(
            {
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
        mis_apartados = Apartado.objects.filter(persona=persona).select_related("producto").order_by("-fecha_apartado")
        if fecha_inicio:
            mis_apartados = mis_apartados.filter(fecha_apartado__gte=fecha_inicio)
        if fecha_fin:
            mis_apartados = mis_apartados.filter(fecha_apartado__lte=fecha_fin)
            
        total_carrito = sum(a.producto.precio * a.cantidad for a in mis_apartados if a.estado == 'pendiente')
        context.update(
            {
                "total_productos": Producto.objects.count(),
                "total_categorias": Categoria.objects.filter(estado=True).count(),
                "total_equipos": Equipo.objects.count(),
                "total_jugadores": Jugador.objects.filter(estado=True).count(),
                "total_partidos": Partido.objects.exclude(estado='Cancelada').count(),
                "total_canchas": Cancha.objects.count(),
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
        from django.utils import timezone
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
    from django.utils import timezone
    
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
def persona_delete(request: HttpRequest, pk: int) -> HttpResponse:
    persona = get_object_or_404(Persona, pk=pk)
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
    
    context = {
        "equipo": equipo,
        "jugadores_equipo": jugadores_equipo,
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
    if not persona or not hasattr(persona, "jugador"):
        messages.error(request, "Solo los jugadores pueden crear equipos.")
        return redirect("tejobar_app:equipos_index")

    if JugadorEquipo.objects.filter(jugador=persona.jugador).exists():
        messages.error(request, "Ya perteneces a un equipo.")
        return redirect("tejobar_app:equipos_index")

    if request.method == "POST":
        form = EquipoForm(request.POST)
        if form.is_valid():
            equipo = form.save()
            JugadorEquipo.objects.create(jugador=persona.jugador, equipo=equipo, es_capitan=True)
            persona.rol = Persona.ROL_CAPITAN
            persona.save()
            messages.success(request, "Equipo creado correctamente. Ahora eres el capitán.")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)
    else:
        form = EquipoForm()
    return render(request, "equipos/form.html", {"form": form})


@login_required
def equipo_update(request: HttpRequest, pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
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
        miembro_a_expulsar = get_object_or_404(JugadorEquipo, equipo=equipo, jugador__persona__pk=jugador_pk)
        
        # Prevent captain from removing themselves through this view, they should use delete team
        if miembro_a_expulsar.es_capitan and persona.rol != Persona.ROL_ADMIN:
            messages.error(request, "El capitán no puede ser expulsado. Elimina el equipo si deseas salir.")
            return redirect("tejobar_app:equipos_show", pk=equipo.pk)
            
        miembro_a_expulsar.delete()
        messages.success(request, "Jugador expulsado del equipo.")
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)
        
    return redirect("tejobar_app:equipos_show", pk=equipo.pk)




@admin_required
def admin_venta_directa(request: HttpRequest) -> HttpResponse:
    from django.db import transaction

    productos = Producto.objects.filter(stock__gt=0).select_related("categoria").order_by("categoria__nombre", "nombre")
    error = None
    success = None

    if request.method == "POST":
        producto_ids = request.POST.getlist("producto_id[]")
        cantidades = request.POST.getlist("cantidad[]")
        cliente_nombre = request.POST.get("cliente_nombre", "").strip()

        if not producto_ids or not cantidades or len(producto_ids) != len(cantidades):
            error = "No agregaste ningún producto al carrito."
        else:
            try:
                with transaction.atomic():
                    total_productos = 0
                    nombres_vendidos = []

                    for pid, cant_str in zip(producto_ids, cantidades):
                        cantidad = int(cant_str)
                        if cantidad <= 0:
                            raise ValueError(f"Cantidad inválida para el producto.")

                        producto = get_object_or_404(Producto.objects.select_for_update(), pk=pid)

                        if producto.stock < cantidad:
                            raise ValueError(f"Stock insuficiente para {producto.nombre}. Disponible: {producto.stock} unidades.")

                        producto.stock -= cantidad
                        producto.save()

                        Novedad.objects.create(
                            producto=producto,
                            tipo_novedad=Novedad.TIPO_VENDIDO,
                            cantidad=cantidad,
                            descripcion=f"Venta física (POS): {cliente_nombre or 'Anónimo'}"
                        )

                        Apartado.objects.create(
                            persona=None,
                            producto=producto,
                            cantidad=cantidad,
                            estado=Apartado.ESTADO_COMPRADO,
                            cliente_nombre=cliente_nombre or None,
                        )

                        total_productos += cantidad
                        nombres_vendidos.append(f"{cantidad}x {producto.nombre}")

                    success = f"✅ Venta registrada con éxito: {total_productos} artículos. ({', '.join(nombres_vendidos)}) a {cliente_nombre or 'Cliente Anónimo'}"
                    productos = Producto.objects.filter(stock__gt=0).select_related("categoria").order_by("categoria__nombre", "nombre")

            except ValueError as e:
                error = str(e)
            except Exception as e:
                error = "Ocurrió un error al procesar la venta. Revisa los datos."

    return render(request, "ventas/directa.html", {
        "productos": productos,
        "error": error,
        "success": success,
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
def admin_product_list(request: HttpRequest) -> HttpResponse:
    Producto.actualizar_stock_vencidos()
    productos = Producto.objects.all()
    return render(request, "productos/admin_index.html", {"productos": productos})


@admin_required
def admin_product_create(request: HttpRequest) -> HttpResponse:
    if request.method == "POST":
        form = ProductoForm(request.POST, request.FILES)
        if form.is_valid():
            nuevo_prod = form.save()
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
            prod_actualizado = form.save()
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
        "success": request.build_absolute_uri(reverse("tejobar_app:pago_exitoso")),
        "failure": request.build_absolute_uri(reverse("tejobar_app:pago_fallido")),
        "pending": request.build_absolute_uri(reverse("tejobar_app:pago_pendiente"))
    }

    preference_data = {
        "items": items,
        "back_urls": back_urls,
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
    return render(request, "partidos/admin_index.html", {"partidos": partidos})


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
        partido.delete()
        messages.success(request, "Partido eliminado correctamente.")
        return redirect("tejobar_app:admin_partidos_index")
    return render(request, "partidos/confirm_delete.html", {"partido": partido})


@admin_required
def iniciar_partido(request: HttpRequest, pk: int) -> HttpResponse:
    partido = get_object_or_404(Partido, pk=pk)
    if not partido.hora_inicio:
        partido.hora_inicio = timezone.now()
        partido.estado = Partido.ESTADO_CONFIRMADA
        partido.save()
        messages.success(request, "Cronómetro del partido iniciado.")
    else:
        messages.warning(request, "Este partido ya había iniciado.")
    return redirect("tejobar_app:admin_partidos_index")


@admin_required
def finalizar_partido(request: HttpRequest, pk: int) -> HttpResponse:
    partido = get_object_or_404(Partido, pk=pk)
    if partido.hora_inicio and not partido.hora_fin:
        partido.hora_fin = timezone.now()
        partido.save()
        messages.success(request, "Cronómetro detenido. Ya puede ver el total a pagar.")
    else:
        messages.warning(request, "No se puede finalizar este partido.")
    return redirect("tejobar_app:admin_partidos_index")


@admin_required
def pagar_partido(request: HttpRequest, pk: int) -> HttpResponse:
    partido = get_object_or_404(Partido, pk=pk)
    if partido.hora_inicio and partido.hora_fin and not partido.pago_cancha:
        partido.pago_cancha = True
        partido.save()
        
        # Log the court payment
        Novedad.objects.create(
            producto=None,
            tipo_novedad=Novedad.TIPO_CANCHA,
            cantidad=1,
            descripcion=f"Pago de cancha para el partido #{partido.pk} - Total: ${partido.total_cancha}"
        )
        
        messages.success(request, "Pago de cancha registrado exitosamente.")
    else:
        messages.error(request, "No se puede registrar el pago para este partido.")
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
    if request.method == "POST":
        cancha.delete()
        messages.success(request, "Cancha eliminada.")
        return redirect("tejobar_app:admin_canchas_index")
    return render(request, "canchas/confirm_delete.html", {"cancha": cancha})


def partido_list(request: HttpRequest) -> HttpResponse:
    partidos = Partido.objects.select_related("equipo1", "equipo2", "cancha").order_by("fecha")
    return render(request, "partidos/index.html", {"partidos": partidos})


from django.http import JsonResponse

@login_required
def api_disponibilidad_partido(request: HttpRequest) -> JsonResponse:
    fecha = request.GET.get('fecha')
    hora_inicio = request.GET.get('hora')
    hora_reserva_fin = request.GET.get('hora_reserva_fin')
    partido_id = request.GET.get('partido_id')

    if not fecha or not hora_inicio:
        return JsonResponse({"canchas_ocupadas": {}, "equipos_ocupados": {}})

    # Traer partidos del mismo día para evaluar empalme
    qs = Partido.objects.filter(fecha=fecha).exclude(estado=Partido.ESTADO_CANCELADA)
    if partido_id:
        qs = qs.exclude(pk=partido_id)
        
    canchas_ocupadas = {}
    equipos_ocupados = {}

    for p in qs:
        # Check overlap
        start_b = p.hora
        end_b = p.hora_reserva_fin if p.hora_reserva_fin else ("23:59" if p.estado != Partido.ESTADO_CONFIRMADA else "23:59")
        
        start_a = hora_inicio
        end_a = hora_reserva_fin if hora_reserva_fin else "23:59"

        if start_a < end_b and start_b < end_a:
            # Hay cruce, registrar motivo
            motivo = f"hasta {p.hora_reserva_fin}" if p.hora_reserva_fin else "En curso"
            
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
def admin_categorias_index(request):
    persona = getattr(request.user, "persona", None)
    if not persona or persona.rol != Persona.ROL_ADMIN:
        return redirect("tejobar_app:dashboard")

    categorias = Categoria.objects.all()
    return render(request, "categorias/admin_index.html", {"categorias": categorias})

@login_required
def admin_categorias_create(request):
    persona = getattr(request.user, "persona", None)
    if not persona or persona.rol != Persona.ROL_ADMIN:
        return redirect("tejobar_app:dashboard")

    if request.method == "POST":
        form = CategoriaForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, "Categoría creada con éxito.")
            return redirect("tejobar_app:admin_categorias_index")
    else:
        form = CategoriaForm()

    return render(request, "categorias/form.html", {"form": form})

@login_required
def admin_categorias_update(request, pk):
    persona = getattr(request.user, "persona", None)
    if not persona or persona.rol != Persona.ROL_ADMIN:
        return redirect("tejobar_app:dashboard")

    categoria = get_object_or_404(Categoria, pk=pk)

    if request.method == "POST":
        form = CategoriaForm(request.POST, instance=categoria)
        if form.is_valid():
            form.save()
            messages.success(request, "Categoría editada con éxito.")
            return redirect("tejobar_app:admin_categorias_index")
    else:
        form = CategoriaForm(instance=categoria)

    return render(request, "categorias/form.html", {"form": form, "categoria": categoria})

@login_required
def admin_categorias_delete(request, pk):
    persona = getattr(request.user, "persona", None)
    if not persona or persona.rol != Persona.ROL_ADMIN:
        return redirect("tejobar_app:dashboard")

    categoria = get_object_or_404(Categoria, pk=pk)

    if request.method == "POST":
        categoria.delete()
        messages.success(request, "Categoría eliminada.")
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
            "success": request.build_absolute_uri(reverse('tejobar_app:pago_exitoso')),
            "failure": request.build_absolute_uri(reverse('tejobar_app:pago_fallido')),
            "pending": request.build_absolute_uri(reverse('tejobar_app:pago_pendiente'))
        },
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
def crear_preferencia_cancha(request, pk):
    partido = get_object_or_404(Partido, pk=pk)
    
    if partido.pago_cancha:
        messages.warning(request, "Este partido ya está pagado.")
        return redirect("dashboard")
        
    monto_total = partido.total_cancha
    if monto_total <= 0:
        messages.warning(request, "El partido no tiene costo de cancha o no se ha calculado.")
        return redirect("dashboard")
        
    sdk = mercadopago.SDK(settings.MERCADOPAGO_ACCESS_TOKEN)
    
    preference_data = {
        "items": [
            {
                "id": str(partido.pk),
                "title": f"Cancha para Partido #{partido.pk}",
                "quantity": 1,
                "currency_id": "COP",
                "unit_price": float(monto_total)
            }
        ],
        "back_urls": {
            "success": request.build_absolute_uri(reverse('tejobar_app:pago_exitoso')),
            "failure": request.build_absolute_uri(reverse('tejobar_app:pago_fallido')),
            "pending": request.build_absolute_uri(reverse('tejobar_app:pago_pendiente'))
        },
        "external_reference": f"cancha_{partido.pk}"
    }

    preference_response = sdk.preference().create(preference_data)
    preference = preference_response["response"]
    
    return redirect(preference["init_point"])

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
            partido_id = external_reference.split("_")[1]
            partido = get_object_or_404(Partido, pk=partido_id)
            if not partido.pago_cancha:
                partido.pago_cancha = True
                partido.save()
                
                Novedad.objects.create(
                    producto=None,
                    tipo_novedad=Novedad.TIPO_CANCHA,
                    cantidad=1,
                    descripcion=f"Pago Cancha MercadoPago (ID: {payment_id}) - Partido #{partido.pk}"
                )
                messages.success(request, "Pago de cancha exitoso. Se ha registrado en novedades.")
                
    return redirect("dashboard")

@login_required
def pago_fallido(request):
    messages.error(request, "El pago a través de MercadoPago ha fallado o fue cancelado.")
    return redirect("dashboard")

@login_required
def pago_pendiente(request):
    messages.info(request, "El pago se encuentra pendiente. Te notificaremos cuando se apruebe.")
    return redirect("dashboard")


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
    if request.method == "POST":
        persona = getattr(request.user, "persona", None)
        apartado = get_object_or_404(Apartado, pk=pk, persona=persona, estado=Apartado.ESTADO_PENDIENTE)
        nueva_cantidad = int(request.POST.get("cantidad", 1))
        
        if nueva_cantidad <= 0:
            messages.error(request, "La cantidad debe ser mayor a cero.")
            return redirect("tejobar_app:dashboard")
            
        diferencia = nueva_cantidad - apartado.cantidad
        if diferencia > 0 and apartado.producto.stock < diferencia:
            messages.error(request, f"Stock insuficiente. Disponible adicional: {apartado.producto.stock}")
            return redirect("tejobar_app:dashboard")
            
        # Update stock and Apartado
        apartado.producto.stock -= diferencia
        apartado.producto.save()
        apartado.cantidad = nueva_cantidad
        apartado.save()
        
        messages.success(request, "Cantidad del carrito actualizada.")
        
    return redirect("tejobar_app:dashboard")


@login_required
def eliminar_item_carrito(request: HttpRequest, pk: int) -> HttpResponse:
    if request.method == "POST":
        persona = getattr(request.user, "persona", None)
        apartado = get_object_or_404(Apartado, pk=pk, persona=persona, estado=Apartado.ESTADO_PENDIENTE)
        
        # Restore stock
        apartado.producto.stock += apartado.cantidad
        apartado.producto.save()
        
        apartado.delete()
        messages.success(request, "Producto eliminado del carrito.")
        
    return redirect("tejobar_app:dashboard")


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
