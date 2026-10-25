<?php

namespace App\Controller\RRHH;

use App\Entity\RRHH\AE2;
use App\Entity\RRHH\CategoriaDocente;
use App\Entity\Security\User;
use App\Form\RRHH\AE2Type;
use App\Form\RRHH\CategoriaDocenteType;
use App\Repository\Estructura\EstructuraRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\RRHH\AE2Repository;
use App\Repository\RRHH\CategoriaDocenteRepository;
use App\Repository\Security\UserRepository;
use App\Services\Utils;
use App\Services\UploadedDocumentStorage;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use function Symfony\Component\String\u;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_RRHH_REPORTE_AE2')"))]
#[Route('/rrhh/ae2')]
class AE2Controller extends AbstractController
{

    /**
     * @param AE2Repository $AE2Repository
     * @return Response
     */
    #[Route('/', name: 'app_rrhh_reporte_ae2_index', methods: ['GET'])]
    public function index(AE2Repository $AE2Repository, PersonaRepository $personaRepository, Utils $utils)
    {
        $persona = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()]);
        $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
        return $this->render('modules/rrhh/reporte/ae2/index.html.twig', [
            'registros' => $AE2Repository->findBy($esAdminRRHH ? [] : ['entidad' => $persona?->getEntidad()], ['anno' => 'DESC', 'mes' => 'ASC']),
        ]);
    }


    /**
     * @param Request $request
     * @param AE2Repository $aE2Repository
     * @return Response
     */
    #[Route('/registrar', name: 'app_rrhh_reporte_ae2_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, AE2Repository $aE2Repository, PersonaRepository $personaRepository, UploadedDocumentStorage $storage, Utils $utils, EntityManagerInterface $entityManager)
    {
        try {
            $ae2 = new AE2();
            if (!$ae2->getMes()) {
                $ae2->setMes((int)date('n'));
            }
            if (!$ae2->getAnno()) {
                $ae2->setAnno((int)date('Y'));
            }
            $persona = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()]);
            $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
            if (!$esAdminRRHH && $persona?->getEntidad()) {
                $ae2->setEntidad($persona->getEntidad());
                $ae2->setTotalPlantillaAprobada($this->obtenerPlantillaAprobada($entityManager, $persona->getEntidad()->getId()));
            }

            $form = $this->createForm(AE2Type::class, $ae2, ['action' => 'registrar', 'administrador_rrhh' => $esAdminRRHH]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $ae2->getEntidad()) {
                $ae2->setTotalPlantillaAprobada($this->obtenerPlantillaAprobada($entityManager, $ae2->getEntidad()->getId()));
            }
            if ($form->isSubmitted() && $form->isValid()) {
                if (($file = $form['documento']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $ae2->setDocumento($storage->storeName($file, 'uploads/rrhh/ae2/documento'));
                }

                if ($ae2->getEntidad()) {
                    $aE2Repository->add($ae2, true);
                    $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                    return $this->redirectToRoute('app_rrhh_reporte_ae2_index', [], Response::HTTP_SEE_OTHER);
                }
                $this->addFlash('error', 'Debe seleccionar una universidad válida.');
            }
            return $this->render('modules/rrhh/reporte/ae2/new.html.twig', [
                'form' => $form->createView(),
                'entidad' => $ae2->getEntidad(),
                'administrador_rrhh' => $esAdminRRHH,
                'plantillas_aprobadas' => $this->obtenerPlantillasAprobadas($entityManager),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_reporte_ae2_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param AE2 $ae2
     * @param AE2Repository $ae2Repository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_rrhh_reporte_ae2_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, AE2 $ae2, AE2Repository $ae2Repository, PersonaRepository $personaRepository, UploadedDocumentStorage $storage, Utils $utils)
    {
        try {
            $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
            $form = $this->createForm(AE2Type::class, $ae2, ['action' => 'modificar', 'administrador_rrhh' => $esAdminRRHH]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (($file = $form['documento']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $ae2->setDocumento($storage->replace($file, 'uploads/rrhh/ae2/documento', $ae2->getDocumento()));
                }
                if (empty($ae2->getEntidad())) {
                    $this->addFlash('error', 'La institucion no es correcta.');
                }
                $ae2Repository->edit($ae2);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_rrhh_reporte_ae2_index', [], Response::HTTP_SEE_OTHER);
            }
            $persona = $personaRepository->findBy(['usuario' => $this->getUser()->getId()]);
            $entidad = isset($persona[0]) ? $persona[0]->getEntidad() : null;
            return $this->render('modules/rrhh/reporte/ae2/edit.html.twig', [
                'form' => $form->createView(),
                'entidad' => $entidad,
                'administrador_rrhh' => $esAdminRRHH,
                'ae2' => $ae2
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_reporte_ae2_modificar', ['id' => $ae2->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param AE2 $ae2
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_rrhh_reporte_ae2_detail', methods: ['GET', 'POST'])]
    public function detail(AE2 $ae2)
    {
        return $this->render('modules/rrhh/reporte/ae2/detail.html.twig', [
            'item' => $ae2,
        ]);
    }

    /**
     * @param Request $request
     * @param AE2 $ae2
     * @param AE2Repository $ae2Repository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_rrhh_reporte_ae2_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, AE2 $ae2, AE2Repository $ae2Repository)
    {
        try {
            if ($ae2Repository->find($ae2) instanceof AE2) {
                $ae2Repository->remove($ae2, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_rrhh_reporte_ae2_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_rrhh_reporte_ae2_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_reporte_ae2_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    private function obtenerPlantillaAprobada(EntityManagerInterface $entityManager, int $entidadId): int
    {
        return (int) ($entityManager->getConnection()->fetchOne(
            'SELECT total FROM rrhh.tbd_plantilla_aprobada WHERE entidad_id = :entidad',
            ['entidad' => $entidadId]
        ) ?: 0);
    }

    private function obtenerPlantillasAprobadas(EntityManagerInterface $entityManager): array
    {
        $filas = $entityManager->getConnection()->fetchAllAssociative('SELECT entidad_id, total FROM rrhh.tbd_plantilla_aprobada');
        $resultado = [];
        foreach ($filas as $fila) {
            $resultado[(int) $fila['entidad_id']] = (int) $fila['total'];
        }
        return $resultado;
    }
}
