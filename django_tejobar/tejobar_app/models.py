from django.contrib.auth.models import User
from django.db import models


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

    def __str__(self) -> str:
        return self.nombre_equipo


class Cancha(models.Model):
    estado = models.BooleanField(default=True, null=True)
    disponibilidad = models.CharField(max_length=100, blank=True, null=True)
    precio_por_hora = models.FloatField(default=10000.0)

    def __str__(self) -> str:
        return f"Cancha {self.pk}"


class ProductoQuerySet(models.QuerySet):
    def disponibles(self):
        from django.utils import timezone
        today = timezone.now().date()
        from django.db.models import Q
        return self.filter(stock__gt=0).filter(
            Q(fecha_vencimiento__gte=today) | Q(fecha_vencimiento__isnull=True)
        )


class Producto(models.Model):
    nombre = models.CharField(max_length=100)
    precio = models.FloatField()
    stock = models.IntegerField()
    fecha_vencimiento = models.DateField(blank=True, null=True)
    imagen = models.ImageField(upload_to="productos", blank=True, null=True)

    objects = ProductoQuerySet.as_manager()

    def __str__(self) -> str:
        return self.nombre

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
                Novedad.objects.create(
                    producto=p,
                    tipo_novedad=Novedad.TIPO_VENCIDO,
                    cantidad=p.stock,
                    descripcion="Stock caducado automáticamente"
                )
        
        vencidos.update(stock=0)


class ApartadoQuerySet(models.QuerySet):
    def pendientes(self):
        return self.filter(estado="pendiente")

    def comprados(self):
        return self.filter(estado="comprado")


class Apartado(models.Model):
    ESTADO_PENDIENTE = "pendiente"
    ESTADO_COMPRADO = "comprado"

    ESTADO_CHOICES = [
        (ESTADO_PENDIENTE, "Pendiente"),
        (ESTADO_COMPRADO, "Comprado"),
    ]

    persona = models.ForeignKey(
        Persona, on_delete=models.CASCADE, related_name="apartados"
    )
    producto = models.ForeignKey(
        Producto, on_delete=models.CASCADE, related_name="apartados"
    )
    cantidad = models.IntegerField()
    fecha_apartado = models.DateTimeField(auto_now_add=True)
    estado = models.CharField(
        max_length=20, choices=ESTADO_CHOICES, default=ESTADO_PENDIENTE
    )

    objects = ApartadoQuerySet.as_manager()

    def __str__(self) -> str:
        return f"Apartado #{self.pk} - {self.persona} - {self.producto}"


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
    jugador = models.ForeignKey(
        Jugador, on_delete=models.CASCADE, related_name="jugador_equipos"
    )
    equipo = models.ForeignKey(
        Equipo, on_delete=models.CASCADE, related_name="equipo_jugadores"
    )
    es_capitan = models.BooleanField(default=False)

    class Meta:
        unique_together = ("jugador", "equipo")

    def __str__(self) -> str:
        return f"{self.jugador} en {self.equipo}"


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
    hora_inicio = models.DateTimeField(blank=True, null=True)
    hora_fin = models.DateTimeField(blank=True, null=True)
    pago_cancha = models.BooleanField(default=False)

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

    TIPO_CHOICES = [
        (TIPO_AGREGADO, "Agregado / Ingreso"),
        (TIPO_VENDIDO, "Vendido / Apartado"),
        (TIPO_VENCIDO, "Vencido / Retirado"),
    ]

    producto = models.ForeignKey(
        Producto, on_delete=models.CASCADE, related_name="novedades"
    )
    tipo_novedad = models.CharField(
        max_length=20, choices=TIPO_CHOICES, default=TIPO_AGREGADO
    )
    cantidad = models.IntegerField()
    fecha = models.DateTimeField(auto_now_add=True)
    descripcion = models.CharField(max_length=255, blank=True, null=True)

    def __str__(self) -> str:
        return f"Novedad: {self.get_tipo_novedad_display()} - {self.producto.nombre} ({self.cantidad})"
