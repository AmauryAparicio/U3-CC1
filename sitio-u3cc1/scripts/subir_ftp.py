#!/usr/bin/env python3
"""Sube WordPress a InfinityFree por FTP.

Las credenciales NO se guardan en ningún archivo: se leen de variables de entorno
de la sesión o se piden por teclado.
    FTP_HOST (ftpupload.net), FTP_USER, FTP_PASS, y opcionalmente FTP_DIR (htdocs) y FTP_PORT (21)
Uso: python3 subir_ftp.py [carpeta_origen]   (por defecto ../wp)

Es resistente a cortes: si la conexión se cae a mitad de la subida, basta con
volver a ejecutar el script. Los archivos ya subidos (mismo tamaño en el
servidor) se saltan, así que solo sube lo que falta.
"""
import getpass, os, sys, ftplib, time

origen = os.path.abspath(sys.argv[1] if len(sys.argv) > 1 else os.path.join(os.path.dirname(__file__), '..', 'wp'))
host = os.environ.get('FTP_HOST') or input('Host FTP: ').strip()
port = int(os.environ.get('FTP_PORT', '21'))
user = os.environ.get('FTP_USER') or input('Usuario FTP: ').strip()
pwd = os.environ.get('FTP_PASS') or getpass.getpass('Contraseña FTP: ')
destino = os.environ.get('FTP_DIR', 'htdocs').strip('/')

# Carpetas/archivos que no hace falta subir: temas por defecto sin usar y el
# plugin Akismet (requiere una clave de API que no vamos a configurar).
OMITIR = ('twentytwenty', 'akismet')

def conectar():
    ftp = ftplib.FTP(timeout=60)
    ftp.connect(host, port)
    ftp.login(user, pwd)
    ftp.set_pasv(True)
    ftp.voidcmd('TYPE I')  # modo binario: sin esto, SIZE da resultados incorrectos en algunos servidores
    print('Conectado:', ftp.getwelcome())
    return ftp

def asegurar_dir(ftp, nombre):
    try:
        ftp.mkd(nombre)
    except ftplib.error_perm:
        pass  # ya existe

def tamano_remoto(ftp, nombre):
    try:
        return ftp.size(nombre)
    except ftplib.error_perm:
        return None

# Lista completa de archivos a subir, calculada una sola vez para poder
# mostrar avance y para poder retomar tras un corte de conexión.
archivos_a_subir = []
for raiz, dirs, archivos in os.walk(origen):
    rel = os.path.relpath(raiz, origen)
    partes = [] if rel == '.' else rel.split(os.sep)
    if any(seg.startswith(OMITIR) for seg in partes):
        dirs[:] = []
        continue
    for a in archivos:
        if a == 'wp-config.php':
            continue  # se sube aparte, con los datos remotos
        archivos_a_subir.append((partes, a, os.path.join(raiz, a)))

total = len(archivos_a_subir)
tam_total = sum(os.path.getsize(p) for _, _, p in archivos_a_subir)
print(f'{total} archivos por subir ({tam_total / 1024 / 1024:.1f} MB) a /{destino}')

ftp = conectar()
ftp.cwd('/' + destino)
dir_actual = []
subidos = 0
saltados = 0
inicio = time.time()

for partes, nombre, ruta_local in archivos_a_subir:
    intentos = 0
    while True:
        try:
            if partes != dir_actual:
                ftp.cwd('/' + destino)
                for seg in partes:
                    asegurar_dir(ftp, seg)
                    ftp.cwd(seg)
                dir_actual = partes

            tam_local = os.path.getsize(ruta_local)
            if tamano_remoto(ftp, nombre) == tam_local:
                saltados += 1
                break

            with open(ruta_local, 'rb') as f:
                ftp.storbinary('STOR ' + nombre, f)
            subidos += 1
            break
        except (ftplib.all_errors, OSError, EOFError) as e:
            intentos += 1
            if intentos > 5:
                print(f'\nERROR: no se pudo subir {"/".join(partes + [nombre])} tras 5 intentos: {e}')
                raise
            print(f'\nSe cortó la conexión ({e}); reconectando (intento {intentos}/5)...')
            time.sleep(3)
            try:
                ftp.quit()
            except Exception:
                pass
            ftp = conectar()
            ftp.cwd('/' + destino)
            dir_actual = []

    hechos = subidos + saltados
    if hechos % 50 == 0 or hechos == total:
        transcurrido = time.time() - inicio
        print(f'{hechos}/{total} ({subidos} subidos, {saltados} ya estaban) — {transcurrido:.0f}s', end='\r')

print(f'\nListo: {subidos} archivos subidos, {saltados} ya estaban en el servidor, {total} en total, en /{destino}')
ftp.quit()
