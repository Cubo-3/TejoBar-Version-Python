import os
import pymysql

# 1. Leer las variables de entorno que Railway le da a tu app
host = os.getenv("MYSQLHOST")
user = os.getenv("MYSQLUSER")
password = os.getenv("MYSQLPASSWORD")
database = os.getenv("MYSQLDATABASE")
port = int(os.getenv("MYSQLPORT", 3306))

print("Conectando a la base de datos de Railway...")

# 2. Conectarse a MySQL
conn = pymysql.connect(
    host=host,
    user=user,
    password=password,
    database=database,
    port=port,
    client_flag=pymysql.constants.CLIENT.MULTI_STATEMENTS # Permite ejecutar todo el .sql junto
)

# 3. Leer el archivo .sql y ejecutarlo
try:
    with conn.cursor() as cursor:
        print("Leyendo archivo tejobar_db.sql...")
        with open("tejobar_db.sql", "r", encoding="utf-8") as f:
            sql_script = f.read()
        
        print("Insertando tablas y datos en Railway (esto puede tardar unos segundos)...")
        cursor.execute(sql_script)
    conn.commit()
    print("¡Base de datos importada con éxito en Railway!")
finally:
    conn.close()