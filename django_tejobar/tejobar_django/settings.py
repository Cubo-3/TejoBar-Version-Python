import os
from pathlib import Path
from urllib.parse import quote, unquote, urlparse
from dotenv import load_dotenv
# pyrefly: ignore [missing-import]
import dj_database_url

BASE_DIR = Path(__file__).resolve().parent.parent

# Cargar variables de entorno desde el archivo .env
load_dotenv(BASE_DIR / '.env')


def _env(name: str) -> str:
    value = os.getenv(name, "")
    return value.strip().strip('"').strip("'")


def _resolve_cloudinary_config():
    cloudinary_url = _env("CLOUDINARY_URL")
    cloud_name = _env("CLOUDINARY_CLOUD_NAME")
    api_key = _env("CLOUDINARY_API_KEY")
    api_secret = _env("CLOUDINARY_API_SECRET")

    if cloudinary_url and not cloudinary_url.startswith("cloudinary://"):
        if "@" not in cloudinary_url and "://" not in cloudinary_url:
            if not cloud_name:
                cloud_name = cloudinary_url
            cloudinary_url = ""

    if not cloudinary_url and cloud_name and api_key and api_secret:
        cloudinary_url = (
            f"cloudinary://{quote(api_key, safe='')}:{quote(api_secret, safe='')}@{cloud_name}"
        )

    if not cloudinary_url.startswith("cloudinary://"):
        os.environ.pop("CLOUDINARY_URL", None)
        return False, "", {}

    parsed = urlparse(cloudinary_url)
    cloud_name = cloud_name or unquote(parsed.hostname or "")
    api_key = api_key or unquote(parsed.username or "")
    api_secret = api_secret or unquote(parsed.password or "")

    if not (cloud_name and api_key and api_secret):
        os.environ.pop("CLOUDINARY_URL", None)
        return False, "", {}

    cloudinary_url = (
        f"cloudinary://{quote(api_key, safe='')}:{quote(api_secret, safe='')}@{cloud_name}"
    )
    os.environ["CLOUDINARY_URL"] = cloudinary_url
    storage = {
        "CLOUD_NAME": cloud_name,
        "API_KEY": api_key,
        "API_SECRET": api_secret,
        "SECURE": True,
        "MEDIA_TAG": "tejobar_media",
    }
    return True, cloudinary_url, storage


USE_CLOUDINARY, CLOUDINARY_URL, CLOUDINARY_STORAGE = _resolve_cloudinary_config()
RAILWAY_VOLUME_MOUNT_PATH = _env("RAILWAY_VOLUME_MOUNT_PATH")

SECRET_KEY = os.getenv("DJANGO_SECRET_KEY", "django-insecure-tejobar-dev-secret-key-no-uso-produccion")

DEBUG = os.getenv("RAILWAY_ENVIRONMENT") is None

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

if USE_CLOUDINARY:
    staticfiles_index = INSTALLED_APPS.index("django.contrib.staticfiles")
    INSTALLED_APPS.insert(staticfiles_index, "cloudinary_storage")
    INSTALLED_APPS.append("cloudinary")

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
                "tejobar_app.context_processors.admin_notifications",
                "tejobar_app.context_processors.cloudinary_status",
            ],
        },
    },
]

WSGI_APPLICATION = "tejobar_django.wsgi.application"
ASGI_APPLICATION = "tejobar_django.asgi.application"

import sys

use_sqlite = 'test' in sys.argv or (not os.getenv("DATABASE_URL") and not os.getenv("MYSQL_URL") and not os.getenv("MYSQLDATABASE") and not os.getenv("MYSQLHOST"))

if use_sqlite:
    DATABASES = {
        'default': {
            'ENGINE': 'django.db.backends.sqlite3',
            'NAME': BASE_DIR / 'db.sqlite3',
        }
    }
else:
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
if db_url and not use_sqlite:
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
STATIC_ROOT = BASE_DIR / "staticfiles"

# Sirve CSS/JS desde static/ aunque collectstatic falle en el deploy (Railpack).
WHITENOISE_USE_FINDERS = True
WHITENOISE_MAX_AGE = 60 * 60 * 24 * 365

MEDIA_URL = "/media/"
if RAILWAY_VOLUME_MOUNT_PATH:
    MEDIA_ROOT = Path(RAILWAY_VOLUME_MOUNT_PATH) / "media"
else:
    MEDIA_ROOT = BASE_DIR / "media"

# Railway borra el disco local en cada deploy; Cloudinary persiste las fotos subidas.
if USE_CLOUDINARY:
    STORAGES = {
        "default": {
            "BACKEND": "cloudinary_storage.storage.MediaCloudinaryStorage",
        },
        "staticfiles": {
            "BACKEND": "django.contrib.staticfiles.storage.StaticFilesStorage",
        },
    }
else:
    STORAGES = {
        "default": {
            "BACKEND": "django.core.files.storage.FileSystemStorage",
            "OPTIONS": {
                "location": MEDIA_ROOT,
                "base_url": MEDIA_URL,
            },
        },
        "staticfiles": {
            "BACKEND": "django.contrib.staticfiles.storage.StaticFilesStorage",
        },
    }

DEFAULT_AUTO_FIELD = "django.db.models.BigAutoField"

if USE_CLOUDINARY:
    import cloudinary

    cloudinary.config(
        cloud_name=CLOUDINARY_STORAGE["CLOUD_NAME"],
        api_key=CLOUDINARY_STORAGE["API_KEY"],
        api_secret=CLOUDINARY_STORAGE["API_SECRET"],
        secure=True,
    )

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