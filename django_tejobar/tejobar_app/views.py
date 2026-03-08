from django.contrib import messages
from django.contrib.auth import authenticate, login, logout
from django.contrib.auth.decorators import login_required
from django.contrib.auth.models import User
from django.http import HttpRequest, HttpResponse
from django.shortcuts import get_object_or_404, redirect, render
from django.utils import timezone

from .forms import (
    EquipoForm,
    LoginForm,
    PersonaForm,
    ProductoForm,
    RegistroForm,
    PartidoForm,
    CanchaForm
)
from .models import Apartado, Equipo, Historial, Jugador, Persona, Producto, JugadorEquipo, Novedad, Partido, Cancha


def home(request: HttpRequest) -> HttpResponse:
    Producto.actualizar_stock_vencidos()
    productos = Producto.objects.disponibles()[:6]
    return render(request, "home.html", {"productos": productos})


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
            rol = form.cleaned_data["rol"]
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
    productos = Producto.objects.disponibles()
    return render(request, "productos/index.html", {"productos": productos})


def product_detail(request: HttpRequest, pk: int) -> HttpResponse:
    producto = get_object_or_404(Producto, pk=pk)
    return render(request, "productos/show.html", {"producto": producto})


@login_required
def apartar_producto(request: HttpRequest, pk: int) -> HttpResponse:
    producto = get_object_or_404(Producto, pk=pk)

    if request.method == "POST":
        try:
            cantidad = int(request.POST.get("cantidad", "1"))
        except ValueError:
            cantidad = 1

        if cantidad <= 0:
            messages.error(request, "La cantidad debe ser mayor que cero.")
            return redirect("tejobar_app:productos_show", pk=producto.pk)

        if producto.stock < cantidad:
            messages.error(
                request,
                f"Stock insuficiente. Disponible: {producto.stock}",
            )
            return redirect("tejobar_app:productos_show", pk=producto.pk)

        from django.utils import timezone
        if producto.fecha_vencimiento and producto.fecha_vencimiento < timezone.now().date():
            messages.error(request, "Este producto está expirado y no puede ser apartado.")
            return redirect("tejobar_app:productos_show", pk=producto.pk)

        persona = getattr(request.user, "persona", None)
        if not persona:
            messages.error(request, "No tienes un perfil de persona asociado.")
            return redirect("tejobar_app:productos_show", pk=producto.pk)

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

        messages.success(
            request,
            "Producto apartado con éxito. Puedes verlo en tu dashboard.",
        )
        return redirect("tejobar_app:productos_show", pk=producto.pk)

    return redirect("tejobar_app:productos_show", pk=producto.pk)


@login_required
def dashboard(request: HttpRequest) -> HttpResponse:
    Producto.actualizar_stock_vencidos()
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
            
        context.update(
            {
                "total_productos": Producto.objects.count(),
                "productos_bajo_stock": Producto.objects.filter(stock__lt=10).count(),
                "apartados": apartados,
            }
        )
    else:
        mis_apartados = Apartado.objects.filter(persona=persona).select_related("producto").order_by("-fecha_apartado")
        if fecha_inicio:
            mis_apartados = mis_apartados.filter(fecha_apartado__gte=fecha_inicio)
        if fecha_fin:
            mis_apartados = mis_apartados.filter(fecha_apartado__lte=fecha_fin)
            
        context.update(
            {
                "mis_apartados": mis_apartados,
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
def persona_list(request: HttpRequest) -> HttpResponse:
    personas = Persona.objects.all()
    return render(request, "personas/index.html", {"personas": personas})


@login_required
def persona_create(request: HttpRequest) -> HttpResponse:
    if request.method == "POST":
        form = PersonaForm(request.POST)
        if form.is_valid():
            form.save()
            messages.success(request, "Persona creada correctamente")
            return redirect("tejobar_app:personas_index")
    else:
        form = PersonaForm()
    return render(request, "personas/form.html", {"form": form})


@login_required
def persona_update(request: HttpRequest, pk: int) -> HttpResponse:
    persona = get_object_or_404(Persona, pk=pk)
    if request.method == "POST":
        form = PersonaForm(request.POST, instance=persona)
        if form.is_valid():
            form.save()
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
    equipos = Equipo.objects.annotate(num_jugadores=Count('equipo_jugadores')).filter(num_jugadores__lt=10)
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
                if equipo.equipo_jugadores.count() < 10:
                    puede_unirse = True

    jugadores_equipo = equipo.equipo_jugadores.select_related("jugador__persona").all()
    
    context = {
        "equipo": equipo,
        "jugadores_equipo": jugadores_equipo,
        "es_miembro": es_miembro,
        "es_capitan": es_capitan,
        "es_admin": persona.rol == Persona.ROL_ADMIN,
        "puede_unirse": puede_unirse,
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

    if equipo.equipo_jugadores.count() >= 10:
        messages.error(request, "El equipo está lleno (límite 10 jugadores).")
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
def equipo_remove_member(request: HttpRequest, pk: int, jugador_pk: int) -> HttpResponse:
    equipo = get_object_or_404(Equipo, pk=pk)
    persona = getattr(request.user, "persona", None)
    
    es_capitan = False
    if persona and hasattr(persona, "jugador"):
        es_capitan = JugadorEquipo.objects.filter(jugador=persona.jugador, equipo=equipo, es_capitan=True).exists()

    if not persona or (persona.rol != Persona.ROL_ADMIN and not es_capitan):
        messages.error(request, "No tienes permiso para expulsar jugadores de este equipo.")
        return redirect("tejobar_app:equipos_show", pk=equipo.pk)

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
            messages.success(request, "Producto actualizado correctamente")
            return redirect("tejobar_app:admin_productos_index")
    else:
        form = ProductoForm(instance=producto)
    return render(request, "productos/form.html", {"form": form, "producto": producto})


@admin_required
def admin_product_delete(request: HttpRequest, pk: int) -> HttpResponse:
    producto = get_object_or_404(Producto, pk=pk)
    if request.method == "POST":
        producto.delete()
        messages.success(request, "Producto eliminado correctamente")
        return redirect("tejobar_app:admin_productos_index")
    return render(request, "productos/confirm_delete.html", {"producto": producto})


@admin_required
def admin_novedades_index(request: HttpRequest) -> HttpResponse:
    fecha_inicio = request.GET.get("fecha_inicio")
    fecha_fin = request.GET.get("fecha_fin")
    tipo_novedad = request.GET.get("tipo_novedad")

    novedades = Novedad.objects.select_related("producto").order_by("-fecha")

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

    context = {
        "novedades": novedades,
        "fecha_inicio": fecha_inicio or "",
        "fecha_fin": fecha_fin or "",
        "tipo_novedad": tipo_novedad or "",
        "tipos_choices": Novedad.TIPO_CHOICES,
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
