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

# Servir archivos de media SIEMPRE (imágenes de productos subidas por usuarios).
# WhiteNoise NO sirve archivos de media, por eso se registra la ruta incluso en producción.
urlpatterns += static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)

