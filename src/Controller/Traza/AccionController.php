<?php

namespace App\Controller\Traza;

use App\Entity\Traza\Accion;
use App\Entity\Security\User;
use App\Form\Traza\AccionType;
use App\Repository\Traza\AccionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_ACCTION_TRZ")
 */
#[Route('/traza/accion')]
class AccionController extends AbstractController
{

    /**
     * @param AccionRepository $accionRepository
     * @return Response
     */
    #[Route('/', name: 'app_accion_index', methods: ['GET'])]
    public function index(AccionRepository $accionRepository)
    {

        return $this->render('modules/traza/accion/index.html.twig', [
            'registros' => $accionRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);

    }

    /**
     * @param Request $request
     * @param AccionRepository $accionRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_accion_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, AccionRepository $accionRepository)
    {
        try {
            $catDocenteEntity = new Accion();
            $form = $this->createForm(AccionType::class, $catDocenteEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $accionRepository->add($catDocenteEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_accion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/traza/accion/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_accion_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Accion $accion
     * @param AccionRepository $accionRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_accion_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Accion $accion, AccionRepository $accionRepository)
    {
        try {
            $form = $this->createForm(AccionType::class, $accion, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $accionRepository->edit($accion, true);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_accion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/traza/accion/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_accion_modificar', ['id' => $accion], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $accion
     * @param AccionRepository $accionRepository
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_accion_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, Accion $accion)
    {
        return $this->render('modules/traza/accion/detail.html.twig', [
            'item' => $accion,
        ]);
    }

    /**
     * @param Request $request
     * @param Accion $accion
     * @param AccionRepository $accionRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_accion_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, Accion $accion, AccionRepository $accionRepository)
    {
        try {
            if ($accionRepository->find($accion) instanceof Accion) {
                $accionRepository->remove($accion, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_accion_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_accion_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_accion_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
