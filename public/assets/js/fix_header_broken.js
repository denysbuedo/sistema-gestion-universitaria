const fs = require('fs');

// The broken pattern introduced by upgradeCardHeader:
// <div class="card card-luxury">
//     <div class="card-header d-flex align-items-center">
//             <h3 class="card-title text-bold">TITLE</h3>
//         </div>       <-- closes card-header (correct)
//     </div>           <-- EXTRA closing div (closes card-luxury prematurely!)
const BROKEN = /<div class="card card-luxury">\s*<div class="card-header d-flex align-items-center">\s*(<h3[^<]*<\/h3>)\s*<\/div>\s*<\/div>/g;
const FIXED  = `<div class="card card-luxury">\n    <div class="card-header d-flex align-items-center">\n        $1\n    </div>`;

const files = [
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/solicitud_programa/asignar_comision.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/solicitud_programa/aprobar.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/solicitud_programa/votacion.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/solicitud_programa/no_aprobar.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/solicitud_programa/asociar_dictamen.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/solicitud_programa/revisar_dictamen.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/solicitud_programa/edit.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado/solicitud_programa/new.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/pregrado/solicitud_programa_academico_aprobado/asignar_universidad.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/pregrado/solicitud_programa_academico_aprobado/modificar.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/pregrado/solicitud_programa_academico_aprobado/asignar_comision.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/pregrado/solicitud_programa_academico_aprobado/desactivar.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/pregrado/solicitud_programa_academico_aprobado/reabrir.html.twig',
];

files.forEach(fp => {
  if (!fs.existsSync(fp)) { console.log(`SKIP: ${fp}`); return; }
  let c = fs.readFileSync(fp, 'utf8');
  const orig = c;
  c = c.replace(BROKEN, FIXED);

  // Also fix: if card-luxury div was not properly opened wrapping everything
  // Check if form_start / card-body is OUTSIDE card-luxury (i.e., after the premature </div>)
  // This can't be checked with regex alone; just fix the double-close

  if (c !== orig) {
    fs.writeFileSync(fp, c);
    const rel = fp.replace(/.*templates\/modules\//, '');
    console.log(`✓ fixed: ${rel}`);
  } else {
    const rel = fp.replace(/.*templates\/modules\//, '');
    console.log(`- no broken pattern: ${rel}`);
  }
});
