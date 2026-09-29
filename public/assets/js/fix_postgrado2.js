const fs = require('fs');
const path = require('path');

const BASE = 'd:/Proyectos/Personales/akademos_mes/templates/modules/postgrado';

const SHARED_CSS = `    .card-luxury { border-radius:12px; border:none; box-shadow:0 4px 20px rgba(0,0,0,0.08); border-top:4px solid #134679; margin-bottom:1.5rem; }
    .card-luxury .card-header { background-color:#fff; border-bottom:1px solid #f1f5f9; padding:15px 24px; }
    .card-luxury .card-title { color:#1a2e44; font-weight:700; font-size:1rem; margin:0; float:none !important; }
    .label-icon { color:#134679; margin-right:6px; opacity:0.8; }
    .config-table th { background:#f1f5f9; color:#4a5568; font-size:0.82rem; font-weight:700; border-color:#e2e8f0; padding:8px 10px; }
    .config-table td { font-size:0.85rem; color:#1a2e44; border-color:#e2e8f0; padding:7px 10px; vertical-align:middle; }
    .config-table tbody tr:hover { background:#f8fafc; }
    .btn-lux-sm { padding:6px 14px; border-radius:6px; font-weight:700; font-size:0.75rem; border:none; display:inline-flex; align-items:center; gap:5px; text-transform:uppercase; text-decoration:none; cursor:pointer; transition:all 0.2s; }
    .btn-lux-primary { background:#134679; color:#fff !important; }
    .btn-lux-primary:hover { background:#0f3560; }
    .btn-lux-success { background:#16a34a; color:#fff !important; }
    .btn-lux-success:hover { background:#15803d; }
    .btn-action-sm { width:28px; height:28px; padding:0; display:inline-flex; justify-content:center; align-items:center; border-radius:6px; font-size:0.75rem; border:none; transition:all 0.2s; color:#fff; margin:0 2px; text-decoration:none; }
    .btn-action-primary { background:#3b82f6; }
    .btn-action-primary:hover { background:#2563eb; transform:translateY(-1px); }
    .btn-action-warning { background:#eab308; color:#1e293b !important; }
    .btn-action-warning:hover { background:#ca8a04; transform:translateY(-1px); }
    .btn-action-danger { background:#ef4444; }
    .btn-action-danger:hover { background:#dc2626; transform:translateY(-1px); }`;

const DROPDOWN_JS = `            $(document).on('show.bs.dropdown', 'table .dropdown', function () {
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

// ─── solicitud_programa/index.html.twig ──────────────────────────────────
const solicitudProgramaIndex = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Solicitudes de programas{% endblock %}

{% block body %}
<style>
${SHARED_CSS}
    .responsive-input { width:100%; font-size:0.78rem; padding:3px 6px; border:1px solid #e2e8f0; border-radius:4px; }
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-file-signature label-icon"></i>Listado de solicitudes de programas</h3>
        <div class="ml-auto d-flex" style="gap:8px;">
            <a id="exportExcel" title="Exportar a Excel" class="btn-lux-sm btn-lux-success">
                <i class="fa fa-file-excel"></i> Excel
            </a>
            <a title="Nuevo" class="btn-lux-sm btn-lux-primary" href="{{ path('app_solicitud_programa_registrar') }}">
                <i class="fa fa-plus"></i> Nuevo
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th>No.</th>
                <th>Nombre</th>
                <th>Tipo de programa</th>
                <th>Rama de la ciencia</th>
                <th>Institución solicitante</th>
                <th>Coordinador</th>
                <th>Estado</th>
                <th class="text-center">Opciones</th>
            </tr>
            <tr>
                <th></th>
                <th><input type="text" placeholder="Filtrar por Nombre" class="responsive-input" /></th>
                <th><input type="text" placeholder="Filtrar por Tipo" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Rama" class="responsive-input" /></th>
                <th><input type="text" placeholder="Filtrar por Institución" class="responsive-input" /></th>
                <th><input type="text" placeholder="Filtrar por Coordinador" class="responsive-input" /></th>
                <th><input type="text" placeholder="Filtrar por Estado" class="responsive-input" /></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td width="1%">{{ loop.index }}</td>
                    <td>{{ item.nombre | default('') }}</td>
                    <td>{{ item.tipoPrograma.nombre | default('') }}</td>
                    <td>{{ item.ramaCiencia.nombre | default('') }}</td>
                    <td>{{ item.universidad.nombre | default('') }}</td>
                    <td>{{ item.nombreCoordinador | default('') }}</td>
                    <td>{{ item.estadoPrograma.nombre | default('') }}</td>
                    <td class="text-center" style="white-space:nowrap;">
                        <div class="dropdown d-flex justify-content-center">
                            <button class="btn btn-sm btn-light rounded-circle shadow-sm border-0 p-0 d-flex align-items-center justify-content-center"
                                    style="width:32px;height:32px;background:#f1f5f9;color:#64748b;font-size:1rem;line-height:1;"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                &#8942;
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm" style="border-radius:10px;border:none;min-width:210px;padding:6px 0;font-size:0.85rem;">
                                <button class="dropdown-item modalDetailAction" style="padding:8px 16px;"
                                        data-url="{{ path('app_solicitud_programa_detail', {'id': item.id}) }}"
                                        data-title="Detalles de la solicitud del programa">
                                    <i class="fa fa-eye fa-fw mr-2 text-primary"></i> Ver detalles
                                </button>
                                {% if item.tipoSolicitud.id is defined and item.tipoSolicitud.id == 1 %}
                                    <a class="dropdown-item" style="padding:8px 16px;"
                                       href="{{ path('app_solicitud_programa_asignar_comision', {'id': item.id, 'tipo':'2'}) }}">
                                        <i class="fa fa-address-book fa-fw mr-2 text-info"></i> Asignar comisión coordinadora
                                    </a>
                                {% endif %}
                                {% if item.estadoPrograma.id is defined and item.estadoPrograma.id != 4 and item.estadoPrograma.id != 5 and item.estadoPrograma.id != 7 %}
                                    <a class="dropdown-item" style="padding:8px 16px;"
                                       href="{{ path('app_solicitud_programa_modificar', {'id': item.id}) }}">
                                        <i class="fa fa-edit fa-fw mr-2 text-warning"></i> Modificar
                                    </a>
                                {% endif %}
                                {% if item.estadoPrograma.id is defined and item.estadoPrograma.id == 1 %}
                                    <a class="dropdown-item" style="padding:8px 16px;"
                                       href="{{ path('app_solicitud_programa_asignar_comision', {'id': item.id}) }}">
                                        <i class="fa fa-address-card fa-fw mr-2 text-secondary"></i> Asignar comisión
                                    </a>
                                {% endif %}
                                {% if item.estadoPrograma.id is defined and item.estadoPrograma.id == 2 %}
                                    <a class="dropdown-item" style="padding:8px 16px;"
                                       href="{{ path('app_solicitud_programa_asociar_dictamen', {'id': item.id}) }}">
                                        <i class="fa fa-paperclip fa-fw mr-2 text-secondary"></i> Dictámenes
                                    </a>
                                {% endif %}
                                {% if item.estadoPrograma.id is defined and item.estadoPrograma.id == 3 %}
                                    <a class="dropdown-item" style="padding:8px 16px;"
                                       href="{{ path('app_solicitud_programa_revisar_dictamenes', {'id': item.id}) }}">
                                        <i class="fa fa-book-open fa-fw mr-2 text-secondary"></i> Revisar dictámenes
                                    </a>
                                {% endif %}
                                {% if item.estadoPrograma.id is defined and item.estadoPrograma.id == 4 %}
                                    <a class="dropdown-item" style="padding:8px 16px;"
                                       href="{{ path('app_solicitud_programa_votacion', {'id': item.id}) }}">
                                        <i class="fa fa-hand-pointer fa-fw mr-2 text-success"></i> Votar
                                    </a>
                                {% endif %}
                                {% if item.estadoPrograma.id is defined and item.estadoPrograma.id == 5 %}
                                    <a class="dropdown-item" style="padding:8px 16px;"
                                       href="{{ path('app_solicitud_programa_aprobar', {'id': item.id}) }}">
                                        <i class="fa fa-check fa-fw mr-2 text-success"></i> Aprobar
                                    </a>
                                {% endif %}
                                {% if item.estadoPrograma.id is defined and item.estadoPrograma.id == 6 %}
                                    <a class="dropdown-item" style="padding:8px 16px;"
                                       href="{{ path('app_solicitud_programa_no_aprobar', {'id': item.id}) }}">
                                        <i class="fa fa-times fa-fw mr-2 text-danger"></i> Rechazar
                                    </a>
                                {% endif %}
                                <div class="dropdown-divider my-1"></div>
                                <button class="dropdown-item elementRemove text-danger" style="padding:8px 16px;"
                                        data-url="{{ path('app_solicitud_programa_eliminar', {'id': item.id}) }}">
                                    <i class="fa fa-trash fa-fw mr-2"></i> Eliminar
                                </button>
                            </div>
                        </div>
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

// ─── programas_aprobados/index.html.twig ─────────────────────────────────
const programasAprobadosIndex = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Programas aprobados{% endblock %}

{% block body %}
<style>
${SHARED_CSS}
    .responsive-input { width:100%; font-size:0.78rem; padding:3px 6px; border:1px solid #e2e8f0; border-radius:4px; }
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0">
            <i class="fas fa-graduation-cap label-icon"></i>
            {% if app.request.getRequestUri() == '/postgrado/programas_aprobados/formacion_academica' %}
                Listado de Maestrías y Especialidades
            {% elseif app.request.getRequestUri() == '/postgrado/programas_aprobados/grados_cientificos' %}
                Listado de Doctorados
            {% else %}
                Programas aprobados
            {% endif %}
        </h3>
        <div class="ml-auto">
            <a id="exportExcel" title="Exportar a Excel" class="btn-lux-sm btn-lux-success">
                <i class="fa fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th>No.</th>
                <th>Ministerio</th>
                <th>Nombre</th>
                <th>Tipo de programa</th>
                <th>Universidad</th>
                <th>Coordinador</th>
                <th>Categoría de acreditación</th>
                <th class="text-center">Opciones</th>
            </tr>
            <tr>
                <th></th>
                <th><input type="text" placeholder="Filtrar por Ministerio" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Nombre" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Tipo" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Universidad" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Coordinador" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Categoría" class="responsive-input"/></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td width="1%">{{ loop.index }}</td>
                    <td>{{ item.universidad.estructura.nombre | default('') }}</td>
                    <td>{{ item.nombre }}</td>
                    <td>{{ item.tipoPrograma.nombre | default('') }}</td>
                    <td>{{ item.universidad.nombre | default('') }}</td>
                    <td>{{ item.nombreCoordinador | default('') }}</td>
                    <td>{{ item.categoriaAcreditacion.nombre | default('') }}</td>
                    <td class="text-center">
                        <div class="dropdown d-flex justify-content-center">
                            <button class="btn btn-sm btn-light rounded-circle shadow-sm border-0 p-0 d-flex align-items-center justify-content-center"
                                    style="width:32px;height:32px;background:#f1f5f9;color:#64748b;font-size:1rem;line-height:1;"
                                    type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                &#8942;
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow-sm" style="border-radius:10px;border:none;min-width:200px;padding:6px 0;font-size:0.85rem;">
                                <button class="dropdown-item modalDetailAction" style="padding:8px 16px;"
                                        data-url="{{ path('app_solicitud_programa_detail', {'id': item.id}) }}"
                                        data-title="Detalles del programa">
                                    <i class="fa fa-eye fa-fw mr-2 text-primary"></i> Ver detalles
                                </button>
                                <a class="dropdown-item" style="padding:8px 16px;"
                                   href="{{ path('app_solicitud_programa_aprobar', {'id': item.id}) }}">
                                    <i class="fa fa-check fa-fw mr-2 text-success"></i> Completar información
                                </a>
                                <a class="dropdown-item" style="padding:8px 16px;" target="_blank"
                                   href="{{ app.request.getSchemeAndHttpHost() }}/uploads/resolucion_programa/{{ item.resolucionPrograma }}">
                                    <i class="fa fa-download fa-fw mr-2 text-secondary"></i> Descargar resolución
                                </a>
                                <a class="dropdown-item" style="padding:8px 16px;" target="_blank"
                                   href="{{ app.request.getSchemeAndHttpHost() }}/uploads/dictamen_final/{{ item.dictamenFinal }}">
                                    <i class="fa fa-file-download fa-fw mr-2 text-secondary"></i> Descargar dictámen final
                                </a>
                            </div>
                        </div>
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

// ─── reportes/grado_cientifico/index.html.twig ────────────────────────────
const reporteGradoCientifico = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Listado de Doctorados{% endblock %}

{% block body %}
<style>
${SHARED_CSS}
    .responsive-input { width:100%; font-size:0.78rem; padding:3px 6px; border:1px solid #e2e8f0; border-radius:4px; }
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-graduation-cap label-icon"></i>Listado de Doctorados</h3>
        <div class="ml-auto">
            <a id="exportExcel" title="Exportar a Excel" class="btn-lux-sm btn-lux-success">
                <i class="fa fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th>No.</th>
                <th>Ministerio</th>
                <th>Programa</th>
                <th>Tipo de programa</th>
                <th>Universidad</th>
                <th>Categoría de acreditación</th>
                <th>Rama de la Ciencia</th>
            </tr>
            <tr>
                <th></th>
                <th><input type="text" placeholder="Filtrar por Ministerio" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Programa" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Tipo" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Universidad" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Categoría" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Rama" class="responsive-input"/></th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td width="1%">{{ loop.index }}</td>
                    <td>{{ item.universidad.estructura.nombre | default('') }}</td>
                    <td>{{ item.nombre }}</td>
                    <td>{{ item.tipoPrograma.nombre | default('') }}</td>
                    <td>{{ item.universidad.nombre | default('') }}</td>
                    <td>{{ item.categoriaAcreditacion.nombre | default('') }}</td>
                    <td>{{ item.ramaCiencia.nombre | default('') }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}
`;

// ─── reportes/formacion_academica/index.html.twig ─────────────────────────
const reporteFormacionAcademica = `{% extends 'baseAdmin.html.twig' %}

{% block title %}Listado de Maestrías y Especialidades{% endblock %}

{% block body %}
<style>
${SHARED_CSS}
    .responsive-input { width:100%; font-size:0.78rem; padding:3px 6px; border:1px solid #e2e8f0; border-radius:4px; }
</style>

{% include 'pages/modalDetail.html.twig' %}

<div class="card card-luxury">
    <div class="card-header d-flex align-items-center">
        <h3 class="card-title text-bold m-0"><i class="fas fa-book label-icon"></i>Listado de Maestrías y Especialidades</h3>
        <div class="ml-auto">
            <a id="exportExcel" title="Exportar a Excel" class="btn-lux-sm btn-lux-success">
                <i class="fa fa-file-excel"></i> Excel
            </a>
        </div>
    </div>
    <div class="card-body px-4">
        <table class="table config-table table-bordered table-striped dataTable">
            <thead>
            <tr>
                <th>No.</th>
                <th>Ministerio</th>
                <th>Programa</th>
                <th>Tipo de programa</th>
                <th>Universidad</th>
                <th>Categoría de acreditación</th>
                <th>Rama de la Ciencia</th>
            </tr>
            <tr>
                <th></th>
                <th><input type="text" placeholder="Filtrar por Ministerio" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Programa" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Tipo" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Universidad" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Categoría" class="responsive-input"/></th>
                <th><input type="text" placeholder="Filtrar por Rama" class="responsive-input"/></th>
            </tr>
            </thead>
            <tbody>
            {% for item in registros %}
                <tr>
                    <td width="1%">{{ loop.index }}</td>
                    <td>{{ item.universidad.estructura.nombre | default('') }}</td>
                    <td>{{ item.nombre }}</td>
                    <td>{{ item.tipoPrograma.nombre | default('') }}</td>
                    <td>{{ item.universidad.nombre | default('') }}</td>
                    <td>{{ item.categoriaAcreditacion.nombre | default('') }}</td>
                    <td>{{ item.ramaCiencia.nombre | default('') }}</td>
                </tr>
            {% endfor %}
            </tbody>
        </table>
    </div>
</div>
{% endblock %}
`;

// ─── centro_autorizado/new.html.twig — update footer buttons ─────────────
function updateFormFooter(content, cancelRoute) {
  // Replace old btn btn-danger / btn btn-primary footer with lux style
  return content
    .replace(
      /<a class="btn btn-danger mr-2 btn-cancel"\s*data-url="{{ path\('[^']+'\) }}"\s*data-title=""\s*data-content="[^"]+"\s*type="button">Cancelar<\/a>/,
      `<a class="btn-lux-sm" style="background:#f1f5f9;color:#475569;font-weight:700;font-size:0.85rem;padding:10px 24px;border-radius:8px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;" href="{{ path('${cancelRoute}') }}">
                <i class="fas fa-times"></i> Cancelar
            </a>`
    )
    .replace(
      /<button class="btn btn-primary">Aceptar<\/button>/,
      `<button type="submit" class="btn-lux-sm btn-lux-primary" style="font-size:0.85rem;padding:10px 24px;border-radius:8px;">
                <i class="fas fa-check"></i> Aceptar
            </button>`
    );
}

// Write index files
fs.writeFileSync(path.join(BASE, 'solicitud_programa/index.html.twig'), solicitudProgramaIndex);
console.log('✓ solicitud_programa/index');

fs.writeFileSync(path.join(BASE, 'programas_aprobados/index.html.twig'), programasAprobadosIndex);
console.log('✓ programas_aprobados/index');

fs.writeFileSync(path.join(BASE, 'reportes/grado_cientifico/index.html.twig'), reporteGradoCientifico);
console.log('✓ reportes/grado_cientifico/index');

fs.writeFileSync(path.join(BASE, 'reportes/formacion_academica/index.html.twig'), reporteFormacionAcademica);
console.log('✓ reportes/formacion_academica/index');

// Update form footers
const centroFile = path.join(BASE, 'centro_autorizado/new.html.twig');
let centroContent = fs.readFileSync(centroFile, 'utf8');
centroContent = updateFormFooter(centroContent, 'app_postgrado_centro_autorizado_postgrado_index');
fs.writeFileSync(centroFile, centroContent);
console.log('✓ centro_autorizado/new');

const formacionFile = path.join(BASE, 'formacion_doctores/new.html.twig');
let formacionContent = fs.readFileSync(formacionFile, 'utf8');
formacionContent = updateFormFooter(formacionContent, 'app_postgrado_formacion_doctores_index');
fs.writeFileSync(formacionFile, formacionContent);
console.log('✓ formacion_doctores/new');

console.log('\nDone.');
