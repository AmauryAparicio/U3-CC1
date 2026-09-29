// Capturas del formulario en el sitio YA MIGRADO (remoto).
import { chromium } from 'playwright';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const dir = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../evidencias');
const SITIO = 'https://cafenahual.freehosting.dev';
const UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36';

const mensajes = [
  { nombre: 'Mariana López Hernández', correo: 'mariana.lopez@example.com', telefono: '5512345678',
    mensaje: 'Hola, me interesa el café de Chiapas en grano. ¿Hacen envíos a la Ciudad de México y cuánto tardan?' },
  { nombre: 'Carlos Ramírez Ortega', correo: 'carlos.ramirez@example.com', telefono: '2281234567',
    mensaje: 'Buenas tardes, tengo una cafetería en Xalapa y quisiera cotizar 5 kg mensuales de café de Veracruz.' },
];

const browser = await chromium.launch();
const ctx = await browser.newContext({ userAgent: UA, viewport: { width: 1280, height: 800 } });
const page = await ctx.newPage();
const shot = async (nombre) => { await page.screenshot({ path: path.join(dir, nombre), fullPage: true }); console.log('captura', nombre); };

await page.goto(`${SITIO}/`, { waitUntil: 'load', timeout: 20000 });
await shot('15-remoto-sitio-migrado.png');

let n = 16;
for (const m of mensajes) {
  await page.goto(`${SITIO}/contacto/`, { waitUntil: 'load', timeout: 20000 });
  await page.fill('#nc-nombre', m.nombre);
  await page.fill('#nc-correo', m.correo);
  await page.fill('#nc-telefono', m.telefono);
  await page.fill('#nc-mensaje', m.mensaje);
  await shot(`${String(n++).padStart(2, '0')}-remoto-formulario-lleno-${m.nombre.split(' ')[0].toLowerCase()}.png`);
  await Promise.all([page.waitForURL(/contacto=ok/, { timeout: 20000 }), page.click('button[type=submit]')]);
  await page.waitForSelector('.aviso-ok', { timeout: 10000 });
  await shot(`${String(n++).padStart(2, '0')}-remoto-formulario-enviado-${m.nombre.split(' ')[0].toLowerCase()}.png`);
}

await browser.close();
