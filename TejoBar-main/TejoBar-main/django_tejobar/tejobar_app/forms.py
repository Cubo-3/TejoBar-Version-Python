from django import forms
from django.contrib.auth.forms import AuthenticationForm
from django.contrib.auth.models import User

from .models import Persona, Producto, Equipo


class LoginForm(AuthenticationForm):
    username = forms.EmailField(label="Correo electrónico")

    def clean(self):
        # Usamos el email como username
        email = self.cleaned_data.get("username")
        if email:
            try:
                user = User.objects.get(email=email)
                self.cleaned_data["username"] = user.username
            except User.DoesNotExist:
                pass
        return super().clean()


class RegistroForm(forms.Form):
    nombre = forms.CharField(max_length=100)
    rol = forms.ChoiceField(
        choices=[
            ("jugador", "Jugador"),
            ("capitan", "Capitán"),
        ]
    )
    correo = forms.EmailField()
    numero = forms.CharField(max_length=20)
    password = forms.CharField(widget=forms.PasswordInput)
    password2 = forms.CharField(widget=forms.PasswordInput, label="Confirmar contraseña")

    def clean_correo(self):
        correo = self.cleaned_data["correo"]
        from django.db.models import Q
        if User.objects.filter(Q(email__iexact=correo) | Q(username__iexact=correo)).exists():
            raise forms.ValidationError("Ya existe un usuario con este correo.")
        return correo

    def clean(self):
        cleaned = super().clean()
        pwd1 = cleaned.get("password")
        pwd2 = cleaned.get("password2")
        if pwd1 and pwd2 and pwd1 != pwd2:
            raise forms.ValidationError("Las contraseñas no coinciden.")
        return cleaned


class PersonaForm(forms.ModelForm):
    class Meta:
        model = Persona
        fields = ["nombre", "correo", "numero", "rol"]


class ProductoForm(forms.ModelForm):
    class Meta:
        model = Producto
        fields = ["nombre", "precio", "stock", "fecha_vencimiento", "imagen"]


class EquipoForm(forms.ModelForm):
    class Meta:
        model = Equipo
        fields = ["nombre_equipo"]

