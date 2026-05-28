from django.test import TestCase
from django.core.exceptions import ValidationError
from tejobar_app.models import Cancha
from tejobar_app.forms import CanchaForm

class CanchaTests(TestCase):
    def test_cancha_price_must_be_positive(self):
        # Cancha price must be greater than 0
        cancha = Cancha(disponibilidad="Cancha Test 1", precio_por_hora=0.0)
        with self.assertRaises(ValidationError):
            cancha.full_clean()

        cancha2 = Cancha(disponibilidad="Cancha Test 2", precio_por_hora=-10.0)
        with self.assertRaises(ValidationError):
            cancha2.full_clean()

        cancha3 = Cancha(disponibilidad="Cancha Test 3", precio_por_hora=100.0)
        # Should not raise exception
        cancha3.full_clean()

    def test_cancha_description_length_limit(self):
        # Description max length is 200
        desc_ok = "A" * 200
        cancha_ok = Cancha(disponibilidad="Cancha Test Ok", precio_por_hora=5000.0, descripcion=desc_ok)
        cancha_ok.full_clean()

        desc_bad = "A" * 201
        cancha_bad = Cancha(disponibilidad="Cancha Test Bad", precio_por_hora=5000.0, descripcion=desc_bad)
        with self.assertRaises(ValidationError):
            cancha_bad.full_clean()

    def test_cancha_case_insensitive_uniqueness(self):
        # Create first court
        cancha1 = Cancha.objects.create(disponibilidad="Cancha Especial", precio_por_hora=5000.0)
        
        # Test case-insensitive duplicate name
        cancha2 = Cancha(disponibilidad="cancha especial", precio_por_hora=6000.0)
        with self.assertRaises(ValidationError):
            cancha2.full_clean()

    def test_cancha_form_validation(self):
        # Valid data
        form_data = {
            "disponibilidad": "Cancha Nueva",
            "precio_por_hora": 9500.0,
            "descripcion": "Descripción de prueba",
            "estado": True
        }
        form = CanchaForm(data=form_data)
        self.assertTrue(form.is_valid())

        # Invalid price (<= 0)
        form_data["precio_por_hora"] = 0
        form = CanchaForm(data=form_data)
        self.assertFalse(form.is_valid())
        self.assertIn("precio_por_hora", form.errors)

        # Invalid description length (> 200)
        form_data["precio_por_hora"] = 9500.0
        form_data["descripcion"] = "A" * 201
        form = CanchaForm(data=form_data)
        self.assertFalse(form.is_valid())
        self.assertIn("descripcion", form.errors)



