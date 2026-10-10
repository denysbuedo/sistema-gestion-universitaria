<?php

namespace App\Controller\Convenio;

use App\Entity\Convenio\Convenio;
use App\Entity\Convenio\Modalidad;
use App\Form\Convenio\ModalidadType;
use App\Repository\Convenio\ModalidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_MODALIDAD")
 */
#[Route('/convenio/modalidad')]
class ModalidadController extends AbstractController
{

    /**
     * @param ModalidadRepository $modalidadRepository
     * @return Response
     */
    #[Route('/', name: 'app_modalidad_index', methods: ['GET'])]
    public function index(ModalidadRepository $modalidadRepository)
    {
        return $this->render('modules/convenio/modalidad/index.html.twig', [
            'registros' => $modalidadRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param ModalidadRepository $modalidadRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_modalidad_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, ModalidadRepository $modalidadRepository)
    {
        try {
            $entidad = new Modalidad();
            $form = $this->createForm(ModalidadType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $modalidadRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_modalidad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/convenio/modalidad/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_modalidad_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Modalidad $modalidad
     * @param ModalidadRepository $modalidadRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_modalidad_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Modalidad $modalidad, ModalidadRepository $modalidadRepository)
    {
        try {
            $form = $this->createForm(ModalidadType::class, $modalidad, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $modalidadRepository->edit($modalidad);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_modalidad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/convenio/modalidad/edit.html.twig', [
                'form' => $form->createView(),
                'convenio' => $modalidad
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_modalidad_modificar', ['id' => $modalidad->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Modalidad $modalidad
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_modalidad_detail', methods: ['GET', 'POST'])]
    public function detail(Modalidad $modalidad)
    {
        return $this->render('modules/convenio/modalidad/detail.html.twig', [
            'item' => $modalidad,
        ]);
    }

    /**
     * @param Modalidad $modalidad
     * @param ModalidadRepository $modalidadRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_modalidad_eliminar', methods: ['GET'])]
    public function eliminar(Modalidad $modalidad, ModalidadRepository $modalidadRepository)
    {
        try {
            if ($modalidadRepository->find($modalidad) instanceof Modalidad) {
                $modalidadRepository->remove($modalidad, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_modalidad_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_modalidad_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_modalidad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
