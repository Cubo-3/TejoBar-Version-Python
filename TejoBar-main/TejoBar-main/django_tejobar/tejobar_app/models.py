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
        cls.objects.filter(
            fecha_vencimiento__lt=today, 
            stock__gt=0
        ).update(stock=0)


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
    capitan = models.CharField(max_length=100)
    cancha = models.ForeignKey(Cancha, on_delete=models.CASCADE, related_name="partidos")
    estado = models.CharField(
        max_length=20, choices=ESTADO_CHOICES, default=ESTADO_PENDIENTE
    )

    def __str__(self) -> str:
        return f"Partido #{self.pk} - {self.fecha} {self.hora}"


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

