<?php

namespace App\Controller\Personal;

use App\Entity\Personal\Organizacion;
use App\Entity\Security\User;
use App\Form\Personal\OrganizacionType;
use App\Repository\Personal\OrganizacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_ORGANZ")
 */
#[Route('/personal/organizacion')]
class OrganizacionController extends AbstractController
{

    /**
     * @param OrganizacionRepository $organizacionRepository
     * @return Response
     */
    #[Route('/', name: 'app_organizacion_index', methods: ['GET'])]
    public function index(OrganizacionRepository $organizacionRepository)
    {
        try {
            return $this->render('modules/personal/organizacion/index.html.twig', [
                'registros' => $organizacionRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_organizacion_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param OrganizacionRepository $organizacionRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_organizacion_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, OrganizacionRepository $organizacionRepository)
    {
//        try {
            $catOrganizacionEntity = new Organizacion();
            $form = $this->createForm(OrganizacionType::class, $catOrganizacionEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $organizacionRepository->add($catOrganizacionEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_organizacion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/personal/organizacion/new.html.twig', [
                'form' => $form->createView(),
            ]);
//        } catch (\Exception $exception) {
//            $this->addFlash('error', $exception->getMessage());
//            return $this->redirectToRoute('app_organizacion_registrar', [], Response::HTTP_SEE_OTHER);
//        }
    }


    /**
     * @param Request $request
     * @param User $organizacion
     * @param OrganizacionRepository $organizacionRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_organizacion_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Organizacion $organizacion, OrganizacionRepository $organizacionRepository)
    {
        try {
            $form = $this->createForm(OrganizacionType::class, $organizacion, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $organizacionRepository->edit($organizacion);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_organizacion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/personal/organizacion/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_organizacion_modificar', ['id' => $organizacion], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Organizacion $organizacion
     * @param OrganizacionRepository $organizacionRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_organizacion_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, Organizacion $organizacion, OrganizacionRepository $organizacionRepository)
    {
        try {
            if ($organizacionRepository->find($organizacion) instanceof Organizacion) {
                $organizacionRepository->remove($organizacion, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_organizacion_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_organizacion_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_organizacion_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Organizacion $organizacion
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_organizacion_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, Organizacion $organizacion)
    {
        return $this->render('modules/personal/organizacion/detail.html.twig', [
            'item' => $organizacion,
        ]);
    }
}
