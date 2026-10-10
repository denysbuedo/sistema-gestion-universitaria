<?php

namespace App\Controller\Institucion;

use App\Entity\Institucion\TipoInstitucion;
use App\Entity\Security\User;
use App\Form\Institucion\TipoInstitucionType;
use App\Repository\Institucion\TipoInstitucionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_TIPINST")
 */
#[Route('/institucion/tipo_institucion')]
class TipoInstitucionController extends AbstractController
{

    /**
     * @param TipoInstitucionRepository $tipoInstitucionRepository
     * @return Response
     */
    #[Route('/', name: 'app_tipo_institucion_index', methods: ['GET'])]
    public function index(TipoInstitucionRepository $tipoInstitucionRepository)
    {
        return $this->render('modules/institucion/tipo_institucion/index.html.twig', [
            'registros' => $tipoInstitucionRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param TipoInstitucionRepository $tipoInstitucionRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_tipo_institucion_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, TipoInstitucionRepository $tipoInstitucionRepository)
    {
        try {
            $catDocenteEntity = new TipoInstitucion();
            $form = $this->createForm(TipoInstitucionType::class, $catDocenteEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tipoInstitucionRepository->add($catDocenteEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_institucion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/institucion/tipo_institucion/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_institucion_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $tipoInstitucion
     * @param TipoInstitucionRepository $tipoInstitucionRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_tipo_institucion_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, TipoInstitucion $tipoInstitucion, TipoInstitucionRepository $tipoInstitucionRepository)
    {
        try {
            $form = $this->createForm(TipoInstitucionType::class, $tipoInstitucion, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $tipoInstitucionRepository->edit($tipoInstitucion);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_institucion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/institucion/tipo_institucion/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_institucion_modificar', ['id' => $tipoInstitucion], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $tipoInstitucion
     * @param TipoInstitucionRepository $tipoInstitucionRepository
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_tipo_institucion_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, TipoInstitucion $tipoInstitucion)
    {
        return $this->render('modules/institucion/tipo_institucion/detail.html.twig', [
            'item' => $tipoInstitucion,
        ]);
    }

    /**
     * @param Request $request
     * @param TipoInstitucion $tipoInstitucion
     * @param TipoInstitucionRepository $tipoInstitucionRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_tipo_institucion_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, TipoInstitucion $tipoInstitucion, TipoInstitucionRepository $tipoInstitucionRepository)
    {
        try {
            if ($tipoInstitucionRepository->find($tipoInstitucion) instanceof TipoInstitucion) {
                $tipoInstitucionRepository->remove($tipoInstitucion, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_institucion_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_tipo_institucion_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_institucion_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
