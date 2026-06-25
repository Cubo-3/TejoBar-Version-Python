import logging
from urllib.parse import urlparse

from django.conf import settings

logger = logging.getLogger(__name__)

PRODUCTO_IMAGEN_PLACEHOLDER = (
    "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='640' height='480' "
    "viewBox='0 0 640 480'%3E%3Crect width='640' height='480' fill='%231a1a24'/%3E"
    "%3Crect x='48' y='48' width='544' height='384' rx='24' fill='%23252530' "
    "stroke='%23ff7b00' stroke-width='4'/%3E%3Ctext x='320' y='250' text-anchor='middle' "
    "fill='%23ff7b00' font-family='Arial' font-size='28'%3ESin imagen%3C/text%3E%3C/svg%3E"
)


def producto_imagen_url(producto) -> str:
    imagen = getattr(producto, "imagen", None)
    if not imagen:
        return PRODUCTO_IMAGEN_PLACEHOLDER

    try:
        url = imagen.url
        if url.startswith("http://") or url.startswith("https://"):
            return url
        if imagen.name and imagen.storage.exists(imagen.name):
            return url
    except Exception:
        logger.exception("No se pudo resolver la imagen del producto %s", getattr(producto, "pk", "?"))

    return PRODUCTO_IMAGEN_PLACEHOLDER


def save_producto_imagen(producto, uploaded_file) -> None:
    if not uploaded_file:
        return

    if producto.imagen:
        try:
            producto.imagen.delete(save=False)
        except Exception:
            logger.exception("No se pudo eliminar la imagen anterior del producto %s", producto.pk)

    producto.imagen = uploaded_file
    producto.save(update_fields=["imagen"])


def apply_producto_imagen(producto, uploaded_file):
    if uploaded_file:
        producto.imagen = uploaded_file

def cloudinary_status_message() -> str:
    if not settings.USE_CLOUDINARY:
        return (
            "Cloudinary no está activo. Configura CLOUDINARY_CLOUD_NAME, "
            "CLOUDINARY_API_KEY y CLOUDINARY_API_SECRET en Railway."
        )

    storage = getattr(settings, "CLOUDINARY_STORAGE", {})
    cloud_name = storage.get("CLOUD_NAME") or urlparse(settings.CLOUDINARY_URL).hostname
    return f"Cloudinary activo (cloud: {cloud_name})."
