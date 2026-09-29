#!/usr/bin/env python3
"""Sube WordPress a InfinityFree por FTP.

Las credenciales NO se guardan en ningún archivo: se leen de variables de entorno
de la sesión o se piden por teclado.
    FTP_HOST (ftpupload.net), FTP_USER, FTP_PASS, y opcionalmente FTP_DIR (htdocs)
Uso: python3 subir_ftp.py [carpeta_origen]   (por defecto ../wp)
"""
import getpass, os, sys, ftplib

origen = os.path.abspath(sys.argv[1] if len(sys.argv) > 1 else os.path.join(os.path.dirname(__file__), '..', 'wp'))
host = os.environ.get('FTP_HOST') or input('Host FTP: ').strip()
user = os.environ.get('FTP_USER') or input('Usuario FTP: ').strip()
pwd = os.environ.get('FTP_PASS') or getpass.getpass('Contraseña FTP: ')
destino = os.environ.get('FTP_DIR', 'htdocs')
OMITIR_TEMAS = ('twentytwenty',)

ftp = ftplib.FTP(host, timeout=60)
ftp.login(user, pwd)
ftp.set_pasv(True)
print('Conectado:', ftp.getwelcome())
ftp.cwd(destino)

def asegurar_dir(nombre):
    try:
        ftp.mkd(nombre)
    except ftplib.error_perm:
        pass  # ya existe

total = 0
for raiz, dirs, archivos in os.walk(origen):
    rel = os.path.relpath(raiz, origen)
    if any(seg.startswith(OMITIR_TEMAS) for seg in rel.split(os.sep)):
        dirs[:] = []
        continue
    ftp.cwd('/' + destino)
    if rel != '.':
        for seg in rel.split(os.sep):
            asegurar_dir(seg)
            ftp.cwd(seg)
    for a in archivos:
        if a == 'wp-config.php':
            continue  # se sube aparte, con los datos remotos
        with open(os.path.join(raiz, a), 'rb') as f:
            ftp.storbinary('STOR ' + a, f)
        total += 1
        if total % 100 == 0:
            print(total, 'archivos...')
print('Listo:', total, 'archivos subidos a', destino)
ftp.quit()
