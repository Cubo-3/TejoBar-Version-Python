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
    
    from .models import Notificacion
    
    context = {
        'notificaciones_vencimiento': [],
        'notificaciones_pagos': [],
        'notificaciones_personales': [],
        'total_notificaciones': 0
    }
    
    is_admin = hasattr(request.user, 'persona') and request.user.persona.rol == 'admin' or request.user.is_superuser
    
    if is_admin:
        # Lógica anterior de inventario (vencimientos) - opcional mantenerla
        dismissed = request.session.get('dismissed_notifications', [])
        if not isinstance(dismissed, list):
            dismissed = []
            
        hoy = timezone.now().date()
        limite_vencimiento = hoy + timedelta(days=14)
        
        productos_por_vencer_qs = Producto.objects.filter(
            stock__gt=0,
            fecha_vencimiento__lte=limite_vencimiento
        ).order_by('fecha_vencimiento')
        
        context['notificaciones_vencimiento'] = [
            p for p in productos_por_vencer_qs
            if f"vencimiento_{p.id}" not in dismissed
        ]
        
        # Nuevas notificaciones de Admin (usuario=None o usuario=request.user)
        notifs_admin = Notificacion.objects.filter(
            usuario__isnull=True, 
            leida=False
        ) | Notificacion.objects.filter(
            usuario=request.user, 
            leida=False
        )
        context['notificaciones_personales'] = notifs_admin.order_by('-fecha_creacion')[:15]
    else:
        # Es jugador
        notifs_jugador = Notificacion.objects.filter(
            usuario=request.user,
            leida=False
        ).order_by('-fecha_creacion')[:15]
        context['notificaciones_personales'] = notifs_jugador
        
    context['total_notificaciones'] = len(context['notificaciones_vencimiento']) + len(context['notificaciones_personales'])
    
    return context
