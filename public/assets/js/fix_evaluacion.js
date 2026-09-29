const fs = require('fs');

// ─── CSS ──────────────────────────────────────────────────────────────────────
const LUX_CSS = `
        .card-luxury { border-radius:12px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.08); border-top:4px solid #134679; margin-bottom:1.5rem; }
        .card-luxury .card-header { background-color:#fff; border-bottom:1px solid #f1f5f9; padding:15px 24px; }
        .card-luxury .card-title { color:#1a2e44; font-weight:700; font-size:1rem; margin:0; float:none !important; }
        .label-icon { color:#134679; margin-right:6px; opacity:0.8; }
        .fs-label { font-size:0.82rem; font-weight:600; color:#4b5563; margin-bottom:6px; display:block; }
        .btn-fs-submit { background:#134679; color:#fff !important; font-weight:700; font-size:0.85rem; padding:10px 24px; border-radius:8px; border:none; transition:all 0.2s; display:inline-flex; align-items:center; gap:8px; cursor:pointer; }
        .btn-fs-submit:hover { background:#0f3560; transform:translateY(-1px); box-shadow:0 4px 12px rgba(19,70,121,0.2); }
        .btn-fs-cancel { background:#f1f5f9; color:#475569 !important; font-weight:700; font-size:0.85rem; padding:10px 24px; border-radius:8px; border:none; transition:all 0.2s; display:inline-flex; align-items:center; gap:8px; text-decoration:none; cursor:pointer; }
        .btn-fs-cancel:hover { background:#e2e8f0; color:#1e293b !important; transform:translateY(-1px); }
        .btn-lux-sm { padding:6px 14px; border-radius:6px; font-weight:700; font-size:0.75rem; border:none; display:inline-flex; align-items:center; gap:5px; text-transform:uppercase; transition:all 0.2s; text-decoration:none; color:#fff; cursor:pointer; }
        .btn-lux-primary { background:#134679; color:#fff; }
        .btn-lux-primary:hover { background:#0f3560; color:#fff; transform:translateY(-1px); }
        .btn-lux-success { background:#15803d; color:#fff; }
        .btn-lux-success:hover { background:#166534; color:#fff; transform:translateY(-1px); }
        .btn-action-sm { width:28px; height:28px; padding:0; display:inline-flex; justify-content:center; align-items:center; border-radius:6px; font-size:0.75rem; border:none; transition:all 0.2s; color:#fff; margin:0 2px; text-decoration:none; cursor:pointer; }
        .btn-action-primary { background:#3b82f6; }
        .btn-action-primary:hover { background:#2563eb; transform:translateY(-1px); }
        .btn-action-warning { background:#eab308; color:#1e293b !important; }
        .btn-action-warning:hover { background:#ca8a04; transform:translateY(-1px); }
        .btn-action-danger { background:#ef4444; }
        .btn-action-danger:hover { background:#dc2626; transform:translateY(-1px); }
        .badge-pill-on { background:#dcfce7; color:#15803d; font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:20px; display:inline-block; }
        .badge-pill-off { background:#fee2e2; color:#b91c1c; font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:20px; display:inline-block; }
        .config-table th { background:#f1f5f9; color:#4a5568; font-size:0.82rem; font-weight:700; border-color:#e2e8f0; padding:8px 10px; }
        .config-table td { font-size:0.85rem; color:#1a2e44; border-color:#e2e8f0; padding:7px 10px; vertical-align:middle; }
`;

// ─── Helpers ──────────────────────────────────────────────────────────────────

function injectCss(c) {
  if (c.includes('.card-luxury {')) return c; // already injected

  // Try {% block stylesheets %} ... {{ parent() }} ... <style>
  if (c.includes('{% block stylesheets %}')) {
    if (c.includes('<style>')) {
      c = c.replace('<style>', '<style>' + LUX_CSS);
    } else {
      c = c.replace(
        /(\{% block stylesheets %\}[\s\S]*?\{\{ parent\(\) \}\})/,
        '$1\n    <style>' + LUX_CSS + '    </style>'
      );
    }
    return c;
  }

  // No stylesheets block — inject <style> before first <div class="card
  if (c.includes('<style>')) {
    c = c.replace('<style>', '<style>' + LUX_CSS);
  } else {
    c = c.replace(
      /(\{% block body %\}\s*\n)/,
      '$1<style>' + LUX_CSS + '</style>\n\n'
    );
  }
  return c;
}

function fixCard(c) {
  return c.replace(/class="card shadow-sm"/g, 'class="card card-luxury"');
}

function fixHeader(c) {
  // Pattern: <div class="card-header">\n   <div class="row" style="...">\n      <h3>TITLE</h3>\n      [optional extra content]\n   </div>\n</div>
  // We need to handle cases where there's extra content (buttons) after h3

  // Case 1: Only h3 inside row (no extra buttons)
  c = c.replace(
    /<div class="card-header">\s*<div class="row"[^>]*>\s*(<h3[^<]*<\/h3>)\s*<\/div>\s*<\/div>/g,
    '<div class="card-header d-flex align-items-center">\n            $1\n        </div>'
  );

  // Case 2: h3 + one <a> button (Nuevo)
  c = c.replace(
    /<div class="card-header">\s*<div class="row"[^>]*>\s*(<h3[^<]*<\/h3>)\s*(<a[^>]+class="btn btn-primary[^"]*"[^>]*>[\s\S]*?<\/a>)\s*<\/div>\s*<\/div>/g,
    (_, h3, btn) => {
      // Convert btn btn-primary to btn-lux-sm btn-lux-primary
      const newBtn = btn
        .replace(/class="btn btn-primary[^"]*"/, 'class="btn-lux-sm btn-lux-primary"')
        .replace(/<i\s+class="fa fa-plus"><\/i>\s*/g, '<i class="fas fa-plus mr-1"></i>');
      return `<div class="card-header d-flex align-items-center">\n            ${h3}\n            <div class="ml-auto">${newBtn.trim()}</div>\n        </div>`;
    }
  );

  // Case 3: h3 + <div style="float: left..."> wrapping a button (Excel export pattern)
  c = c.replace(
    /<div class="card-header">\s*<div class="row"[^>]*>\s*(<h3[^<]*<\/h3>)\s*<div[^>]*style="float[^"]*"[^>]*>([\s\S]*?)<\/div>\s*<\/div>\s*<\/div>/g,
    (_, h3, inner) => {
      const newInner = inner
        .replace(/class="btn btn-success[^"]*"/g, 'class="btn-lux-sm btn-lux-success"')
        .replace(/class="btn btn-primary[^"]*"/g, 'class="btn-lux-sm btn-lux-primary"');
      return `<div class="card-header d-flex align-items-center">\n            ${h3}\n            <div class="ml-auto d-flex gap-2">${newInner.trim()}</div>\n        </div>`;
    }
  );

  return c;
}

function fixFooterButtons(c) {
  // Cancel: btn btn-danger mr-2 btn-cancel
  c = c.replace(
    /class="btn btn-danger[^"]*btn-cancel[^"]*"/g,
    'class="btn-fs-cancel btn-cancel"'
  );
  c = c.replace(
    /class="btn btn-danger btn-cancel[^"]*"/g,
    'class="btn-fs-cancel btn-cancel"'
  );
  // Submit: btn btn-primary (footer)
  c = c.replace(
    /<button class="btn btn-primary">(Aceptar|Guardar|Crear|Actualizar|Registrar|Asociar)<\/button>/g,
    '<button class="btn-fs-submit"><i class="fas fa-check mr-2"></i>$1</button>'
  );
  c = c.replace(
    /<button class="btn btn-primary"(\s*>)(Aceptar|Guardar|Crear|Actualizar|Registrar)/g,
    '<button class="btn-fs-submit"$1<i class="fas fa-check mr-2"></i>$2'
  );
  // Generic btn btn-primary in footer (with id)
  c = c.replace(
    /<button id="([^"]+)" class="btn btn-primary">(Aceptar|Guardar|Crear|Actualizar|Registrar)<\/button>/g,
    '<button id="$1" class="btn-fs-submit"><i class="fas fa-check mr-2"></i>$2</button>'
  );
  return c;
}

function fixTableButtons(c) {
  // btn btn-outline-primary → btn-action-sm btn-action-primary
  c = c.replace(/class="btn btn-outline-primary modalDetailAction"/g, 'class="btn-action-sm btn-action-primary modalDetailAction"');
  c = c.replace(/class="btn btn-outline-primary elementRemove"/g, 'class="btn-action-sm btn-action-primary elementRemove"');
  c = c.replace(/class="btn btn-outline-primary"/g, 'class="btn-action-sm btn-action-primary"');
  // btn btn-outline-danger → btn-action-sm btn-action-danger
  c = c.replace(/class="btn btn-outline-danger elementRemove"/g, 'class="btn-action-sm btn-action-danger elementRemove"');
  c = c.replace(/class="btn btn-outline-danger"/g, 'class="btn-action-sm btn-action-danger"');
  return c;
}

function fixMsStatus(c) {
  // ms-status -active → badge-pill-on
  c = c.replace(
    /<span class="ms-status -active">(Habilitado|Activo|Si|Sí)<\/span>/g,
    '<span class="badge-pill-on"><i class="fas fa-check mr-1"></i>$1</span>'
  );
  c = c.replace(
    /<span class="ms-status">(Deshabilitado|Inactivo|No)<\/span>/g,
    '<span class="badge-pill-off"><i class="fas fa-times mr-1"></i>$1</span>'
  );
  // Generic ms-status with dynamic content
  c = c.replace(
    /class="ms-status -active"/g,
    'class="badge-pill-on"'
  );
  c = c.replace(
    /class="ms-status"/g,
    'class="badge-pill-off"'
  );
  return c;
}

function addStylesToBlockBody(c) {
  // If no {% block stylesheets %}, ensure <style> is right after {% block body %}
  if (!c.includes('{% block stylesheets %}') && !c.includes('.card-luxury {')) {
    if (c.includes('<style>')) {
      c = c.replace('<style>', '<style>' + LUX_CSS);
    }
  }
  return c;
}

function processFile(fp) {
  if (!fs.existsSync(fp)) { console.log(`SKIP (not found): ${fp}`); return; }
  let c = fs.readFileSync(fp, 'utf8');
  const orig = c;

  c = fixCard(c);
  c = injectCss(c);
  c = fixHeader(c);
  c = fixFooterButtons(c);
  c = fixTableButtons(c);
  c = fixMsStatus(c);

  const rel = fp.replace(/.*templates\/modules\//, '');
  if (c !== orig) {
    fs.writeFileSync(fp, c);
    console.log(`✓ fixed: ${rel}`);
  } else {
    console.log(`- no changes: ${rel}`);
  }
}

// ─── File list ────────────────────────────────────────────────────────────────
const BASE = 'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion';

const files = [
  // Configuración simple (index + new + edit)
  `${BASE}/rol_comision/index.html.twig`,
  `${BASE}/rol_comision/new.html.twig`,
  `${BASE}/rol_comision/edit.html.twig`,
  `${BASE}/estado_solicitud/index.html.twig`,
  `${BASE}/estado_solicitud/new.html.twig`,
  `${BASE}/estado_solicitud/edit.html.twig`,
  `${BASE}/estado_aplazamiento/index.html.twig`,
  `${BASE}/estado_aplazamiento/new.html.twig`,
  `${BASE}/estado_aplazamiento/edit.html.twig`,
  `${BASE}/convocatoria/index.html.twig`,
  `${BASE}/convocatoria/new.html.twig`,
  `${BASE}/convocatoria/edit.html.twig`,
  `${BASE}/comision/index.html.twig`,
  `${BASE}/comision/new.html.twig`,
  `${BASE}/comision/edit.html.twig`,
  // Solicitud
  `${BASE}/solicitud/index.html.twig`,
  `${BASE}/solicitud/new.html.twig`,
  `${BASE}/solicitud/edit.html.twig`,
  `${BASE}/solicitud/aprobar.html.twig`,
  `${BASE}/solicitud/rechazar.html.twig`,
  `${BASE}/solicitud/informeAutoevaluacion.html.twig`,
  `${BASE}/solicitud/planTrabajo.html.twig`,
  // Plan anual
  `${BASE}/plan_anual/index.html.twig`,
  `${BASE}/plan_anual/aplazar.html.twig`,
  `${BASE}/plan_anual/dictamenComision.html.twig`,
  `${BASE}/plan_anual/dictamenCTE.html.twig`,
  `${BASE}/plan_anual/dictamenJAN.html.twig`,
  // Otros índices
  `${BASE}/plan_trabajo/index.html.twig`,
  `${BASE}/informe_autoevaluacion/index.html.twig`,
  `${BASE}/aplazamiento/index.html.twig`,
  // Categorías acreditación
  `${BASE}/categoria_acreditacion_pregrado/index.html.twig`,
  `${BASE}/categoria_acreditacion_pregrado/edit.html.twig`,
  `${BASE}/categoria_acreditacion_posgrado/index.html.twig`,
  `${BASE}/categoria_acreditacion_posgrado/edit.html.twig`,
  `${BASE}/categoria_acreditacion_ies/index.html.twig`,
  `${BASE}/categoria_acreditacion_ies/edit.html.twig`,
];

files.forEach(processFile);
console.log('\nDone.');
