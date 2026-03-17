from django.contrib import admin
from .models import Partido, Equipo, Cancha, Torneo, Categoria

@admin.register(Partido)
class PartidoAdmin(admin.ModelAdmin):
    list_display = ('id', 'fecha', 'hora', 'equipo1', 'equipo2', 'cancha', 'estado')
    list_filter = ('estado', 'fecha', 'cancha')
    search_fields = ('equipo1__nombre_equipo', 'equipo2__nombre_equipo')

admin.site.register(Equipo)
admin.site.register(Cancha)
admin.site.register(Torneo)
admin.site.register(Categoria) 