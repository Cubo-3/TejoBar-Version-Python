import pymysql

# Patch version so Django's mysqlclient >= 2.2.1 check passes
pymysql.version_info = (2, 2, 1, "final", 0)
pymysql.__version__ = "2.2.1"

pymysql.install_as_MySQLdb()
