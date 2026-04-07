from django.core.exceptions import ValidationError
from django.contrib.auth.hashers import check_password

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
