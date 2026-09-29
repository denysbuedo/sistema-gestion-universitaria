const fs = require('fs');
const path = require('path');

const BASE = 'd:/Proyectos/Personales/akademos_mes/templates/modules';

// ── CSS block to inject when missing ────────────────────────────────────────
const LUX_CSS = `        .card-luxury { border-radius:12px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.08); border-top:4px solid #134679; margin-bottom:1.5rem; }
        .card-luxury .card-header { background-color:#fff; border-bottom:1px solid #f1f5f9; padding:15px 24px; }
        .card-luxury .card-title { color:#1a2e44; font-weight:700; font-size:1rem; margin:0; float:none !important; }
        .label-icon { color:#134679; margin-right:6px; opacity:0.8; }
        .fs-label { font-size:0.82rem; font-weight:600; color:#4b5563; margin-bottom:6px; display:block; }
        .btn-fs-submit { background:#134679; color:#fff !important; font-weight:700; font-size:0.85rem; padding:10px 24px; border-radius:8px; border:none; transition:all 0.2s; display:inline-flex; align-items:center; gap:8px; cursor:pointer; }
        .btn-fs-submit:hover { background:#0f3560; transform:translateY(-1px); box-shadow:0 4px 12px rgba(19,70,121,0.2); }
        .btn-fs-cancel { background:#f1f5f9; color:#475569 !important; font-weight:700; font-size:0.85rem; padding:10px 24px; border-radius:8px; border:none; transition:all 0.2s; display:inline-flex; align-items:center; gap:8px; text-decoration:none; cursor:pointer; }
        .btn-fs-cancel:hover { background:#e2e8f0; color:#1e293b !important; transform:translateY(-1px); }
        .config-table th { background:#f1f5f9; color:#4a5568; font-size:0.82rem; font-weight:700; border-color:#e2e8f0; padding:8px 10px; }
        .config-table td { font-size:0.85rem; color:#1a2e44; border-color:#e2e8f0; padding:7px 10px; vertical-align:middle; }`;

// ── MODAL CSS (pasaporte-style) ──────────────────────────────────────────────
const MODAL_CSS = `        .modal-lux .modal-content { border-radius:12px; border:none; box-shadow:0 10px 30px rgba(0,0,0,.2); }
        .modal-lux .modal-header { background:#134679; color:#fff; border-top-left-radius:12px; border-top-right-radius:12px; padding:18px 24px; border-bottom:none; }
        .modal-lux .modal-title { font-weight:700; font-size:1rem; }
        .modal-lux .modal-header .close { color:#fff; opacity:0.8; text-shadow:none; }
        .modal-lux .modal-header .close:hover { opacity:1; }
        .modal-lux .modal-footer { background:#f8f9fa; border-bottom-left-radius:12px; border-bottom-right-radius:12px; border-top:1px solid #e9ecef; }`;

function hasLuxuryCard(content) {
  return content.includes('card-luxury');
}

function needsFooterUpdate(content) {
  return content.includes('btn btn-danger') || content.includes('btn btn-primary">Aceptar') || content.includes('btn btn-primary">Asociar');
}

// ── Transform: inject CSS into {% block stylesheets %} or <style> block ─────
function injectCss(content, extra) {
  if (content.includes('btn-fs-submit') && !extra) return content;

  const cssToAdd = (extra || '') + '\n' + LUX_CSS;

  // If has {% block stylesheets %}
  if (content.includes('{% block stylesheets %}')) {
    if (!content.includes('.btn-fs-submit')) {
      content = content.replace(
        /(\{% block stylesheets %\}[\s\S]*?<style>)/,
        `{% block stylesheets %}\n    {{ parent() }}\n    <style>\n${cssToAdd}`
      );
    }
    return content;
  }

  // If has <style> inside {% block body %}
  if (content.match(/\{% block body %\}\s*<style>/)) {
    if (!content.includes('.btn-fs-submit')) {
      content = content.replace(
        /(\{% block body %\}\s*<style>)/,
        `{% block body %}\n<style>\n${cssToAdd}`
      );
    }
    return content;
  }

  // No style block — inject after {% block body %}
  if (!content.includes('.btn-fs-submit')) {
    content = content.replace(
      '{% block body %}',
      `{% block body %}\n<style>\n${cssToAdd}\n</style>\n`
    );
  }
  return content;
}

// ── Transform: card shadow-sm → card card-luxury ────────────────────────────
function upgradeCard(content) {
  return content
    .replace(/<div class="card shadow-sm">/g, '<div class="card card-luxury">')
    .replace(/<div class="card">/g, '<div class="card card-luxury">');
}

// ── Transform: header div row → d-flex ──────────────────────────────────────
function upgradeCardHeader(content) {
  // Replace old pattern: <div class="card-header"><div class="row" style="display: flex; ...">
  content = content.replace(
    /<div class="card-header">\s*<div class="row" style="display: flex; justify-content: space-between; align-items: center[^"]*">/g,
    `<div class="card-header d-flex align-items-center">`
  );
  // Remove closing </div> that was for the row inside card-header (tricky — skip, just remove empty </div> after h3)
  return content;
}

// ── Transform: old footer buttons → lux buttons ────────────────────────────
function upgradeFooterButtons(content, cancelUrl) {
  // Pattern 1: btn-cancel with data-url + btn-primary Aceptar
  content = content.replace(
    /<a class="btn btn-danger mr-2 btn-cancel"\s+data-url="([^"]+)"\s+data-title="[^"]*"\s+data-content="([^"]+)"\s+type="button">Cancelar<\/a>\s*<button[^>]*class="btn btn-primary"[^>]*>Aceptar<\/button>/g,
    `<a class="btn-fs-cancel btn-cancel" data-url="$1" data-content="$2"><i class="fas fa-times mr-2"></i>Cancelar</a>
            <button type="submit" class="btn-fs-submit"><i class="fas fa-check mr-2"></i>Aceptar</button>`
  );

  // Pattern 2: btn-cancel + id=btn_aceptar
  content = content.replace(
    /<a class="btn btn-danger mr-2 btn-cancel"\s+data-url="([^"]+)"\s+data-title="[^"]*"\s+data-content="([^"]+)"\s+type="button">Cancelar<\/a>\s*<button id="btn_aceptar"[^>]*class="btn btn-primary"[^>]*>Aceptar<\/button>/g,
    `<a class="btn-fs-cancel btn-cancel" data-url="$1" data-content="$2"><i class="fas fa-times mr-2"></i>Cancelar</a>
            <button type="submit" class="btn-fs-submit" id="btn_aceptar"><i class="fas fa-check mr-2"></i>Aceptar</button>`
  );

  // Pattern 3: just btn-primary Asociar
  content = content.replace(
    /<button class="btn btn-primary">Asociar<\/button>/g,
    `<button type="submit" class="btn-fs-submit"><i class="fas fa-check mr-2"></i>Asociar</button>`
  );

  // Pattern 4: btn-primary PDF export button in header
  content = content.replace(
    /<a title="([^"]+)" class="btn btn-primary" target="_blank"\s+href="([^"]+)"><i\s+class="fa fa-file-pdf"><\/i>\s*<\/a>/g,
    `<a title="$1" class="btn-lux-sm btn-lux-secondary" target="_blank" href="$2"><i class="fa fa-file-pdf mr-1"></i> PDF</a>`
  );

  return content;
}

// ── Transform: upgrade modal to pasaporte-style ─────────────────────────────
function upgradeModal(content) {
  // Add modal-lux class
  content = content.replace(
    /class="modal-content"(\s+style="[^"]*border-radius[^"]*")?/g,
    'class="modal-content"'
  );

  // Upgrade modal-dialog wrapper to add modal-lux
  content = content.replace(
    /class="modal-dialog([^"]*)"(\s+role="document")?/g,
    (m, extra) => `class="modal-dialog modal-lux${extra}"`
  );

  // If modal-header has inline style, replace with clean version
  content = content.replace(
    /<div class="modal-header"[^>]*>/g,
    '<div class="modal-header">'
  );

  // Modal footer inline style
  content = content.replace(
    /<div class="modal-footer"[^>]*>/g,
    '<div class="modal-footer">'
  );

  // Modal-content inline style
  content = content.replace(
    /<div class="modal-content"[^>]*>/g,
    '<div class="modal-content">'
  );

  // Modal cancel button → lux
  content = content.replace(
    /<button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal" style="[^"]*">CANCELAR<\/button>/g,
    `<button type="button" class="btn-fs-cancel" data-dismiss="modal"><i class="fas fa-times mr-2"></i>Cancelar</button>`
  );

  return content;
}

// ── Fix header structure (remove extra row div) ──────────────────────────────
function fixHeaderRow(content) {
  // Old: <div class="card-header d-flex align-items-center"><h3...></h3>...buttons...</div></div>
  // The outer </div> was for the row — we need to remove it
  // This is best done by replacing the specific pattern
  content = content.replace(
    /(<div class="card-header d-flex align-items-center">\s*<h3[^<]*<\/h3>)\s*<\/div>\s*(<\/div>)/g,
    '$1\n        </div>'
  );
  return content;
}

// ── Process a single file ────────────────────────────────────────────────────
function processFile(fp) {
  if (!fs.existsSync(fp)) return `SKIP (not found): ${fp}`;
  let content = fs.readFileSync(fp, 'utf8');
  const original = content;

  content = injectCss(content);
  content = upgradeCard(content);
  content = upgradeCardHeader(content);
  content = upgradeFooterButtons(content);
  content = upgradeModal(content);

  const rel = fp.replace(/.*templates\/modules\//, '');
  if (content !== original) {
    fs.writeFileSync(fp, content);
    return `✓ ${rel}`;
  }
  return `- ${rel} (no change)`;
}

// ── File list ────────────────────────────────────────────────────────────────
const files = [
  // PREGRADO — non-index forms that still use old card/button styles
  `${BASE}/pregrado/solicitud_programa_academico/aprobar.html.twig`,
  `${BASE}/pregrado/solicitud_programa_academico/no_aprobar.html.twig`,
  `${BASE}/pregrado/solicitud_programa_academico/edit.html.twig`,
  `${BASE}/pregrado/solicitud_programa_academico_aprobado/asignar_universidad.html.twig`,
  `${BASE}/pregrado/solicitud_programa_academico_aprobado/modificar.html.twig`,
  `${BASE}/pregrado/solicitud_programa_academico_aprobado/asignar_comision.html.twig`,
  `${BASE}/pregrado/solicitud_programa_academico_aprobado/desactivar.html.twig`,
  `${BASE}/pregrado/solicitud_programa_academico_aprobado/reabrir.html.twig`,
  `${BASE}/pregrado/plan_estudio/new.html.twig`,
  `${BASE}/pregrado/plan_estudio/edit.html.twig`,
  `${BASE}/pregrado/plan_estudio/asociar_documento.html.twig`,
  // POSTGRADO — non-index forms
  `${BASE}/postgrado/solicitud_programa/new.html.twig`,
  `${BASE}/postgrado/solicitud_programa/edit.html.twig`,
  `${BASE}/postgrado/solicitud_programa/aprobar.html.twig`,
  `${BASE}/postgrado/solicitud_programa/asignar_comision.html.twig`,
  `${BASE}/postgrado/solicitud_programa/votacion.html.twig`,
  `${BASE}/postgrado/solicitud_programa/no_aprobar.html.twig`,
  `${BASE}/postgrado/solicitud_programa/asociar_dictamen.html.twig`,
  `${BASE}/postgrado/solicitud_programa/revisar_dictamen.html.twig`,
];

files.forEach(f => console.log(processFile(f)));
console.log('\nDone.');
