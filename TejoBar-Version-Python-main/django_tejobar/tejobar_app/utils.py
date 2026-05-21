from django.template.loader import render_to_string
import xhtml2pdf.pisa as pisa
from io import BytesIO
from django.http import HttpResponse
from django.utils import timezone

def generar_pdf(context_data, template_name, filename_prefix="reporte"):
    """
    Función base reutilizable para generar archivos PDF asegurando que no haya duplicación de código.
    Preparada para escalar (ej. soporte a Excel en el futuro usando pandas/openpyxl con una función hermana).
    """
    html_string = render_to_string(template_name, context_data)
    result = BytesIO()
    pdf = pisa.pisaDocument(BytesIO(html_string.encode("UTF-8")), result)
    
    if not pdf.err:
        response = HttpResponse(result.getvalue(), content_type='application/pdf')
        filename = f"{filename_prefix}_{timezone.now().strftime('%Y%m%d_%H%M')}.pdf"
        response['Content-Disposition'] = f'attachment; filename="{filename}"'
        return response
    return HttpResponse("Error Renderizando PDF", status=400)
