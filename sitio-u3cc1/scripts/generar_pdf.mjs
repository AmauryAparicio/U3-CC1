// Genera documento/Evidencias_U3CC1.pdf. Uso: SITIO_REMOTO=https://... node generar_pdf.mjs
import { createRequire } from 'node:module';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
const { chromium } = createRequire(import.meta.url)(process.env.PLAYWRIGHT_PATH || 'playwright');
const doc = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../documento');
const liga = process.env.SITIO_REMOTO
  ? `<a href="${process.env.SITIO_REMOTO}">${process.env.SITIO_REMOTO}</a>`
  : '<span style="background:#fff3a0;padding:0 4px">[PEGAR AQUÍ LA LIGA DEL SITIO EN INFINITYFREE]</span>';
const html = fs.readFileSync(path.join(doc, 'evidencias.template.html'), 'utf8').replaceAll('{{LIGA}}', liga);
fs.writeFileSync(path.join(doc, 'evidencias.html'), html);
const b = await chromium.launch({ executablePath: process.env.CHROMIUM || undefined });
const p = await b.newPage();
await p.goto('file://' + path.join(doc, 'evidencias.html'));
await p.pdf({ path: path.join(doc, 'Evidencias_U3CC1.pdf'), format: 'Letter', printBackground: true,
  displayHeaderFooter: true, headerTemplate: '<span></span>',
  footerTemplate: '<div style="font-size:9px;width:100%;text-align:center;color:#555">Jorge Amaury Aparicio Cuevas · Grupo 8691 · U3-CC1 · Página <span class="pageNumber"></span></div>',
  margin: { top: '2.2cm', bottom: '2.2cm', left: '2cm', right: '2cm' } });
await b.close();
console.log('PDF generado');
