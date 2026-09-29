const fs = require('fs');
const path = require('path');

const BASE = 'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado';

// ─── Shared CSS block ────────────────────────────────────────────────────────
const SHARED_CSS = `    .card-luxury { border-radius:12px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.08); border-top:4px solid #134679; margin-bottom:1.5rem; }
        .card-luxury .card-header { background-color:#fff; border-bottom:1px solid #f1f5f9; padding:15px 24px; }
        .card-luxury .card-title { color:#1a2e44; font-weight:700; font-size:1rem; margin:0; float:none !important; }
        .label-icon { color:#134679; margin-right:6px; opacity:0.8; }
        .config-table th { background:#f1f5f9; color:#4a5568; font-size:0.82rem; font-weight:700; border-color:#e2e8f0; padding:8px 10px; }
        .config-table td { font-size:0.85rem; color:#1a2e44; border-color:#e2e8f0; padding:7px 10px; vertical-align:middle; }
        .config-table tbody tr:hover { background:#f8fafc; }
        .btn-lux-sm { padding:6px 14px; border-radius:6px; font-weight:700; font-size:0.75rem; border:none; display:inline-flex; align-items:center; gap:5px; text-transform:uppercase; text-decoration:none; cursor:pointer; transition:all 0.2s; }
        .btn-lux-primary { background:#134679; color:#fff !important; }
        .btn-lux-primary:hover { background:#0f3560; }`;

// ─── Dropdown JS ─────────────────────────────────────────────────────────────
const DROPDOWN_JS = `        $(document).on('show.bs.dropdown', 'table .dropdown', function () {
            var $btn  = $(this).find('button[data-toggle="dropdown"]');
            var $menu = $(this).find('.dropdown-menu');
            var offset = $btn.offset();
            $menu.appendTo('body').css({
                position: 'fixed',
                top:   offset.top + $btn.outerHeight() + 2,
                left:  'auto',
                right: $(window).width() - offset.left - $btn.outerWidth(),
                display: 'block', zIndex: 9999
            });
        });
        $(document).on('hide.bs.dropdown', 'table .dropdown', function () {
            var $menu = $(this).find('.dropdown-menu');
            if ($menu.parent().is('body')) {
                $menu.appendTo($(this)).css({ position: '', top: '', left: '', right: '', zIndex: '' });
            }
        });`;

// ─── Badge Estado pill ────────────────────────────────────────────────────────
function badgePill(field) {
  return `{% if '1' == item.${field} %}
                                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;background:#dcfce7;color:#15803d;letter-spacing:.3px;"><i class="fas fa-circle" style="font-size:0.45rem;vertical-align:middle;margin-right:4px;"></i>Habilitado</span>
                            {% else %}
                                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:0.72rem;font-weight:700;background:#fee2e2;color:#b91c1c;letter-spacing:.3px;"><i class="fas fa-circle" style="font-size:0.45rem;vertical-align:middle;margin-right:4px;"></i>Deshabilitado</span>
                            {% endif %}`;
}

// ─── Dropdown cell ────────────────────────────────────────────────────────────
function dropdownCell(items) {
  const inner = items.map(i => {
    if (i.divider) return `                                <div class="dropdown-divider my-1"></div>`;
    if (i.type === 'detail') return `                                <button class="dropdown-item modalDetailAction" style="padding:8px 16px;"
                                        data-url="{{ path('${i.route}', {'id': item.id}) }}"
                                        data-title="${i.title}">
                                    <i class="fa fa-eye fa-fw mr-2 text-primary"></i> Ver detalles
                                </button>`;
    if (i.type === 'link') return `                                <a class="dropdown-item" style="padding:8px 16px;" href="{{ path('${i.route}', {'id': item.id}) }}">
                                    <i class="fa fa-edit fa-fw mr-2 text-warning"></i> Modificar
                                </a>`;
    if (i.type === 'config') return `                                <a class="dropdown-item" style="padding:8px 16px;" href="{{ path('${i.route}', {'id': item.id}) }}">
                                    <i class="fas fa-cog fa-fw mr-2 text-secondary"></i> ${i.label}
                                </a>`;
    if (i.type === 'remove') return `                                <button class="dropdown-item elementRemove text-danger" style="padding:8px 16px;"
                                        data-url="{{ path('${i.route}', {'id': item.id}) }}">
                                    <i class="fa fa-trash fa-fw mr-2"></i> Eliminar
                                </button>`;
  }).join('\n');

  return `<div class="dropdown d-flex justify-content-center">
                            <button class="btn btn-sm btn-light rounded-circle shadow-sm border-0 p-0 d-flex align-items-center justify-content-center"
                                    style="width:32px;height:32px;background:#f1f5f9;color:#64748b;font-size:1rem;line-height:1;"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                &#8942;
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm" style="border-radius:10px;border:none;min-width:180px;padding:6px 0;font-size:0.85rem;">
${inner}
                            </div>
                        </div>`;
}

// ─── Pill toggle for new/edit forms ──────────────────────────────────────────
const PILL_CSS = `        .estado-toggle { display:inline-flex; border-radius:20px; background:#f1f5f9; padding:3px; gap:2px; }
        .et-opt { padding:4px 14px; border-radius:20px; border:none; font-size:0.75rem; font-weight:700;
                  cursor:pointer; background:transparent; color:#94a3b8; display:inline-flex;
                  align-items:center; gap:5px; transition:all .18s; letter-spacing:.3px; }
        .et-opt i { font-size:0.42rem; vertical-align:middle; }
        .et-opt.et-on.active  { background:#dcfce7; color:#15803d; }
        .et-opt.et-off.active { background:#fee2e2; color:#b91c1c; }
        .et-opt:not(.active):hover { color:#64748b; }`;

const PILL_JS = `        $(document).on('click', '.estado-toggle .et-opt', function() {
            var $toggle = $(this).closest('.estado-toggle');
            var cbId    = $toggle.data('cb');
            var isOn    = $(this).hasClass('et-on');
            $toggle.find('.et-opt').removeClass('active');
            $(this).addClass('active');
            $('#' + cbId).prop('checked', isOn);
        });`;

function pillToggleHtml() {
  return `<label class="d-block" style="font-size:0.82rem;font-weight:600;color:#4b5563;margin-bottom:6px;"><i class="fas fa-toggle-on label-icon"></i>Estado</label>
                    <input type="checkbox"
                           id="{{ form.activo.vars.id }}"
                           name="{{ form.activo.vars.full_name }}"
                           class="d-none activo-checkbox"
                           {% if form.activo.vars.data %}checked{% endif %}>
                    <div class="estado-toggle" data-cb="{{ form.activo.vars.id }}">
                        <button type="button" class="et-opt et-on{% if form.activo.vars.data %} active{% endif %}">
                            <i class="fas fa-circle"></i> Habilitado
                        </button>
                        <button type="button" class="et-opt et-off{% if not form.activo.vars.data %} active{% endif %}">
                            <i class="fas fa-circle"></i> Deshabilitado
                        </button>
                    </div>`;
}

// ═══════════════════════════════════════════════════════════════════════════
// INDEX REWRITES
// ═══════════════════════════════════════════════════════════════════════════

// ─── rama_ciencia/index.html.twig ─────────────────────────────────────────
const ramaCienciaIndex = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Ramas de la ciencia{% endblock %}

{% block body %}
<style>
    ${SHARED_CSS}
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-flask label-icon"></i>Listado de ramas de la ciencia</h3>
        <div class="ml-auto">
            <a title="Nuevo" class="btn-lux-sm btn-lux-primary" href="{{ path('app_rama_ciencia_registrar') }}">
                <i class="fa fa-plus"></i> Nuevo
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th width="1%">No.</th>
                <th>Nombre</th>
                <th width="10%">Siglas</th>
                <th width="12%" class="text-center">Estado</th>
                <th width="8%" class="text-center">Opciones</th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td>{{ loop.index }}</td>
                    <td>{{ item.nombre | default('') }}</td>
                    <td>{{ item.siglas | default('') }}</td>
                    <td class="text-center">
                        ${badgePill('activo')}
                    </td>
                    <td class="text-center">
                        ${dropdownCell([
  {type:'detail', route:'app_rama_ciencia_detail', title:'Detalles de la rama de la ciencia'},
  {type:'link',   route:'app_rama_ciencia_modificar'},
  {divider:true},
  {type:'remove', route:'app_rama_ciencia_eliminar'},
])}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script>
        $(document).ready(function () {
${DROPDOWN_JS}
        });
    </script>
{% endblock %}
`;

// ─── tipo_programa/index.html.twig ────────────────────────────────────────
const tipoProgramaIndex = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Tipos de programas{% endblock %}

{% block body %}
<style>
    ${SHARED_CSS}
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-list-ul label-icon"></i>Listado de tipos de programas</h3>
        <div class="ml-auto">
            <a title="Nuevo" class="btn-lux-sm btn-lux-primary" href="{{ path('app_tipo_programa_registrar') }}">
                <i class="fa fa-plus"></i> Nuevo
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th width="1%">No.</th>
                <th>Nombre</th>
                <th width="12%" class="text-center">Estado</th>
                <th width="8%" class="text-center">Opciones</th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td>{{ loop.index }}</td>
                    <td>{{ item.nombre }}</td>
                    <td class="text-center">
                        ${badgePill('activo')}
                    </td>
                    <td class="text-center">
                        ${dropdownCell([
  {type:'detail', route:'app_tipo_programa_detail', title:'Detalles del tipo de programa'},
  {type:'link',   route:'app_tipo_programa_modificar'},
  {divider:true},
  {type:'remove', route:'app_tipo_programa_eliminar'},
])}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script>
        $(document).ready(function () {
${DROPDOWN_JS}
        });
    </script>
{% endblock %}
`;

// ─── tipo_solicitud/index.html.twig ───────────────────────────────────────
const tipoSolicitudIndex = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Tipos de solicitudes{% endblock %}

{% block body %}
<style>
    ${SHARED_CSS}
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-clipboard-list label-icon"></i>Listado de tipos de solicitudes</h3>
        <div class="ml-auto">
            <a title="Nuevo" class="btn-lux-sm btn-lux-primary" href="{{ path('app_tipo_solicitud_registrar') }}">
                <i class="fa fa-plus"></i> Nuevo
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th width="1%">No.</th>
                <th>Nombre</th>
                <th>Clasificaciones</th>
                <th width="12%" class="text-center">Estado</th>
                <th width="8%" class="text-center">Opciones</th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td>{{ loop.index }}</td>
                    <td>{{ item.nombre }}</td>
                    <td>
                        {% for item1 in item.clasificaciones %}
                            <span style="display:inline-block;padding:2px 9px;border-radius:20px;font-size:0.72rem;font-weight:700;background:#0E375F;color:#fff;letter-spacing:.3px;margin:1px 2px;">{{ item1 }}</span>
                        {% endfor %}
                    </td>
                    <td class="text-center">
                        ${badgePill('activo')}
                    </td>
                    <td class="text-center">
                        ${dropdownCell([
  {type:'detail', route:'app_tipo_solicitud_detail', title:'Detalles del tipo de solicitud'},
  {type:'link',   route:'app_tipo_solicitud_modificar'},
  {type:'config', route:'app_tipo_solicitud_config_clasificacion', label:'Configurar clasificaciones'},
  {divider:true},
  {type:'remove', route:'app_tipo_solicitud_eliminar'},
])}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script>
        $(document).ready(function () {
${DROPDOWN_JS}
        });
    </script>
{% endblock %}
`;

// ─── rol_comision/index.html.twig ─────────────────────────────────────────
const rolComisionIndex = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Roles de comisión{% endblock %}

{% block body %}
<style>
    ${SHARED_CSS}
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-user-tag label-icon"></i>Listado de roles</h3>
        <div class="ml-auto">
            <a title="Nuevo" class="btn-lux-sm btn-lux-primary" href="{{ path('app_rol_comision_registrar') }}">
                <i class="fa fa-plus"></i> Nuevo
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th width="1%">No.</th>
                <th>Nombre</th>
                <th width="12%" class="text-center">COPEP</th>
                <th width="12%" class="text-center">Estado</th>
                <th width="8%" class="text-center">Opciones</th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td>{{ loop.index }}</td>
                    <td>{{ item.nombre }}</td>
                    <td class="text-center">
                        ${badgePill('copep')}
                    </td>
                    <td class="text-center">
                        ${badgePill('activo')}
                    </td>
                    <td class="text-center">
                        ${dropdownCell([
  {type:'detail', route:'app_rol_comision_detail', title:'Detalles del rol'},
  {type:'link',   route:'app_rol_comision_modificar'},
  {divider:true},
  {type:'remove', route:'app_rol_comision_eliminar'},
])}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script>
        $(document).ready(function () {
${DROPDOWN_JS}
        });
    </script>
{% endblock %}
`;

// ─── comision/index.html.twig ─────────────────────────────────────────────
const comisionIndex = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Comisiones{% endblock %}

{% block body %}
<style>
    ${SHARED_CSS}
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-users label-icon"></i>Listado de comisiones</h3>
        <div class="ml-auto">
            <a title="Nuevo" class="btn-lux-sm btn-lux-primary" href="{{ path('app_comision_registrar') }}">
                <i class="fa fa-plus"></i> Nuevo
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th width="1%">No.</th>
                <th>Nombre</th>
                <th width="15%">Tipo de comisión</th>
                <th>Miembros</th>
                <th width="12%" class="text-center">Estado</th>
                <th width="8%" class="text-center">Opciones</th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td>{{ loop.index }}</td>
                    <td>{{ item.nombre }}</td>
                    <td>{{ item.tipoComision.nombre | default('') }}</td>
                    <td>
                        <ul class="list-inline mb-0">
                            {% for item1 in item.miembros %}
                                <li class="list-inline-item">
                                    <img title="{{ item1.miembro.primerNombre ~ ' ' ~ item1.miembro.segundoNombre ~' '~ item1.miembro.primerApellido ~ ' ' ~ item1.miembro.segundoApellido }}"
                                         src="{% if item1.miembro.foto != null %}{{ app.request.getSchemeAndHttpHost() }}/uploads/images/personas/{{ item1.miembro.foto }}{% else %}/assets/images/usuario.png{% endif %}"
                                         class="img-circle" alt="{{ item1.miembro.primerNombre }}"
                                         style="width:36px;height:36px;object-fit:cover;cursor:pointer;border:2px solid #e2e8f0;">
                                </li>
                            {% endfor %}
                        </ul>
                    </td>
                    <td class="text-center">
                        ${badgePill('activo')}
                    </td>
                    <td class="text-center">
                        ${dropdownCell([
  {type:'detail', route:'app_comision_detail', title:'Detalles de la comisión'},
  {type:'link',   route:'app_comision_modificar'},
  {divider:true},
  {type:'remove', route:'app_comision_eliminar'},
])}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script>
        $(document).ready(function () {
            localStorage.removeItem('nombre');
            localStorage.removeItem('descripcion');
            localStorage.removeItem('activo');
${DROPDOWN_JS}
        });
    </script>
{% endblock %}
`;

// ─── copep/index.html.twig ────────────────────────────────────────────────
const copepIndex = `{% extends 'baseAdmin.html.twig' %}

{% block title %}COPEP{% endblock %}

{% block body %}
<style>
    ${SHARED_CSS}
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-university label-icon"></i>Listado de COPEP</h3>
        <div class="ml-auto">
            <a title="Nuevo" class="btn-lux-sm btn-lux-primary" href="{{ path('app_copep_registrar') }}">
                <i class="fa fa-plus"></i> Nuevo
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th width="1%">No.</th>
                <th>Nombre</th>
                <th>Miembros</th>
                <th width="12%" class="text-center">Estado</th>
                <th width="8%" class="text-center">Opciones</th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td>{{ loop.index }}</td>
                    <td>{{ item.nombre }}</td>
                    <td>
                        <ul class="list-inline mb-0">
                            {% for item1 in item.miembros %}
                                <li class="list-inline-item">
                                    <img title="{{ item1.miembro.primerNombre ~ ' ' ~ item1.miembro.segundoNombre ~' '~ item1.miembro.primerApellido ~ ' ' ~ item1.miembro.segundoApellido }}"
                                         src="{% if item1.miembro.foto != null %}{{ app.request.getSchemeAndHttpHost() }}/uploads/images/personas/{{ item1.miembro.foto }}{% else %}/assets/images/usuario.png{% endif %}"
                                         class="img-circle" alt="{{ item1.miembro.primerNombre }}"
                                         style="width:36px;height:36px;object-fit:cover;cursor:pointer;border:2px solid #e2e8f0;">
                                </li>
                            {% endfor %}
                        </ul>
                    </td>
                    <td class="text-center">
                        ${badgePill('activo')}
                    </td>
                    <td class="text-center">
                        ${dropdownCell([
  {type:'detail', route:'app_copep_detail', title:'Detalles de la COPEP'},
  {type:'link',   route:'app_copep_modificar'},
  {divider:true},
  {type:'remove', route:'app_copep_eliminar'},
])}
                    </td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}

{% block javascripts %}
    {{ parent() }}
    <script>
        $(document).ready(function () {
            localStorage.removeItem('nombre');
            localStorage.removeItem('descripcion');
            localStorage.removeItem('activo');
${DROPDOWN_JS}
        });
    </script>
{% endblock %}
`;

// ═══════════════════════════════════════════════════════════════════════════
// NEW/EDIT FORM PROCESSOR
// ═══════════════════════════════════════════════════════════════════════════

function processForm(content) {
  // 1. Replace form_label/form_widget/form_errors for activo with pill toggle
  const activoBlock = `{{ form_label(form.activo) }}
                    {{ form_widget(form.activo) }}
                    {{ form_errors(form.activo) }}`;
  const activoBlock2 = `{{ form_label(form.activo) }}
                   {{ form_widget(form.activo) }}
                   {{ form_errors(form.activo) }}`;
  const activoBlock3 = `{{ form_label(form.activo) }}
                    {{ form_widget(form.activo) }}
                    {{ form_errors(form.activo) }}`;

  const pill = pillToggleHtml();

  let out = content;

  // Try all indent variants
  [activoBlock, activoBlock2, activoBlock3].forEach(pattern => {
    if (out.includes(pattern)) {
      out = out.replace(pattern, pill);
    }
  });

  // Also handle col-4 variant used in tipo_solicitud
  const activoBlock4 = `{{ form_label(form.activo) }}\n                    {{ form_widget(form.activo) }}\n                    {{ form_errors(form.activo) }}`;
  if (out.includes(activoBlock4)) {
    out = out.replace(activoBlock4, pill);
  }

  // 2. Add pill CSS to <style> block (before closing </style>)
  if (!out.includes('estado-toggle')) {
    out = out.replace('    </style>', PILL_CSS + '\n    </style>');
  }

  // 3. Add pill JS to {% block javascripts %}
  if (!out.includes('et-opt')) {
    out = out.replace(
      /(\{% block javascripts %\}\s*<script>\s*\$\(document\)\.ready\(function\(\) \{)/,
      `{% block javascripts %}\n    <script>\n        $(document).ready(function() {\n${PILL_JS}\n`
    );
    // If the above didn't match, try simpler insertion after <script>
    if (!out.includes(PILL_JS)) {
      out = out.replace(
        /(\{% block javascripts %\}\s*<script>)/,
        `{% block javascripts %}\n    <script>\n        $(document).ready(function() {\n${PILL_JS}\n        });`
      );
      // Actually, let's do it more carefully - just insert after opening <script> tag
    }
  }

  return out;
}

// Better form processor: inject pill JS into existing JS block
function processFormFile(filePath) {
  let content = fs.readFileSync(filePath, 'utf8');

  // Step 1: Replace activo form fields with pill toggle
  // Match: any whitespace + form_label/widget/errors for activo
  content = content.replace(
    /(\s*)(\{\{ form_label\(form\.activo\) \}\}\s*\{\{ form_widget\(form\.activo\) \}\}\s*\{\{ form_errors\(form\.activo\) \}\})/g,
    (match, indent) => {
      return '\n' + indent + pillToggleHtml();
    }
  );

  // Step 2: Add pill CSS before </style>
  if (!content.includes('estado-toggle')) {
    content = content.replace(/(\s*<\/style>)/, '\n' + PILL_CSS + '\n$1');
  }

  // Step 3: Inject pill JS handler into the {% block javascripts %} block
  if (!content.includes('et-opt')) {
    // Find {% block javascripts %} and inject pill JS after the first <script> opening
    content = content.replace(
      /({% block javascripts %}\s*<script>\s*)/,
      `{% block javascripts %}\n    <script>\n        $(document).ready(function() {\n${PILL_JS}\n        });\n\n`
    );

    // Fallback: find existing $(document).ready block
    if (!content.includes(PILL_JS)) {
      content = content.replace(
        /(\{% block javascripts %\}[\s\S]*?<script>[\s\S]*?\$\(document\)\.ready\(function\s*\(\)\s*\{)/,
        (match) => match + '\n' + PILL_JS
      );
    }

    // Last fallback: inject before first $ in JS block
    if (!content.includes(PILL_JS)) {
      content = content.replace(
        /({% block javascripts %}\n    <script>\n        )/,
        `{% block javascripts %}\n    <script>\n        $(document).ready(function() {\n${PILL_JS}\n        });\n\n        `
      );
    }
  }

  return content;
}

// ═══════════════════════════════════════════════════════════════════════════
// WRITE ALL FILES
// ═══════════════════════════════════════════════════════════════════════════

// Index files
fs.writeFileSync(path.join(BASE, 'rama_ciencia/index.html.twig'), ramaCienciaIndex);
console.log('✓ rama_ciencia/index');

fs.writeFileSync(path.join(BASE, 'tipo_programa/index.html.twig'), tipoProgramaIndex);
console.log('✓ tipo_programa/index');

fs.writeFileSync(path.join(BASE, 'tipo_solicitud/index.html.twig'), tipoSolicitudIndex);
console.log('✓ tipo_solicitud/index');

fs.writeFileSync(path.join(BASE, 'rol_comision/index.html.twig'), rolComisionIndex);
console.log('✓ rol_comision/index');

fs.writeFileSync(path.join(BASE, 'comision/index.html.twig'), comisionIndex);
console.log('✓ comision/index');

fs.writeFileSync(path.join(BASE, 'copep/index.html.twig'), copepIndex);
console.log('✓ copep/index');

// new/edit form files
const formFiles = [
  'rama_ciencia/new.html.twig',
  'rama_ciencia/edit.html.twig',
  'tipo_programa/new.html.twig',
  'tipo_programa/edit.html.twig',
  'tipo_solicitud/new.html.twig',
  'tipo_solicitud/edit.html.twig',
  'rol_comision/new.html.twig',
  'rol_comision/edit.html.twig',
  'comision/new.html.twig',
  'comision/edit.html.twig',
  'copep/new.html.twig',
  'copep/edit.html.twig',
];

formFiles.forEach(rel => {
  const fp = path.join(BASE, rel);
  if (!fs.existsSync(fp)) {
    console.log('  SKIP (not found):', rel);
    return;
  }
  const result = processFormFile(fp);
  fs.writeFileSync(fp, result);
  const hasToggle = result.includes('estado-toggle');
  const hasPillJs = result.includes('et-opt');
  console.log(`${hasToggle && hasPillJs ? '✓' : '⚠'} ${rel} (toggle:${hasToggle} js:${hasPillJs})`);
});

console.log('\nDone.');
