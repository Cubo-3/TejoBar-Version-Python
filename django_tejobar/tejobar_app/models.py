from django.contrib.auth.models import User
from django.db import models
from django.core.validators import MinValueValidator, MaxValueValidator
from django.core.exceptions import ValidationError


class Persona(models.Model):
    ROL_JUGADOR = "jugador"
    ROL_CAPITAN = "capitan"
    ROL_ADMIN = "admin"

    ROL_CHOICES = [
        (ROL_JUGADOR, "Jugador"),
        (ROL_CAPITAN, "Capitán"),
        (ROL_ADMIN, "Admin"),
    ]

    user = models.OneToOneField(
        User, on_delete=models.CASCADE, related_name="persona", null=True, blank=True
    )
    nombre = models.CharField(max_length=100)
    correo = models.EmailField(unique=True)
    numero = models.CharField(max_length=20)
    rol = models.CharField(max_length=10, choices=ROL_CHOICES, default=ROL_JUGADOR)

    def __str__(self) -> str:
        return self.nombre


class Jugador(models.Model):
    persona = models.OneToOneField(
        Persona,
        primary_key=True,
        on_delete=models.CASCADE,
        related_name="jugador",
    )
    estado = models.BooleanField(default=True)
    rut = models.CharField(max_length=50, blank=True, null=True)

    def __str__(self) -> str:
        return f"{self.persona.nombre} ({'Activo' if self.estado else 'Inactivo'})"


class Equipo(models.Model):
    nombre_equipo = models.CharField(max_length=100, unique=True)

    @property
    def capitan_nombre(self) -> str:
        cap = self.equipo_jugadores.filter(es_capitan=True).first()
        if cap:
            return cap.get_nombre()
        return "Sin Capitán"

    @property
    def num_jugadores(self):
        return self.equipo_jugadores.count()

    @property
    def is_completo(self):
        return 3 <= self.num_jugadores <= 5
        
    @property
    def is_excedido(self):
        return self.num_jugadores > 5
        
    @property
    def is_insuficiente(self):
        return self.num_jugadores < 3

    def __str__(self) -> str:
        return self.nombre_equipo


class Cancha(models.Model):
    estado = models.BooleanField(default=True, null=True)
    disponibilidad = models.CharField(max_length=100, blank=True, null=True)
    precio_por_hora = models.FloatField(default=8000.0)

    def __str__(self) -> str:
        if self.disponibilidad:
            return self.disponibilidad
        return f"Cancha {self.pk}"


class ProductoQuerySet(models.QuerySet):
    def disponibles(self):
        from django.utils import timezone
        today = timezone.now().date()
        from django.db.models import Q
        return self.filter(stock__gt=0).filter(
            Q(fecha_vencimiento__gte=today) | Q(fecha_vencimiento__isnull=True)
        )


class Categoria(models.Model):
    nombre = models.CharField(max_length=100, unique=True)
    descripcion = models.TextField(blank=True, null=True)
    estado = models.BooleanField(default=True) # Activo/Inactivo

    def __str__(self):
        return self.nombre

class Producto(models.Model):
    nombre = models.CharField(max_length=100)
    precio = models.DecimalField(
        max_digits=10, 
        decimal_places=2,
        validators=[
            MinValueValidator(0.01, message="El precio no puede ser 0 ni negativo."),
            MaxValueValidator(10000000.00, message="El precio excede el límite permitido (10M).")
        ]
    )
    stock = models.IntegerField(
        validators=[
            MinValueValidator(0, message="El stock no puede ser negativo."),
            MaxValueValidator(10000, message="Capacidad máxima de bodega excedida (10,000 u).")
        ]
    )
    fecha_vencimiento = models.DateField(blank=True, null=True)
    descripcion = models.TextField("Descripción", blank=True, null=True)
    imagen = models.ImageField(upload_to="productos", blank=True, null=True)
    categoria = models.ForeignKey(
        Categoria,
        on_delete=models.SET_NULL,
        null=True,
        blank=True,
        related_name="productos",
    )

    objects = ProductoQuerySet.as_manager()

    def __str__(self) -> str:
        return self.nombre

    def clean(self):
        super().clean()
        if self.precio is not None and self.precio <= 0:
            raise ValidationError({'precio': 'El precio debe ser mayor a 0.'})
        if self.stock is not None and self.stock < 0:
            raise ValidationError({'stock': 'El stock no puede ser negativo.'})

    def save(self, *args, **kwargs):
        self.full_clean()
        super().save(*args, **kwargs)

    @property
    def is_expired(self) -> bool:
        from django.utils import timezone
        if self.fecha_vencimiento:
            return self.fecha_vencimiento < timezone.now().date()
        return False

    @classmethod
    def actualizar_stock_vencidos(cls):
        from django.utils import timezone
        today = timezone.now().date()
        vencidos = cls.objects.filter(
            fecha_vencimiento__lt=today, 
            stock__gt=0
        )
        
        from .models import Novedad # Import here to avoid circular logic at load
        for p in vencidos:
            if p.stock > 0:
                p_current_stock = p.stock
                from .models import MovimientoInventario
                MovimientoInventario.objects.create(
                    producto=p,
                    tipo_movimiento=MovimientoInventario.TIPO_PERDIDA,
                    cantidad=p_current_stock,
                    motivo="Stock caducado automáticamente"
                )
                Novedad.objects.create(
                    producto=p,
                    tipo_novedad=Novedad.TIPO_VENCIDO,
                    cantidad=p_current_stock,
                    descripcion="Stock caducado automáticamente"
                )


class ApartadoQuerySet(models.QuerySet):
    def pendientes(self):
        return self.filter(estado="pendiente")

    def comprados(self):
        return self.filter(estado="comprado")


class Apartado(models.Model):
    ESTADO_PENDIENTE = "pendiente"
    ESTADO_COMPRADO = "comprado"
    ESTADO_CANCELADO = "cancelado"

    ESTADO_CHOICES = [
        (ESTADO_PENDIENTE, "Pendiente"),
        (ESTADO_COMPRADO, "Comprado"),
        (ESTADO_CANCELADO, "Cancelado / Abandonado"),
    ]

    persona = models.ForeignKey(
        Persona, on_delete=models.CASCADE, related_name="apartados",
        null=True, blank=True
    )
    producto = models.ForeignKey(
        Producto, on_delete=models.CASCADE, related_name="apartados"
    )
    cantidad = models.IntegerField()
    fecha_apartado = models.DateTimeField(auto_now_add=True)
    estado = models.CharField(
        max_length=20, choices=ESTADO_CHOICES, default=ESTADO_PENDIENTE
    )
    cliente_nombre = models.CharField(max_length=150, blank=True, null=True)
    cliente_telefono = models.CharField(max_length=30, blank=True, null=True)

    objects = ApartadoQuerySet.as_manager()

    def __str__(self) -> str:
        return f"Apartado #{self.pk} - {self.persona} - {self.producto}"

    @classmethod
    def liberar_carritos_abandonados(cls, horas_limite=2):
        from django.utils import timezone
        from datetime import timedelta
        
        tiempo_limite = timezone.now() - timedelta(hours=horas_limite)
        abandonados = cls.objects.filter(
            estado=cls.ESTADO_PENDIENTE,
            fecha_apartado__lt=tiempo_limite
        )
        
        from .models import Novedad # Prevent circular import
        from .models import MovimientoInventario
        for apartado in abandonados:
            if apartado.producto:
                MovimientoInventario.objects.create(
                    producto=apartado.producto,
                    tipo_movimiento=MovimientoInventario.TIPO_INGRESO,
                    cantidad=apartado.cantidad,
                    motivo="Devolución: Carrito abandonado."
                )
                
                Novedad.objects.create(
                    producto=apartado.producto,
                    tipo_novedad=Novedad.TIPO_AGREGADO,
                    cantidad=apartado.cantidad,
                    descripcion="Stock devuelto: Carrito abandonado."
                )
        
        # Finally, mark all abandoned as cancelled
        abandonados.update(estado=cls.ESTADO_CANCELADO)


class Historial(models.Model):
    persona = models.ForeignKey(
        Persona, on_delete=models.CASCADE, related_name="historial"
    )
    producto = models.ForeignKey(
        Producto, on_delete=models.CASCADE, related_name="historial"
    )
    cantidad = models.IntegerField()
    precio = models.DecimalField(max_digits=10, decimal_places=2)
    total = models.DecimalField(max_digits=10, decimal_places=2)
    fecha_entrega = models.DateTimeField(auto_now_add=True)
    estado = models.CharField(max_length=255, default="entregado")
    created_at = models.DateTimeField(auto_now_add=True, null=True, blank=True)
    updated_at = models.DateTimeField(auto_now=True, null=True, blank=True)

    def __str__(self) -> str:
        return f"Historial #{self.pk} - {self.persona} - {self.producto}"


class JugadorEquipo(models.Model):
    TIPO_REGISTRADO = 'registrado'
    TIPO_INVITADO = 'invitado'

    TIPO_CHOICES = [
        (TIPO_REGISTRADO, 'Registrado'),
        (TIPO_INVITADO, 'Invitado'),
    ]

    jugador = models.ForeignKey(
        Jugador, on_delete=models.CASCADE, related_name="jugador_equipos", null=True, blank=True
    )
    equipo = models.ForeignKey(
        Equipo, on_delete=models.CASCADE, related_name="equipo_jugadores"
    )
    es_capitan = models.BooleanField(default=False)
    
    tipo_usuario = models.CharField(max_length=20, choices=TIPO_CHOICES, default=TIPO_REGISTRADO)
    nombre_invitado = models.CharField(max_length=100, blank=True, null=True)
    telefono_invitado = models.CharField(max_length=20, blank=True, null=True)
    correo_invitado = models.EmailField(blank=True, null=True)

    def clean(self):
        super().clean()
        from django.core.exceptions import ValidationError
        if self.tipo_usuario == self.TIPO_REGISTRADO and self.jugador:
            equipos_previos = JugadorEquipo.objects.filter(jugador=self.jugador).exclude(pk=self.pk)
            if equipos_previos.exists():
                raise ValidationError({"jugador": "Este jugador ya pertenece a otro equipo. Un jugador no puede estar en dos equipos a la vez."})
        
        if self.es_capitan:
            capitanes = JugadorEquipo.objects.filter(equipo=self.equipo, es_capitan=True).exclude(pk=self.pk)
            if capitanes.exists():
                raise ValidationError({"es_capitan": f"El equipo '{self.equipo.nombre_equipo}' ya tiene un capitán asignado."})

    def save(self, *args, **kwargs):
        self.full_clean()
        super().save(*args, **kwargs)

    @classmethod
    def reactivar_jugador_en_equipo(cls, jugador, equipo_destino_id):
        from django.db import transaction
        from django.core.exceptions import ValidationError
        # Se requiere importar localmente para evitar dependencias circulares complejas
        from tejobar_app.models import Equipo
        
        with transaction.atomic():
            equipo_actual = cls.objects.filter(jugador=jugador).first()
            if equipo_actual:
                if equipo_actual.equipo.id == equipo_destino_id:
                    raise ValidationError("El jugador ya está activo en este equipo.")
                equipo_actual.delete()

            equipo_destino = Equipo.objects.get(id=equipo_destino_id)
            ya_hay_capitan = cls.objects.filter(equipo=equipo_destino, es_capitan=True).exists()

            nuevo_vinculo = cls(
                jugador=jugador,
                equipo=equipo_destino,
                es_capitan=not ya_hay_capitan,
                tipo_usuario=cls.TIPO_REGISTRADO
            )
            nuevo_vinculo.save()
            return nuevo_vinculo

    def __str__(self) -> str:
        return f"{self.get_nombre()} en {self.equipo.nombre_equipo}"

    def get_nombre(self):
        if self.tipo_usuario == self.TIPO_REGISTRADO and self.jugador:
            return self.jugador.persona.nombre
        return self.nombre_invitado or "Jugador Invitado Desconocido"

    def get_telefono(self):
        if self.tipo_usuario == self.TIPO_REGISTRADO and self.jugador:
            return self.jugador.persona.numero
        return self.telefono_invitado

    def get_correo(self):
        if self.tipo_usuario == self.TIPO_REGISTRADO and self.jugador:
            return self.jugador.persona.correo
        return self.correo_invitado


class Partido(models.Model):
    ESTADO_PENDIENTE = "Pendiente"
    ESTADO_CONFIRMADA = "Confirmada"
    ESTADO_CANCELADA = "Cancelada"

    ESTADO_CHOICES = [
        (ESTADO_PENDIENTE, "Pendiente"),
        (ESTADO_CONFIRMADA, "Confirmada"),
        (ESTADO_CANCELADA, "Cancelada"),
    ]

    fecha = models.DateField()
    hora = models.CharField(max_length=20)
    equipo1 = models.ForeignKey(
        Equipo,
        on_delete=models.CASCADE,
        related_name="partidos_como_equipo1",
        null=True,  # Allow null temporarily for existing rows
    )
    equipo2 = models.ForeignKey(
        Equipo,
        on_delete=models.CASCADE,
        related_name="partidos_como_equipo2",
        null=True,
    )
    cancha = models.ForeignKey(Cancha, on_delete=models.CASCADE, related_name="partidos")
    estado = models.CharField(
        max_length=20, choices=ESTADO_CHOICES, default=ESTADO_PENDIENTE
    )
    hora_reserva_fin = models.CharField(max_length=20, blank=True, null=True) # User-input reservation end time
    hora_inicio = models.DateTimeField(blank=True, null=True)
    hora_fin = models.DateTimeField(blank=True, null=True)
    pago_cancha = models.BooleanField(default=False)

    def is_overlapping_time(self, other) -> bool:
        """Helper para determinar si dos partidos cruzan en horarios basados en strings (HH:MM)."""
        if self.fecha != other.fecha:
            return False

        if other.estado == Partido.ESTADO_CANCELADA:
            return False

        def parse_time(time_str, is_end=False):
            if not time_str:
                return "23:59" if is_end else "00:00"
            return time_str.zfill(5)  # Ensure "9:00" becomes "09:00"

        start_a = parse_time(self.hora)
        end_a = parse_time(self.hora_reserva_fin, is_end=True)
        start_b = parse_time(other.hora)
        end_b = parse_time(other.hora_reserva_fin, is_end=True)

        if start_a < end_b and start_b < end_a:
            return True
        return False

    def clean(self):
        super().clean()
        from django.core.exceptions import ValidationError
        from django.db.models import Q

        errors = {}

        if self.fecha and self.hora:
            start_time = self.hora.zfill(5)
            end_time = self.hora_reserva_fin.zfill(5) if self.hora_reserva_fin else "23:59"

            if start_time < "10:00" or start_time > "23:00":
                errors["hora"] = "El horario de atención es de 10:00 a 23:00. No se pueden programar partidos fuera de este horario."

            if end_time < "10:00" or end_time > "23:59":
                if "hora_reserva_fin" not in errors:
                    errors["hora_reserva_fin"] = "El horario de finalización debe estar entre las 10:00 y las 23:59."

            if start_time >= end_time:
                errors["hora_reserva_fin"] = "La hora final debe ser mayor que la inicial."

            # Fetch potential overlapping matches for the same date
            qs = Partido.objects.filter(fecha=self.fecha).exclude(pk=self.pk)
            overlapping_matches = [m for m in qs if self.is_overlapping_time(m)]

            for m in overlapping_matches:
                if hasattr(self, "cancha") and self.cancha and m.cancha_id == self.cancha.id:
                    errors["cancha"] = f"La cancha se cruza con otro partido ({m.hora} - {m.hora_reserva_fin or 'En curso'})."

                if hasattr(self, "equipo1") and self.equipo1:
                    if m.equipo1_id == self.equipo1.id or m.equipo2_id == self.equipo1.id:
                        errors["equipo1"] = f"El equipo se cruza con otro partido ({m.hora} - {m.hora_reserva_fin or 'En curso'})."

                if hasattr(self, "equipo2") and self.equipo2:
                    if m.equipo1_id == self.equipo2.id or m.equipo2_id == self.equipo2.id:
                        errors["equipo2"] = f"El equipo se cruza con otro partido ({m.hora} - {m.hora_reserva_fin or 'En curso'})."

        # Validaciones de cantidad de jugadores para los equipos (solo permitir entre 3 y 5)
        if hasattr(self, 'equipo1') and self.equipo1:
            num1 = self.equipo1.num_jugadores
            if num1 < 3:
                errors["equipo1"] = "Este equipo no tiene suficientes jugadores. Debe tener al menos 3."
            elif num1 > 5:
                errors["equipo1"] = "Un equipo no puede tener más de 5 jugadores."
                
        if hasattr(self, 'equipo2') and self.equipo2:
            num2 = self.equipo2.num_jugadores
            if num2 < 3:
                errors["equipo2"] = "Este equipo no tiene suficientes jugadores. Debe tener al menos 3."
            elif num2 > 5:
                errors["equipo2"] = "Un equipo no puede tener más de 5 jugadores."

        if errors:
            raise ValidationError(errors)

    def __str__(self) -> str:
        return f"Partido #{self.pk} - {self.fecha} {self.hora}"

    @property
    def horas_jugadas(self):
        if not self.hora_inicio or not self.hora_fin:
            return 0.0
        diferencia = self.hora_fin - self.hora_inicio
        minutos = diferencia.total_seconds() / 60
        return round(minutos / 60, 2)

    @property
    def total_cancha(self):
        if not self.cancha or not self.cancha.precio_por_hora:
            return 0.0
        return round(self.horas_jugadas * self.cancha.precio_por_hora, 2)


class Torneo(models.Model):
    partido = models.OneToOneField(
        Partido, on_delete=models.CASCADE, related_name="torneo"
    )
    fecha = models.DateTimeField()
    equipo1 = models.ForeignKey(
        Equipo,
        on_delete=models.CASCADE,
        related_name="torneos_como_equipo1",
    )
    equipo2 = models.ForeignKey(
        Equipo,
        on_delete=models.CASCADE,
        related_name="torneos_como_equipo2",
    )
    cancha = models.ForeignKey(
        Cancha, on_delete=models.CASCADE, related_name="torneos"
    )

    def __str__(self) -> str:
        return f"Torneo #{self.pk}"


class CompraQuerySet(models.QuerySet):
    pass


class Compra(models.Model):
    jugador = models.ForeignKey(
        Jugador, on_delete=models.CASCADE, related_name="compras"
    )
    fecha = models.DateField()
    total = models.FloatField()

    objects = CompraQuerySet.as_manager()

    def __str__(self) -> str:
        return f"Compra #{self.pk} - {self.fecha}"


class Novedad(models.Model):
    TIPO_AGREGADO = "agregado"
    TIPO_VENDIDO = "vendido"
    TIPO_VENCIDO = "vencido"
    TIPO_CANCHA = "cancha"
    TIPO_PERDIDA = "perdida"

    TIPO_CHOICES = [
        (TIPO_AGREGADO, "Agregado / Ingreso"),
        (TIPO_VENDIDO, "Vendido / Apartado"),
        (TIPO_VENCIDO, "Vencido / Retirado"),
        (TIPO_CANCHA, "Pago de Cancha"),
        (TIPO_PERDIDA, "Accidente / Pérdida"),
    ]

    producto = models.ForeignKey(
        Producto, on_delete=models.SET_NULL, related_name="novedades", null=True, blank=True
    )
    tipo_novedad = models.CharField(
        max_length=20, choices=TIPO_CHOICES, default=TIPO_AGREGADO
    )
    cantidad = models.IntegerField(default=1)
    fecha = models.DateTimeField(auto_now_add=True)
    descripcion = models.CharField(max_length=255, blank=True, null=True)

    def __str__(self) -> str:
        prod_name = self.producto.nombre if self.producto else "N/A"
        return f"Novedad: {self.get_tipo_novedad_display()} - {prod_name} ({self.cantidad})"

    @property
    def movimiento(self) -> str:
        if self.tipo_novedad == self.TIPO_AGREGADO:
            return "Entrada"
        elif self.tipo_novedad in [self.TIPO_VENDIDO, self.TIPO_VENCIDO, self.TIPO_PERDIDA]:
            return "Salida"
        return "N/A"

class HistorialEquipo(models.Model):
    jugador = models.ForeignKey(Jugador, on_delete=models.CASCADE, related_name="historial_equipos")
    equipo = models.ForeignKey(Equipo, on_delete=models.CASCADE, related_name="historial_jugadores")
    fecha_ingreso = models.DateTimeField(auto_now_add=True)
    fecha_salida = models.DateTimeField(null=True, blank=True)
    fue_capitan = models.BooleanField(default=False)

    def __str__(self):
        return f"{self.jugador} en {self.equipo} ({self.fecha_ingreso.date()})"
    
    @property
    def is_activo(self):
        return self.fecha_salida is None

# Configuración de Signals para automatizar el historial
from django.db.models.signals import post_save, post_delete
from django.dispatch import receiver

@receiver(post_save, sender=JugadorEquipo)
def gestionar_historial_ingreso(sender, instance, created, **kwargs):
    if instance.tipo_usuario == JugadorEquipo.TIPO_REGISTRADO and instance.jugador:
        if created:
            HistorialEquipo.objects.create(
                jugador=instance.jugador,
                equipo=instance.equipo,
                fue_capitan=instance.es_capitan
            )
        else:
            from django.utils import timezone
            historial = HistorialEquipo.objects.filter(
                jugador=instance.jugador, equipo=instance.equipo, fecha_salida__isnull=True
            ).first()
            if historial and historial.fue_capitan != instance.es_capitan:
                historial.fue_capitan = instance.es_capitan
                historial.save()

@receiver(post_delete, sender=JugadorEquipo)
def gestionar_historial_salida(sender, instance, **kwargs):
    if instance.tipo_usuario == JugadorEquipo.TIPO_REGISTRADO and instance.jugador:
        from django.utils import timezone
        historial = HistorialEquipo.objects.filter(
            jugador=instance.jugador, equipo=instance.equipo, fecha_salida__isnull=True
        ).first()
        if historial:
            historial.fecha_salida = timezone.now()
            historial.save()

class MovimientoInventario(models.Model):
    TIPO_INGRESO = "ingreso"
    TIPO_VENTA = "venta"
    TIPO_PERDIDA = "perdida"

    TIPO_CHOICES = [
        (TIPO_INGRESO, "Ingreso"),
        (TIPO_VENTA, "Venta"),
        (TIPO_PERDIDA, "Pérdida"),
    ]

    ORIGEN_COMPRA = "COMPRA_PROVEEDOR"
    ORIGEN_DEVOLUCION = "DEVOLUCION_CLIENTE"
    ORIGEN_AJUSTE = "AJUSTE_INVENTARIO"

    ORIGEN_CHOICES = [
        (ORIGEN_COMPRA, "Compra a proveedor"),
        (ORIGEN_DEVOLUCION, "Devolución de cliente"),
        (ORIGEN_AJUSTE, "Ajuste de inventario"),
    ]

    id_movimiento = models.AutoField(primary_key=True)
    producto = models.ForeignKey(Producto, on_delete=models.CASCADE, related_name="movimientos")
    tipo_movimiento = models.CharField(max_length=20, choices=TIPO_CHOICES)
    cantidad = models.PositiveIntegerField()
    motivo = models.CharField(max_length=255, blank=True, null=True)
    origen = models.CharField(max_length=50, choices=ORIGEN_CHOICES, blank=True, null=True)
    detalle = models.TextField(blank=True, null=True)
    fecha = models.DateTimeField(auto_now_add=True)
    usuario = models.ForeignKey(User, on_delete=models.SET_NULL, null=True, blank=True)

    def clean(self):
        super().clean()
        from django.core.exceptions import ValidationError
        if self.tipo_movimiento == self.TIPO_PERDIDA and not self.motivo:
            raise ValidationError({'motivo': 'El motivo es obligatorio para registrar una pérdida.'})

    def save(self, *args, **kwargs):
        is_new = self.pk is None
        self.full_clean()
        
        if is_new:
            if self.tipo_movimiento == self.TIPO_INGRESO:
                self.producto.stock += self.cantidad
            elif self.tipo_movimiento in [self.TIPO_VENTA, self.TIPO_PERDIDA]:
                if self.producto.stock < self.cantidad:
                    from django.core.exceptions import ValidationError
                    raise ValidationError(f'Stock insuficiente para {self.producto.nombre}. Disponible: {self.producto.stock}')
                self.producto.stock -= self.cantidad
            self.producto.save()
            
        super().save(*args, **kwargs)

    def __str__(self) -> str:
        return f"{self.get_tipo_movimiento_display()} - {self.producto.nombre} ({self.cantidad})"
