from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ("tejobar_app", "0025_producto_activo"),
    ]

    operations = [
        migrations.AlterField(
            model_name="producto",
            name="imagen",
            field=models.ImageField(blank=True, max_length=255, null=True, upload_to="productos"),
        ),
    ]
