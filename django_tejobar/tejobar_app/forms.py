from django import forms
from django.contrib.auth.forms import AuthenticationForm
from django.contrib.auth.models import User

from .models import Persona, Producto, Equipo, Partido, Cancha, Categoria


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


class CategoriaForm(forms.ModelForm):
    class Meta:
        model = Categoria
        fields = ["nombre", "descripcion", "estado"]
        widgets = {
            'nombre': forms.TextInput(attrs={'class': 'form-control', 'placeholder': 'Ej. Bebidas alcohólicas'}),
            'descripcion': forms.Textarea(attrs={'class': 'form-control', 'rows': 3, 'placeholder': 'Opcional'}),
            'estado': forms.CheckboxInput(attrs={'class': 'form-check-input'}),
        }


class ProductoForm(forms.ModelForm):
    class Meta:
        model = Producto
        fields = ["nombre", "precio", "stock", "fecha_vencimiento", "imagen", "categoria"]
        widgets = {
            'categoria': forms.Select(attrs={'class': 'form-control'}),
        }
        
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        # Solo mostrar categorías activas en el dropdown
        self.fields['categoria'].queryset = Categoria.objects.filter(estado=True)


class EquipoForm(forms.ModelForm):
    usuarios_registrados = forms.ModelMultipleChoiceField(
        queryset=None,
        widget=forms.SelectMultiple(attrs={'class': 'form-control'}),
        required=False,
        label="Jugadores Registrados (Opcional)",
        help_text="Mantén presionada la tecla Ctrl (o Command en Mac) para seleccionar múltiples usuarios"
    )

    class Meta:
        model = Equipo
        fields = ["nombre_equipo", "usuarios_registrados"]

    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        from .models import Jugador, JugadorEquipo
        # Include active users
        self.fields['usuarios_registrados'].queryset = Jugador.objects.filter(
            estado=True, persona__user__is_active=True
        )
        # If editing an existing team, prepopulate the field
        if self.instance and self.instance.pk:
            self.initial['usuarios_registrados'] = self.instance.equipo_jugadores.filter(
                tipo_usuario=JugadorEquipo.TIPO_REGISTRADO, jugador__isnull=False
            ).values_list('jugador', flat=True)

class PartidoForm(forms.ModelForm):
    class Meta:
        model = Partido
        fields = ["equipo1", "equipo2", "cancha", "fecha", "hora", "hora_reserva_fin"]
        labels = {
            'hora': 'Hora de Inicio',
            'hora_reserva_fin': 'Hora Final (Opcional)',
        }
        widgets = {
            'fecha': forms.DateInput(attrs={'type': 'date', 'class': 'form-control'}),
            'hora': forms.TimeInput(attrs={'type': 'time', 'class': 'form-control'}),
            'hora_reserva_fin': forms.TimeInput(attrs={'type': 'time', 'class': 'form-control'}),
            'equipo1': forms.Select(attrs={'class': 'form-control'}),
            'equipo2': forms.Select(attrs={'class': 'form-control'}),
            'cancha': forms.Select(attrs={'class': 'form-control'}),
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


class JugadorEquipoForm(forms.ModelForm):
    class Meta:
        from .models import JugadorEquipo
        model = JugadorEquipo
        fields = ["tipo_usuario", "jugador", "nombre_invitado", "telefono_invitado", "correo_invitado"]
        labels = {
            "tipo_usuario": "Tipo de Jugador",
            "jugador": "Seleccionar Jugador Registrado",
            "nombre_invitado": "Nombre del Invitado",
            "telefono_invitado": "Teléfono del Invitado (Opcional)",
            "correo_invitado": "Correo del Invitado (Opcional)",
        }
        widgets = {
            'tipo_usuario': forms.Select(attrs={'class': 'form-control', 'id': 'tipo_usuario_select'}),
            'jugador': forms.Select(attrs={'class': 'form-control', 'id': 'jugador_select'}),
            'nombre_invitado': forms.TextInput(attrs={'class': 'form-control', 'id': 'nombre_invitado_input'}),
            'telefono_invitado': forms.TextInput(attrs={'class': 'form-control', 'id': 'telefono_invitado_input'}),
            'correo_invitado': forms.EmailInput(attrs={'class': 'form-control', 'id': 'correo_invitado_input'}),
        }

    def __init__(self, *args, **kwargs):
        equipo = kwargs.pop('equipo', None)
        super().__init__(*args, **kwargs)
        from .models import Jugador
        # Filter out players already in the team
        if equipo:
            existing_players = equipo.equipo_jugadores.filter(jugador__isnull=False).values_list('jugador_id', flat=True)
            self.fields['jugador'].queryset = Jugador.objects.exclude(persona_id__in=existing_players)
        else:
            self.fields['jugador'].queryset = Jugador.objects.all()

    def clean(self):
        cleaned_data = super().clean()
        tipo = cleaned_data.get("tipo_usuario")
        jugador = cleaned_data.get("jugador")
        nombre_invitado = cleaned_data.get("nombre_invitado")

        if tipo == 'registrado' and not jugador:
            self.add_error('jugador', 'Debe seleccionar un jugador registrado.')
        elif tipo == 'invitado' and not nombre_invitado:
            self.add_error('nombre_invitado', 'Debe ingresar el nombre del invitado.')
        
        return cleaned_data

