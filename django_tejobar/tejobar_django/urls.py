from django.conf import settings
from django.conf.urls.static import static
from django.contrib import admin
from django.urls import include, path

urlpatterns = [
    path("admin/", admin.site.urls),
    path("accounts/", include("django.contrib.auth.urls")), # Password reset and other auth views
    path("", include(("tejobar_app.urls", "tejobar_app"), namespace="tejobar_app")),
]

# Servir archivos estáticos solo en DEBUG (WhiteNoise los maneja en producción)
if settings.DEBUG:
    urlpatterns += static(settings.STATIC_URL, document_root=settings.STATIC_ROOT)

# Servir media local solo cuando no se usa Cloudinary (p. ej. desarrollo o volumen en Railway).
if not settings.USE_CLOUDINARY:
    urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)

