import pandas as pd
from django.db import transaction
from django.core.exceptions import ValidationError
from tejobar_app.models import Producto, Categoria

def procesar_archivo_productos(archivo):
    resumen = {
        'creados': 0,
        'actualizados': 0,
        'errores': []
    }

    try:
        if archivo.name.endswith('.csv'):
            df = pd.read_csv(archivo, encoding='utf-8')
        else:
            df = pd.read_excel(archivo)
    except Exception as e:
        resumen['errores'].append(f"Error crítico al leer el archivo. Verifica el formato. Detalle: {str(e)}")
        return resumen

    # Validar que existan las columnas mínimas esperadas (case-insensitive)
    df.columns = df.columns.str.lower()
    columnas_requeridas = {'nombre', 'precio', 'stock', 'categoria'}
    
    if not columnas_requeridas.issubset(set(df.columns)):
        resumen['errores'].append(f"Faltan columnas requeridas. El archivo debe contener exactamente: {', '.join(columnas_requeridas)}")
        return resumen

    # OBTENER CAMPOS DEL MODELO DE FORMA DINÁMICA
    campos_modelo = {f.name for f in Producto._meta.get_fields()}
    columnas_csv = set(df.columns)
    
    # Reportar columnas ignoradas (advertencias)
    columnas_ignoradas = columnas_csv - campos_modelo - {'categoria'} # categoria en CSV es nombre, en DB es id
    if columnas_ignoradas:
        resumen['errores'].append(f"ADVERTENCIA: Las siguientes columnas fueron ignoradas por no existir en el sistema: {', '.join(columnas_ignoradas)}")

    for index, row in df.iterrows():
        try:
            with transaction.atomic():
                nombre = str(row.get('nombre', '')).strip()
                if not nombre or pd.isna(nombre) or nombre == 'nan':
                    raise ValueError("El nombre no puede estar vacío.")

                precio = float(row.get('precio', 0))
                stock = int(row.get('stock', 0))
                
                nombre_categoria = str(row.get('categoria', '')).strip()

                categoria_obj, cat_creada = Categoria.objects.get_or_create(
                    nombre=nombre_categoria,
                    defaults={'descripcion': 'Categoría autogenerada por carga masiva'}
                )
                
                # Preparar diccionario de kwargs para cualquier otra columna opcional existente
                kwargs = {}
                for col in df.columns:
                    if col in campos_modelo and col not in columnas_requeridas:
                        val = row.get(col)
                        kwargs[col] = val if not pd.isna(val) else None

                producto_obj = Producto.objects.filter(nombre=nombre).first()
                if producto_obj:
                    # Actualizar básicos
                    producto_obj.precio = precio
                    producto_obj.stock = stock
                    producto_obj.categoria = categoria_obj
                    # Actualizar dinámicos
                    for k, v in kwargs.items():
                        setattr(producto_obj, k, v)
                    accion = 'actualizado'
                else:
                    # Crear nuevo
                    producto_obj = Producto(
                        nombre=nombre,
                        precio=precio,
                        stock=stock,
                        categoria=categoria_obj,
                        **kwargs
                    )
                    accion = 'creado'

                producto_obj.full_clean()
                producto_obj.save()

                if accion == 'creado':
                    resumen['creados'] += 1
                else:
                    resumen['actualizados'] += 1

        except ValidationError as e:
            # Captura validaciones de MinValue/MaxValue del modelo
            resumen['errores'].append(f"Fila {index + 2} ({row.get('nombre', 'Desconocido')}): {e.messages[0] if hasattr(e, 'messages') else str(e)}")
        except Exception as e:
            resumen['errores'].append(f"Fila {index + 2} ({row.get('nombre', 'Desconocido')}): {str(e)}")

    return resumen
