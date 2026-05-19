import re
from django.core.exceptions import ValidationError
from django.contrib.auth.hashers import check_password


class StrongPasswordValidator:
    """
    Valida que la contraseña cumpla con los requisitos de seguridad:
    - Mínimo 8 caracteres
    - Al menos una letra mayúscula
    - Al menos un número
    - Al menos un carácter especial (!@#$%^&*...)
    """

    def validate(self, password, user=None):
        errors = []

        if len(password) < 8:
            errors.append("La contraseña debe tener al menos 8 caracteres.")

        if not re.search(r'[A-Z]', password):
            errors.append("La contraseña debe contener al menos una letra mayúscula.")

        if not re.search(r'\d', password):
            errors.append("La contraseña debe contener al menos un número.")

        if not re.search(r'[!@#$%^&*()\-_=+\[\]{};:\'",.<>/?\\|`~]', password):
            errors.append("La contraseña debe contener al menos un carácter especial (!@#$%^&*...).")

        if errors:
            raise ValidationError(errors)

    def get_help_text(self):
        return (
            "Tu contraseña debe tener mínimo 8 caracteres, "
            "incluir al menos una mayúscula, un número y un carácter especial."
        )


class NotSameAsOldPasswordValidator:
    """
    Validator to prevent users from picking their current password as the new one.
    """
    def validate(self, password, user=None):
        if user and user.password:
            if check_password(password, user.password):
                raise ValidationError(
                    "La nueva contraseña no puede ser igual a la anterior.",
                    code="password_is_the_same",
                )

    def get_help_text(self):
        return "Tu nueva contraseña no puede ser igual a tu contraseña actual."
