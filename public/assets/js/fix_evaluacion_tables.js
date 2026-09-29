const fs = require('fs');

const files = [
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/solicitud/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/plan_anual/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/comision/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/convocatoria/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/rol_comision/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/estado_solicitud/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/estado_aplazamiento/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/plan_trabajo/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/informe_autoevaluacion/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/aplazamiento/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/categoria_acreditacion_pregrado/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/categoria_acreditacion_posgrado/index.html.twig',
  'd:/Proyectos/Personales/akademos_mes/templates/modules/evaluacion/categoria_acreditacion_ies/index.html.twig',
];

files.forEach(fp => {
  if (!fs.existsSync(fp)) { console.log(`SKIP: ${fp}`); return; }
  let c = fs.readFileSync(fp, 'utf8');
  const orig = c;

  // 1. Add config-table class to the table (preserve existing classes)
  c = c.replace(
    /class="table table-bordered table-striped dataTable"/g,
    'class="table config-table table-bordered table-striped dataTable"'
  );

  // 2. Replace table-custom-header thead with plain thead (config-table styles handle it)
  c = c.replace(/class="table-custom-header"/g, '');

  // 3. Add card-body px-4 class instead of plain card-body for better padding
  c = c.replace(
    /<div class="card-body">\s*\n(\s*<table)/g,
    '<div class="card-body px-4">\n$1'
  );

  const rel = fp.replace(/.*templates\/modules\//, '');
  if (c !== orig) {
    fs.writeFileSync(fp, c);
    console.log(`✓ fixed: ${rel}`);
  } else {
    console.log(`- no changes: ${rel}`);
  }
});
