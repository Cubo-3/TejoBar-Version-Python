from django.urls import path

from . import views

urlpatterns = [
    path("", views.home, name="home"),
    # Auth
    path("login/", views.login_view, name="login"),
    path("logout/", views.logout_view, name="logout"),
    path("register/", views.register_view, name="register"),
    # Catálogo
    path("productos/", views.product_list, name="productos_index"),
    path("productos/<int:pk>/", views.product_detail, name="productos_show"),
    path(
        "productos/<int:pk>/apartar/",
        views.apartar_producto,
        name="productos_apartar",
    ),
    # Dashboard básico
    path("dashboard/", views.dashboard, name="dashboard"),
    path("dashboard/historial/", views.dashboard_historial, name="dashboard_historial"),
    # CRUD básico personas / equipos
    path("personas/", views.persona_list, name="personas_index"),
    path("administracion/productos/", views.admin_product_list, name="admin_productos_index"),
    path("administracion/productos/crear/", views.admin_product_create, name="admin_productos_create"),
    path("administracion/productos/<int:pk>/editar/", views.admin_product_update, name="admin_productos_update"),
    path("administracion/productos/<int:pk>/eliminar/", views.admin_product_delete, name="admin_productos_delete"),
    path("administracion/novedades/", views.admin_novedades_index, name="admin_novedades_index"),
    path("administracion/partidos/", views.admin_partidos_index, name="admin_partidos_index"),
    path("administracion/partidos/crear/", views.admin_partidos_create, name="admin_partidos_create"),
    path("administracion/partidos/<int:pk>/editar/", views.admin_partidos_update, name="admin_partidos_update"),
    path("administracion/partidos/<int:pk>/eliminar/", views.admin_partidos_delete, name="admin_partidos_delete"),
    path("administracion/partidos/<int:pk>/iniciar/", views.iniciar_partido, name="admin_partidos_iniciar"),
    path("administracion/partidos/<int:pk>/finalizar/", views.finalizar_partido, name="admin_partidos_finalizar"),
    path("administracion/partidos/<int:pk>/pagar/", views.pagar_partido, name="admin_partidos_pagar"),
    path("administracion/canchas/", views.admin_canchas_index, name="admin_canchas_index"),
    path("administracion/canchas/crear/", views.admin_canchas_create, name="admin_canchas_create"),
    path("administracion/canchas/<int:pk>/editar/", views.admin_canchas_update, name="admin_canchas_update"),
    path("administracion/canchas/<int:pk>/eliminar/", views.admin_canchas_delete, name="admin_canchas_delete"),
    path("personas/crear/", views.persona_create, name="personas_create"),
    path("personas/<int:pk>/editar/", views.persona_update, name="personas_update"),
    path("personas/<int:pk>/eliminar/", views.persona_delete, name="personas_delete"),
    path("equipos/", views.equipo_list, name="equipos_index"),
    path("equipos/crear/", views.equipo_create, name="equipos_create"),
    path("equipos/<int:pk>/", views.equipo_detail, name="equipos_show"),
    path("equipos/<int:pk>/editar/", views.equipo_update, name="equipos_update"),
    path("equipos/<int:pk>/eliminar/", views.equipo_delete, name="equipos_delete"),
    path("equipos/<int:pk>/unirse/", views.equipo_join, name="equipos_join"),
    path("equipos/<int:pk>/salir/", views.equipo_leave, name="equipos_leave"),
    path("equipos/<int:pk>/expulsar/<int:jugador_pk>/", views.equipo_remove_member, name="equipos_remove_member"),
    # Partidos
    path("partidos/", views.partido_list, name="partidos_index"),
]

