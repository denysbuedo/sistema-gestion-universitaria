const fs = require('fs');
const path = require('path');

const LUX_CSS = `
<style>
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
        .btn-action-sm { width:28px; height:28px; padding:0; display:inline-flex; justify-content:center; align-items:center; border-radius:6px; font-size:0.75rem; border:none; transition:all 0.2s; color:#fff; margin:0 2px; text-decoration:none; cursor:pointer; }
        .btn-action-primary { background:#3b82f6; }
        .btn-action-primary:hover { background:#2563eb; transform:translateY(-1px); }
        .btn-action-danger { background:#ef4444; }
        .btn-action-danger:hover { background:#dc2626; transform:translateY(-1px); }
        .badge-pill-on { background:#dcfce7; color:#15803d; font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:20px; display:inline-block; }
        .badge-pill-off { background:#fee2e2; color:#b91c1c; font-size:0.75rem; font-weight:700; padding:3px 10px; border-radius:20px; display:inline-block; }
        .config-table th { background:#f1f5f9; color:#4a5568; font-size:0.82rem; font-weight:700; border-color:#e2e8f0; padding:8px 10px; }
        .config-table td { font-size:0.85rem; color:#1a2e44; border-color:#e2e8f0; padding:7px 10px; vertical-align:middle; }
        .config-table tbody tr:hover { background:#f8fafc; }
        .dropdown-toggle-no-caret::after { display:none !important; }
        .password-icon { float:right; position:relative; margin:-27px 9px 0 0; cursor:pointer; }
</style>`;

const FORM_FILES = [
  'templates/modules/admin/usuario/new.html.twig',
  'templates/modules/admin/usuario/edit.html.twig',
  'templates/modules/admin/usuario/cambiar_clave.html.twig',
  'templates/modules/admin/usuario/changepsw.html.twig',
  'templates/modules/admin/rol/new.html.twig',
  'templates/modules/admin/rol/edit.html.twig',
  'templates/modules/admin/modulo/new.html.twig',
  'templates/modules/admin/modulo/edit.html.twig',
  'templates/modules/admin/tipo_accion/new.html.twig',
  'templates/modules/admin/tipo_accion/edit.html.twig',
  'templates/modules/admin/funcionalidad/edit.html.twig',
  'templates/modules/admin/notificaciones/new.html.twig',
  'templates/modules/admin/configuracion/idioma/new.html.twig',
  'templates/modules/admin/configuracion/idioma/edit.html.twig',
];

let totalFixed = 0;

for (const relPath of FORM_FILES) {
  const filePath = path.join('d:/Proyectos/Personales/akademos_mes', relPath);
  let content = fs.readFileSync(filePath, 'utf8');
  let changed = false;

  // Inject LUX_CSS after {% block body %}
  if (!content.includes('.card-luxury')) {
    content = content.replace(/(\{% block body %\})/,  '$1\n' + LUX_CSS);
    changed = true;
  }

  // card shadow-sm -> card card-luxury
  if (content.includes('class="card shadow-sm"')) {
    content = content.replace(/class="card shadow-sm"/g, 'class="card card-luxury"');
    changed = true;
  }

  // Old header div.row -> d-flex
  const oldHeader = /<div class="row" style="display: flex; justify-content: space-between; align-items: center">/g;
  if (oldHeader.test(content)) {
    content = content.replace(/<div class="row" style="display: flex; justify-content: space-between; align-items: center">/g, '<div class="d-flex align-items-center">');
    changed = true;
  }

  // card-header closing </div> after h3 title - remove it (the d-flex div is the header div itself now)
  // Actually just fix the h3 class
  content = content.replace(/class="card-title text-bold text-gray"/g, 'class="card-title"');

  // Footer buttons
  content = content.replace(/class="btn btn-danger mr-2 btn-cancel"/g, 'class="btn-fs-cancel mr-2 btn-cancel"');
  content = content.replace(/class="btn btn-primary"/g, 'class="btn-fs-submit"');

  // Remove old style blocks that have password-icon (we baked it into LUX_CSS)
  // Only remove the standalone password-icon style if it's in its own block
  content = content.replace(/\s*{% block stylesheets %}\s*<style>\s*\.password-icon \{[^}]+\}\s*<\/style>\s*{% endblock %}/g, '');

  if (changed) {
    fs.writeFileSync(filePath, content, 'utf8');
    console.log('Fixed:', relPath);
    totalFixed++;
  }
}

console.log(`\nDone. Fixed ${totalFixed} files.`);
