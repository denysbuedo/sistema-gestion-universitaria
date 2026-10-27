<?php

namespace App\Security;

use App\Entity\Personal\Persona;
use App\Entity\Personal\PersonaOrganizacion;
use App\Entity\Security\User;
use App\Services\TraceService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class UserSessionInitializer
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack,
        private TraceService $traceService,
        private string $accionInicioSesion,
        private string $objetoAutenticacion,
        private string $tipoTrazaSesion
    ) {
    }

    public function initialize(User $user): void
    {
        $persona = $this->entityManager->getRepository(Persona::class)->findOneBy(['usuario' => $user->getId()]);
        $session = $this->requestStack->getSession();
        $session->set('id_usuario_autenticado', $user->getId());
        $session->set('usuario_autenticado_role_admin', in_array('ROLE_ADMIN', $user->getRoles(), true));
        $session->set('password_change_first_time', $user->getPasswordChangeFirstTime());
        [$rolVisible, $esAdministradorProceso] = $this->resolverIdentidadInstitucional($user);
        $session->set('rol_usuario_autenticado', $rolVisible);
        $session->set('administrador_proceso_usuario_autenticado', $esAdministradorProceso);

        if (!$persona instanceof Persona) {
            $session->set('nombre_usuario_autenticado', $user->getUsername());
            $session->set('entidad_usuario_autenticado', $esAdministradorProceso ? 'Ministerio de Educación Superior' : null);
            $session->set('id_persona_usuario_autenticado', -1);
            return;
        }

        $siglas = [];
        foreach ($this->entityManager->getRepository(PersonaOrganizacion::class)->findBy(['persona' => $persona->getId()]) as $afiliacion) {
            $siglas[] = $afiliacion->getOrganizacion()->getSiglas();
        }
        $session->set('foto_usuario_autenticado', $persona->getFoto());
        $session->set('nombre_usuario_autenticado', trim(implode(' ', array_filter([
            $persona->getPrimerNombre(), $persona->getSegundoNombre(),
            $persona->getPrimerApellido(), $persona->getSegundoApellido(),
        ]))));
        $session->set('responsabilidad_usuario_autenticado', $persona->getResponsabilidad() ? $persona->getResponsabilidad()->getNombre() : null);
        $session->set('entidad_usuario_autenticado', $esAdministradorProceso ? 'Ministerio de Educación Superior' : $persona->getEntidad()?->getNombre());
        $estructura = $persona->getEstructura();
        $session->set('estructura_usuario_autenticado', $estructura ? $estructura->getId() : null);
        $session->set('nombre_estructura_usuario_autenticado', $estructura ? $estructura->getNombre() : null);
        $session->set('estructura_padre_usuario_autenticado', $estructura && $estructura->getEstructura() ? $estructura->getEstructura()->getNombre() : null);
        $session->set('id_persona_usuario_autenticado', $persona->getId());
        $session->set('provincia_usuario_autenticado', $persona->getProvincia() ? $persona->getProvincia()->getNombre() : null);
        $session->set('municipio_usuario_autenticado', $persona->getMunicipio() ? $persona->getMunicipio()->getNombre() : null);
        $session->set('grado_cientifico_usuario_autenticado', $persona->getGradoAcademico() ? $persona->getGradoAcademico()->getNombre() : null);
        $session->set('afiliaciones_usuario_autenticado', implode(', ', $siglas));

        $this->traceService->registrar(
            $this->accionInicioSesion,
            $this->objetoAutenticacion, null, null,
            $this->tipoTrazaSesion
        );
    }

    /** @return array{0:string,1:bool} */
    private function resolverIdentidadInstitucional(User $user): array
    {
        $homologados = [
            'ROLE_ADMIN' => 'Administrador del sistema',
            'ROL_RRHH' => 'Administrador de Recursos Humanos',
            'ROL_PREGRADO' => 'Administrador de Pregrado',
            'ROL_POSGRADO' => 'Administrador de Posgrado',
            'ROL_STRUCT' => 'Administrador de Organización Institucional',
            'ROL_PERSON' => 'Administrador de Personal',
            'ROL_EVALUACION' => 'Administrador de Evaluación',
            'ROL_INFORMATIZACION' => 'Administrador de Informatización',
        ];
        $nombres = [
            'Director de RRHH' => 'Director de Recursos Humanos',
            'Director de Pregrado' => 'Director de Pregrado',
            'Director de Posgrado' => 'Director de Posgrado',
        ];
        $roles = $user->getUserRoles();
        foreach ($homologados as $clave => $nombre) {
            foreach ($roles as $rol) {
                if ($rol->getRoleKey() === $clave) {
                    return [$nombre, true];
                }
            }
        }
        foreach ($roles as $rol) {
            if (str_starts_with((string) $rol->getNombre(), 'Administrador')) {
                return [(string) $rol->getNombre(), true];
            }
        }
        foreach ($roles as $rol) {
            if (isset($nombres[$rol->getNombre()])) {
                return [$nombres[$rol->getNombre()], false];
            }
        }
        foreach ($roles as $rol) {
            if ($rol->getActivo()) {
                return [(string) $rol->getNombre(), false];
            }
        }
        return ['Usuario del sistema', false];
    }
}
