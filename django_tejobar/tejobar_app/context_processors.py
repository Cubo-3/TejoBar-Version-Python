from datetime import timedelta
from django.utils import timezone
from .models import Producto, Novedad

from django.conf import settings

from .media_utils import cloudinary_status_message


def cloudinary_status(request):
    return {
        "use_cloudinary": settings.USE_CLOUDINARY,
        "cloudinary_status_message": cloudinary_status_message(),
    }


def admin_notifications(request):
    if not request.user.is_authenticated:
        return {}
    
    # Check if the user is an admin
    if not hasattr(request.user, 'persona') or request.user.persona.rol != 'admin':
        return {}
        
    dismissed = request.session.get('dismissed_notifications', [])
    if not isinstance(dismissed, list):
        dismissed = []
    print("SESSION DISMISSED NOTIFS IN CONTEXT PROCESSOR:", dismissed)
        
    hoy = timezone.now().date()
    limite_vencimiento = hoy + timedelta(days=14)
    
    # 1. Productos próximos a vencer (o ya vencidos que aún tengan stock)
    productos_por_vencer_qs = Producto.objects.filter(
        stock__gt=0,
        fecha_vencimiento__lte=limite_vencimiento
    ).order_by('fecha_vencimiento')
    
    # Filter out dismissed vencimiento notifications
    productos_por_vencer = [
        p for p in productos_por_vencer_qs
        if f"vencimiento_{p.id}" not in dismissed
    ]
    
    # 2. Pagos recientes (últimas 48 horas)
    limite_fecha_pagos = timezone.now() - timedelta(hours=48)
    pagos_recientes_qs = Novedad.objects.filter(
        tipo_novedad__in=[Novedad.TIPO_VENDIDO, Novedad.TIPO_CANCHA],
        fecha__gte=limite_fecha_pagos
    ).order_by('-fecha')[:10]
    
    # Filter out dismissed payment notifications
    pagos_recientes = [
        pago for pago in pagos_recientes_qs
        if f"pago_{pago.id}" not in dismissed
    ]
    
    total_notificaciones = len(productos_por_vencer) + len(pagos_recientes)
    
    return {
        'notificaciones_vencimiento': productos_por_vencer,
        'notificaciones_pagos': pagos_recientes,
        'total_notificaciones': total_notificaciones
    }
