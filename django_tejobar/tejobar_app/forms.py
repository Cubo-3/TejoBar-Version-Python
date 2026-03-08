from django import forms
from django.contrib.auth.forms import AuthenticationForm
from django.contrib.auth.models import User

from .models import Persona, Producto, Equipo, Partido, Cancha, Cancha


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

class PartidoForm(forms.ModelForm):
    class Meta:
        model = Partido
        fields = ["equipo1", "equipo2", "cancha", "fecha", "hora", "estado"]
        widgets = {
            'fecha': forms.DateInput(attrs={'type': 'date', 'class': 'form-control'}),
            'hora': forms.TimeInput(attrs={'type': 'time', 'class': 'form-control'}),
            'equipo1': forms.Select(attrs={'class': 'form-control'}),
            'equipo2': forms.Select(attrs={'class': 'form-control'}),
            'cancha': forms.Select(attrs={'class': 'form-control'}),
            'estado': forms.Select(attrs={'class': 'form-control'}),
        }
    
    def clean(self):
        cleaned_data = super().clean()
        equipo1 = cleaned_data.get("equipo1")
        equipo2 = cleaned_data.get("equipo2")
        if equipo1 and equipo2 and equipo1 == equipo2:
            raise forms.ValidationError("Un equipo no puede jugar contra sí mismo.")
        return cleaned_data


class CanchaForm(forms.ModelForm):
    class Meta:
        model = Cancha
        fields = ["disponibilidad", "estado"]
        labels = {
            "disponibilidad": "Nombre o Disponibilidad de Cancha",
            "estado": "Cancha Funcional/Activa"
        }
        widgets = {
            'disponibilidad': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Ej. Cancha 1, Cancha Roja...'}),
            'estado': forms.CheckboxInput(attrs={'class': 'form-check-input ml-2'}),
        }


class CanchaForm(forms.ModelForm):
    class Meta:
        model = Cancha
        fields = ["disponibilidad", "estado"]
        labels = {
            "disponibilidad": "Nombre o Disponibilidad de Cancha",
            "estado": "Cancha Funcional/Activa"
        }
        widgets = {
            'disponibilidad': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Ej. Cancha 1, Cancha Roja...'}),
            'estado': forms.CheckboxInput(attrs={'class': 'form-check-input ml-2'}),
        }

