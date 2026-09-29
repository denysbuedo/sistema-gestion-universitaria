const fs = require('fs');
const path = require('path');

// Map file -> {icon, title} for header customization
const FILE_META = {
  'templates/modules/admin/usuario/new.html.twig':         { icon: 'fa-user-plus',     title: 'Adicionar usuario' },
  'templates/modules/admin/usuario/edit.html.twig':        { icon: 'fa-user-edit',     title: 'Modificar usuario' },
  'templates/modules/admin/usuario/cambiar_clave.html.twig':{ icon: 'fa-key',          title: 'Cambiar contraseña' },
  'templates/modules/admin/usuario/changepsw.html.twig':   { icon: 'fa-lock',          title: 'Cambiar contraseña' },
  'templates/modules/admin/rol/new.html.twig':             { icon: 'fa-user-shield',   title: 'Adicionar rol' },
  'templates/modules/admin/rol/edit.html.twig':            { icon: 'fa-user-shield',   title: 'Modificar rol' },
  'templates/modules/admin/modulo/new.html.twig':          { icon: 'fa-cube',          title: 'Adicionar módulo' },
  'templates/modules/admin/modulo/edit.html.twig':         { icon: 'fa-cube',          title: 'Modificar módulo' },
  'templates/modules/admin/tipo_accion/new.html.twig':     { icon: 'fa-bolt',          title: 'Adicionar tipo de acción' },
  'templates/modules/admin/tipo_accion/edit.html.twig':    { icon: 'fa-bolt',          title: 'Modificar tipo de acción' },
  'templates/modules/admin/funcionalidad/edit.html.twig':  { icon: 'fa-puzzle-piece',  title: 'Modificar funcionalidad' },
  'templates/modules/admin/notificaciones/new.html.twig':  { icon: 'fa-bell',          title: 'Adicionar notificación' },
  'templates/modules/admin/configuracion/idioma/new.html.twig':  { icon: 'fa-language', title: 'Adicionar idioma' },
  'templates/modules/admin/configuracion/idioma/edit.html.twig': { icon: 'fa-language', title: 'Modificar idioma' },
};

let totalFixed = 0;

for (const [relPath, meta] of Object.entries(FILE_META)) {
  const filePath = path.join('d:/Proyectos/Personales/akademos_mes', relPath);
  let content = fs.readFileSync(filePath, 'utf8');

  // Replace the card-header block entirely - handle the nested d-flex div that the first script created
  // Pattern: <div class="card-header">\n        <div class="d-flex align-items-center">\n\n                <h3 class="card-title">TITLE</h3>\n\n            </div>\n        </div>
  // Replace with clean version
  const newHeader = `<div class="card-header d-flex align-items-center">
            <i class="fas ${meta.icon} label-icon mr-2" style="color:#134679;font-size:1.1rem;"></i>
            <h3 class="card-title m-0">${meta.title}</h3>
        </div>`;

  // Match the mangled header produced by fix_admin.js
  const oldHeaderPattern = /<div class="card-header">\s*<div class="d-flex align-items-center">\s*<h3 class="card-title">[^<]*<\/h3>\s*<\/div>\s*<\/div>/gs;
  if (oldHeaderPattern.test(content)) {
    content = content.replace(oldHeaderPattern, newHeader);
    fs.writeFileSync(filePath, content, 'utf8');
    console.log('Fixed header:', relPath);
    totalFixed++;
  } else {
    // Try alternate pattern where header already had content
    const altPattern = /<div class="card-header">\s*<div class="d-flex align-items-center">\s*(?:[\s\S]*?)<\/div>\s*<\/div>/;
    // Just check if card-header doesn't have d-flex already on the outer div
    if (!content.includes('<div class="card-header d-flex')) {
      console.log('SKIP (pattern not matched):', relPath);
    } else {
      console.log('Already correct:', relPath);
    }
  }
}

console.log(`\nDone. Fixed ${totalFixed} files.`);
