// Capturas de phpMyAdmin (ejecutar DESPUÉS de capturas.mjs, para que existan los 2 registros). Uso: NODE_PATH=$(npm root -g) node capturas.mjs
import { createRequire } from 'node:module';
const { chromium } = createRequire(import.meta.url)(process.env.PLAYWRIGHT_PATH || 'playwright');
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const dir = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../evidencias');
const SITIO = process.env.SITIO || 'http://localhost:8080';
const PMA = process.env.PMA || 'http://localhost:8081';

const browser = await chromium.launch({ executablePath: process.env.CHROMIUM || undefined });
const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });
const shot = async (nombre) => {
  await page.waitForTimeout(2500);
  await page.screenshot({ path: path.join(dir, nombre) });
  console.log('captura', nombre);
};

await page.goto(`${PMA}/index.php?route=/database/structure&db=wp_cafenahual`);
await shot('12-phpmyadmin-base-de-datos.png');
await page.goto(`${PMA}/index.php?route=/table/structure&db=wp_cafenahual&table=wp_contactos`);
await shot('13-phpmyadmin-estructura-wp_contactos.png');
await page.goto(`${PMA}/index.php?route=/sql&db=wp_cafenahual&table=wp_contactos&pos=0`);
await shot('14-phpmyadmin-registros-wp_contactos.png');

await browser.close();
