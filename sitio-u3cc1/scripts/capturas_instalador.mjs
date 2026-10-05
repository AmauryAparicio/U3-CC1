// Capturas del asistente web de instalación de WordPress (copia y BD temporales, aisladas del sitio real).
// Uso: node capturas_instalador.mjs
import { chromium } from 'playwright';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const dir = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../evidencias');
const SITIO = 'http://127.0.0.1:8082';

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });
const shot = async (nombre) => {
  await page.screenshot({ path: path.join(dir, nombre), fullPage: true });
  console.log('captura', nombre);
};

// Paso 1: datos de conexión a la base de datos.
await page.goto(`${SITIO}/wp-admin/setup-config.php?step=1`);
await page.waitForSelector('#dbname');
await page.fill('#dbname', 'wp_demo_installer');
await page.fill('#uname', 'wpuser');
await page.fill('#pwd', 'wp1234');
await page.fill('#dbhost', '127.0.0.1');
await shot('04-instalador-conexion-bd.png');
await page.click('input[name="submit"]');

// Confirmación de que la conexión funcionó -> botón para ejecutar la instalación.
await page.waitForSelector('a.button, input[name="submit"]', { timeout: 15000 });
const runInstall = page.locator('a:has-text("Ejecutar la instalación")').first();
await runInstall.click();

// Paso 2: título del sitio, usuario admin.
await page.waitForSelector('#weblog_title', { timeout: 15000 });
await page.fill('#weblog_title', 'Café Nahual (demo instalador)');
await page.fill('#user_login', 'admin');
await page.fill('#pass1', 'InstaladorDemo123!');
await page.fill('#admin_email', 'amaurysunstar@gmail.com');
await page.click('input[name="submit"], #submit');

// Paso 3: pantalla de éxito.
await page.waitForSelector('h1', { timeout: 20000 });
await shot('05-instalador-exito.png');

await browser.close();
