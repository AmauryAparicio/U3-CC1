// Capturas de evidencia con Playwright. Uso: NODE_PATH=$(npm root -g) node capturas.mjs
import { createRequire } from 'node:module';
const { chromium } = createRequire(import.meta.url)(process.env.PLAYWRIGHT_PATH || 'playwright');
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const dir = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../evidencias');
const SITIO = process.env.SITIO || 'http://localhost:8080';
const PMA = process.env.PMA || 'http://localhost:8081';

const mensajes = [
  { nombre: 'Mariana López Hernández', correo: 'mariana.lopez@example.com', telefono: '5512345678',
    mensaje: 'Hola, me interesa el café de Chiapas en grano. ¿Hacen envíos a la Ciudad de México y cuánto tardan?' },
  { nombre: 'Carlos Ramírez Ortega', correo: 'carlos.ramirez@example.com', telefono: '2281234567',
    mensaje: 'Buenas tardes, tengo una cafetería en Xalapa y quisiera cotizar 5 kg mensuales de café de Veracruz.' },
];

const browser = await chromium.launch({ executablePath: process.env.CHROMIUM || undefined });
const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });
const shot = async (nombre, full = false) => {
  await page.screenshot({ path: path.join(dir, nombre), fullPage: full });
  console.log('captura', nombre);
};

// Sitio
await page.goto(`${SITIO}/`);                          await shot('01-sitio-inicio.png', true);
await page.goto(`${SITIO}/historia/`);                 await shot('02-sitio-historia.png', true);
await page.goto(`${SITIO}/productos-y-servicios/`);    await shot('03-sitio-productos.png', true);
await page.goto(`${SITIO}/enlaces-de-interes/`);       await shot('04-sitio-enlaces.png', true);
await page.goto(`${SITIO}/contacto/`);                 await shot('05-formulario-vacio.png', true);

// Prueba negativa: correo inválido no debe guardarse
await page.fill('#nc-nombre', 'Prueba Inválida');
await page.fill('#nc-correo', 'correo-sin-arroba');
await page.fill('#nc-telefono', '123');
await page.fill('#nc-mensaje', 'Este envío debe ser rechazado por la validación.');
await page.click('button[type=submit]');
await page.waitForSelector('.aviso-error');
await shot('06-validacion-rechazo.png', true);

// Dos envíos reales
let n = 7;
for (const m of mensajes) {
  await page.goto(`${SITIO}/contacto/`);
  await page.fill('#nc-nombre', m.nombre);
  await page.fill('#nc-correo', m.correo);
  await page.fill('#nc-telefono', m.telefono);
  await page.fill('#nc-mensaje', m.mensaje);
  await shot(`${String(n++).padStart(2, '0')}-formulario-lleno-${m.nombre.split(' ')[0].toLowerCase()}.png`, true);
  await Promise.all([page.waitForURL(/contacto=ok/), page.click('button[type=submit]')]);
  await page.waitForSelector('.aviso-ok');
  await shot(`${String(n++).padStart(2, '0')}-formulario-enviado-${m.nombre.split(' ')[0].toLowerCase()}.png`, true);
}

// CSS externo
await page.goto(`${SITIO}/wp-content/themes/cafe-nahual/style.css`);
await shot('11-css-externo-style-css.png');

await browser.close();
