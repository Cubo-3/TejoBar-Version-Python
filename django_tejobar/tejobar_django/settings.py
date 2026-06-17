import os
from pathlib import Path
# pyrefly: ignore [missing-import]
import dj_database_url




BASE_DIR = Path(__file__).resolve().parent.parent

SECRET_KEY = os.getenv("DJANGO_SECRET_KEY", "django-insecure-tejobar-dev-secret-key-no-uso-produccion")

DEBUG = True # TEMPORALMENTE EN TRUE PARA VER EL DETALLE DEL ERROR 500 EN EL NAVEGADOR

# CORREGIDO: Se cambió ":" por "="
ALLOWED_HOSTS = [
    "localhost",
    "127.0.0.1",
    ".railway.app",  # Permite cualquier subdominio asignado por Railway
]

CSRF_TRUSTED_ORIGINS = [
    'http://127.0.0.1:8000', 
    'http://localhost:8000', 
    'https://127.0.0.1:8000', 
    'https://localhost:8000', 
    "https://*.railway.app"
]

INSTALLED_APPS = [
    "django.contrib.admin",
    "django.contrib.auth",
    "django.contrib.contenttypes",
    "django.contrib.sessions",
    "django.contrib.messages",
    "django.contrib.staticfiles",
    "tejobar_app",
]

MIDDLEWARE = [
    "django.middleware.security.SecurityMiddleware",
    'whitenoise.middleware.WhiteNoiseMiddleware',
    "django.contrib.sessions.middleware.SessionMiddleware",
    "django.middleware.common.CommonMiddleware",
    "django.middleware.csrf.CsrfViewMiddleware",
    "django.contrib.auth.middleware.AuthenticationMiddleware",
    "django.contrib.messages.middleware.MessageMiddleware",
    "django.middleware.clickjacking.XFrameOptionsMiddleware",
]

ROOT_URLCONF = "tejobar_django.urls"

TEMPLATES = [
    {
        "BACKEND": "django.template.backends.django.DjangoTemplates",
        "DIRS": [BASE_DIR / "templates"],
        "APP_DIRS": True,
        "OPTIONS": {
            "context_processors": [
                "django.template.context_processors.debug",
                "django.template.context_processors.request",
                "django.contrib.auth.context_processors.auth",
                "django.contrib.messages.context_processors.messages",
            ],
        },
    },
]

WSGI_APPLICATION = "tejobar_django.wsgi.application"
ASGI_APPLICATION = "tejobar_django.asgi.application"

# CORREGIDO: Sintaxis limpia para que use la URL de Railway en la nube,
# o caiga en tu MySQL local si no encuentra la variable de entorno de producción.
DATABASES = {
    'default': {
        'ENGINE': 'django.db.backends.mysql',
        'NAME': os.getenv('MYSQLDATABASE', 'tejobar_db'),
        'USER': os.getenv('MYSQLUSER', 'root'),
        'PASSWORD': os.getenv('MYSQLPASSWORD', ''),
        'HOST': os.getenv('MYSQLHOST', 'localhost'),
        'PORT': os.getenv('MYSQLPORT', '3306'),
    }
}

# Uso de dj_database_url para Railway usando DATABASE_URL o MYSQL_URL (el que provea Railway)
db_url = os.getenv("DATABASE_URL") or os.getenv("MYSQL_URL")
if db_url:
    DATABASES['default'] = dj_database_url.parse(db_url, conn_max_age=600)

if DATABASES["default"]["ENGINE"] == "django.db.backends.mysql":
    DATABASES["default"].setdefault("OPTIONS", {})["init_command"] = "SET sql_mode='STRICT_TRANS_TABLES'"

AUTH_PASSWORD_VALIDATORS = [
    {
        "NAME": "django.contrib.auth.password_validation.UserAttributeSimilarityValidator",
    },
    {
        "NAME": "django.contrib.auth.password_validation.MinimumLengthValidator",
        "OPTIONS": {"min_length": 8},
    },
    {
        "NAME": "tejobar_app.validators.StrongPasswordValidator",
    },
    {
        "NAME": "tejobar_app.validators.NotSameAsOldPasswordValidator",
    },
]

LANGUAGE_CODE = "es-es"

TIME_ZONE = "America/Bogota"

USE_I18N = True

USE_TZ = True

STATIC_URL = "/static/"
STATICFILES_DIRS = [BASE_DIR / "static"]
STATIC_ROOT = BASE_DIR / 'staticfiles'

# Esto evita que el despliegue falle si hay alguna referencia rota en el CSS/Bootstrap
STATICFILES_STORAGE = 'whitenoise.storage.CompressedStaticFilesStorage'


MEDIA_URL = "/media/"
MEDIA_ROOT = BASE_DIR / "media"

DEFAULT_AUTO_FIELD = "django.db.models.BigAutoField"

LOGIN_URL = "tejobar_app:login"
LOGIN_REDIRECT_URL = "tejobar_app:dashboard"
LOGOUT_REDIRECT_URL = "tejobar_app:home"

# MercadoPago Settings (Sandbox/Test setup)
MERCADOPAGO_ACCESS_TOKEN = os.getenv("MERCADOPAGO_ACCESS_TOKEN", "tu_access_token_local_aqui")
MERCADOPAGO_PUBLIC_KEY = os.getenv("MERCADOPAGO_PUBLIC_KEY", "tu_public_key_local_aqui")

# Email Configuration für Gmail SMTP
EMAIL_BACKEND = 'django.core.mail.backends.smtp.EmailBackend'
EMAIL_HOST = os.getenv('EMAIL_HOST', 'smtp.gmail.com')
EMAIL_PORT = int(os.getenv('EMAIL_PORT', 587))
EMAIL_USE_TLS = os.getenv('EMAIL_USE_TLS', 'True') == 'True'
EMAIL_HOST_USER = os.getenv('EMAIL_HOST_USER', 'tu_correo_local@gmail.com')
EMAIL_HOST_PASSWORD = os.getenv('EMAIL_HOST_PASSWORD', 'tu_password_local')
DEFAULT_FROM_EMAIL = EMAIL_HOST_USER