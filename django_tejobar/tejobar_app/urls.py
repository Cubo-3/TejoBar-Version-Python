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
    path("dashboard/reporte-pdf/", views.dashboard_reporte_pdf, name="dashboard_reporte_pdf"),
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
    path("equipos/<int:pk>/agregar-miembro/", views.equipo_add_member, name="equipos_add_member"),
    path("equipos/<int:pk>/expulsar/<int:jugador_pk>/", views.equipo_remove_member, name="equipos_remove_member"),
    # Partidos
    path("partidos/", views.partido_list, name="partidos_index"),
    path("api/disponibilidad-partido/", views.api_disponibilidad_partido, name="api_disponibilidad_partido"),

    # Carrito
    path("carrito/editar/<int:pk>/", views.editar_item_carrito, name="carrito_editar"),
    path("carrito/eliminar/<int:pk>/", views.eliminar_item_carrito, name="carrito_eliminar"),

    # Admin Apartados Manual
    path("administracion/apartados/<int:pk>/pagar-efectivo/", views.admin_apartado_pagar_efectivo, name="admin_apartado_pagar_efectivo"),
    path("administracion/apartados/<int:pk>/cancelar/", views.admin_apartado_cancelar, name="admin_apartado_cancelar"),
    path("administracion/historial/<int:pk>/despachar/", views.admin_despachar_pedido, name="admin_despachar_pedido"),

    # MercadoPago Pagos
    path("pago/crear/carrito/", views.crear_preferencia_carrito, name="pago_crear_carrito"),
    path("pago/crear/apartado/<int:pk>/", views.crear_preferencia_apartado, name="pago_crear_apartado"),
    path("pago/crear/cancha/<int:pk>/", views.crear_preferencia_cancha, name="pago_crear_cancha"),
    path("pago/exitoso/", views.pago_exitoso, name="pago_exitoso"),
    path("pago/fallido/", views.pago_fallido, name="pago_fallido"),
    path("pago/pendiente/", views.pago_pendiente, name="pago_pendiente"),

    # Categorías
    path("administracion/categorias/", views.admin_categorias_index, name="admin_categorias_index"),
    path("administracion/categorias/crear/", views.admin_categorias_create, name="admin_categorias_create"),
    path("administracion/categorias/<int:pk>/editar/", views.admin_categorias_update, name="admin_categorias_update"),
    path("administracion/categorias/<int:pk>/eliminar/", views.admin_categorias_delete, name="admin_categorias_delete"),
]

