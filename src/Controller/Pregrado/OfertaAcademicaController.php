<?php

namespace App\Controller\Pregrado;

use App\Entity\Institucion\Institucion;
use App\Repository\Pregrado\SolicitudProgramaAcademicoInstitucionRepository;
use App\Services\Utils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/pregrado/oferta_academica')]
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PROGAPROB') or is_granted('ROLE_PREGRADO_MIS_PROGRAMAS')"))]
final class OfertaAcademicaController extends AbstractController
{
    #[Route('/', name: 'app_pregrado_oferta_academica_index', methods: ['GET'])]
    public function index(
        SolicitudProgramaAcademicoInstitucionRepository $repository,
        EntityManagerInterface $entityManager,
        Utils $utils
    ): Response {
        $accesoGlobal = $this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_GEST_PROGAPROB');
        $institucion = $accesoGlobal ? null : $this->institucionUsuario($utils, $entityManager);
        if (!$accesoGlobal && !$institucion) {
            throw $this->createAccessDeniedException('Su usuario no está asociado a una universidad.');
        }

        $ofertas = $repository->findBy($institucion ? ['institucion' => $institucion] : [], ['id' => 'ASC']);
        $programaIds = [];
        foreach ($ofertas as $oferta) {
            $programaIds[] = $oferta->getSolicitudProgramaAcademico()->getId();
        }
        $sedes = [];
        foreach ($repository->getResumenSedes(array_values(array_unique($programaIds)), $institucion?->getId()) as $fila) {
            $ofertaId = (int) $fila['oferta_id'];
            $modalidadesCentral = json_decode((string) $fila['modalidades_sede_central'], true) ?: [];
            if ($modalidadesCentral !== []) {
                $sedes[$ofertaId]['Sede central'] = $modalidadesCentral;
            }
            if ($fila['cum_nombre']) {
                $sedes[$ofertaId][$fila['cum_nombre']][] = $fila['cum_modalidad'];
                $sedes[$ofertaId][$fila['cum_nombre']] = array_values(array_unique($sedes[$ofertaId][$fila['cum_nombre']]));
            }
        }

        return $this->render('modules/pregrado/oferta_academica/index.html.twig', [
            'ofertas' => $ofertas,
            'sedes' => $sedes,
            'accesoGlobal' => $accesoGlobal,
            'institucion' => $institucion,
        ]);
    }

    private function institucionUsuario(Utils $utils, EntityManagerInterface $entityManager): ?Institucion
    {
        $datos = $utils->obtenerDatosEstructuraUsuarioAutenticado($this->getUser()->getId());
        $idEntidad = $datos['id_entidad'] ?? null;

        return $idEntidad && $idEntidad > 0
            ? $entityManager->getRepository(Institucion::class)->findOneBy(['estructura' => $idEntidad])
            : null;
    }
}
