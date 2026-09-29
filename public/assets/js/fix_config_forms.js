const fs = require('fs');

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
`;

const files = [
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/rama_ciencia/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/rama_ciencia/edit.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/tipo_programa/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/tipo_programa/edit.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/tipo_solicitud/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/tipo_solicitud/edit.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/rol_comision/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/rol_comision/edit.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/comision/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/comision/edit.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/copep/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/copep/edit.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/centro_autorizado/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/formacion_doctores/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/tipo_solicitud/config_clasificacion.html.twig',
];

files.forEach(fp => {
  if (!fs.existsSync(fp)) { console.log(`SKIP: ${fp}`); return; }
  let c = fs.readFileSync(fp, 'utf8');
  const orig = c;

  // 1. Replace card shadow-sm -> card card-luxury
  c = c.replace(/class="card shadow-sm"/g, 'class="card card-luxury"');

  // 2. Inject LUX_CSS if card-luxury is present but CSS is not yet there
  if (c.includes('card-luxury') && !c.includes('.card-luxury {')) {
    // Inject after {% block body %} or after opening <style> tag if exists
    // Try to inject into existing <style> block first
    if (c.includes('<style>')) {
      c = c.replace('<style>', '<style>' + LUX_CSS);
    } else {
      // Inject before first <div class="card
      c = c.replace(/(<div class="card card-luxury")/, '<style>' + LUX_CSS + '</style>\n$1');
    }
  }

  // 3. Fix old card-header with div.row pattern -> d-flex
  c = c.replace(
    /<div class="card-header">\s*<div class="row"[^>]*>\s*(<h3[^<]*<\/h3>)\s*<\/div>\s*<\/div>/g,
    '<div class="card-header d-flex align-items-center">\n            $1\n        </div>'
  );
  // Also handle when there's only one closing </div> after h3
  c = c.replace(
    /<div class="card-header">\s*<div class="row"[^>]*>\s*(<h3[^<]*<\/h3>)\s*<\/div>/g,
    '<div class="card-header d-flex align-items-center">\n            $1\n        </div>'
  );

  // 4. Replace old footer buttons
  c = c.replace(
    /class="btn btn-danger btn-cancel"/g,
    'class="btn-fs-cancel btn-cancel"'
  );
  c = c.replace(
    /class="btn btn-danger (?:btn-)?cancel"/g,
    'class="btn-fs-cancel btn-cancel"'
  );
  c = c.replace(
    /class="btn btn-primary"(\s*>(?:Guardar|Aceptar|Crear|Actualizar|Registrar))/g,
    'class="btn-fs-submit"$1'
  );

  const rel = fp.replace(/.*templates\/modules\//, '');
  if (c !== orig) {
    fs.writeFileSync(fp, c);
    console.log(`✓ fixed: ${rel}`);
  } else {
    console.log(`- no changes: ${rel}`);
  }
});
