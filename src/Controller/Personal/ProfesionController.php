<?php

namespace App\Controller\Personal;

use App\Entity\Personal\Profesion;
use App\Entity\Security\User;
use App\Form\Personal\ProfesionType;
use App\Repository\Personal\ProfesionRepository;
use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_PROF')"))]
#[Route('/personal/profesion')]
class ProfesionController extends AbstractController
{

    /**
     * @param ProfesionRepository $profesionRepository
     * @return Response
     */
    #[Route('/', name: 'app_profesion_index', methods: ['GET'])]
    public function index(ProfesionRepository $profesionRepository)
    {
        try {
            return $this->render('modules/personal/profesion/index.html.twig', [
                'registros' => $profesionRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_profesion_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param ProfesionRepository $profesionRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_profesion_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, ProfesionRepository $profesionRepository, Utils $utils)
    {
        try {
            $profesionEntity = new Profesion();
            $form = $this->createForm(ProfesionType::class, $profesionEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $profesionRepository->add($profesionEntity, true);

                $utils->actualizarProfesionDri($profesionEntity);

                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_profesion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/personal/profesion/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_profesion_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Profesion $profesion
     * @param ProfesionRepository $profesionRepository
     * @param Utils $utils
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_profesion_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Profesion $profesion, ProfesionRepository $profesionRepository, Utils $utils)
    {
        try {
            $form = $this->createForm(ProfesionType::class, $profesion, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $profesionRepository->edit($profesion);

                $utils->actualizarProfesionDri($profesion);

                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_profesion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/personal/profesion/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_profesion_modificar', ['id' => $profesion], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Profesion $profesion
     * @param ProfesionRepository $profesionRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_profesion_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, Profesion $profesion, ProfesionRepository $profesionRepository, Utils $utils)
    {
        try {
            if ($profesionRepository->find($profesion) instanceof Profesion) {
                $profesionRepository->remove($profesion, true);

                $utils->actualizarProfesionDri($profesion, true);

                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_profesion_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_profesion_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_profesion_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param User $profesion
     * @param CategoriaDocenteRepository $categoriaDocenteRepository
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_profesion_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, Profesion $profesion)
    {
        return $this->render('modules/personal/profesion/detail.html.twig', [
            'item' => $profesion,
        ]);
    }
}
