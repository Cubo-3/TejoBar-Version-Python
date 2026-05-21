import pymysql
try:
    conn = pymysql.connect(host='127.0.0.1', user='root', password='', port=3306)
    print("Connected successfully!")
    with conn.cursor() as cursor:
        cursor.execute("SHOW DATABASES;")
        for db in cursor.fetchall():
            print(db)
    conn.close()
except Exception as e:
    print(f"Error: {e}")
