import django.core.validators
from django.db import migrations, models


def normalizar_nombres_cancha(apps, schema_editor):
    Cancha = apps.get_model("tejobar_app", "Cancha")
    usados = set()
    for cancha in Cancha.objects.all().order_by("id"):
        nombre = (cancha.disponibilidad or "").strip()
        if not nombre:
            nombre = f"Cancha {cancha.pk}"
        base = nombre[:50]
        nombre_final = base
        sufijo = 2
        clave = nombre_final.casefold()
        while clave in usados:
            sufijo_texto = f" ({sufijo})"
            nombre_final = f"{base[: 50 - len(sufijo_texto)]}{sufijo_texto}"
            clave = nombre_final.casefold()
            sufijo += 1
        usados.add(clave)
        if nombre_final != cancha.disponibilidad:
            cancha.disponibilidad = nombre_final
            cancha.save(update_fields=["disponibilidad"])


class Migration(migrations.Migration):

    dependencies = [
        ("tejobar_app", "0022_alter_producto_descripcion"),
    ]

    operations = [
        migrations.RunPython(normalizar_nombres_cancha, migrations.RunPython.noop),
        migrations.AlterField(
            model_name="cancha",
            name="disponibilidad",
            field=models.CharField(
                max_length=50,
                unique=True,
                validators=[
                    django.core.validators.MinLengthValidator(
                        2,
                        message="El nombre de la cancha debe tener al menos 2 caracteres.",
                    ),
                    django.core.validators.MaxLengthValidator(
                        50,
                        message="El nombre de la cancha no puede superar los 50 caracteres.",
                    ),
                ],
            ),
        ),
    ]
