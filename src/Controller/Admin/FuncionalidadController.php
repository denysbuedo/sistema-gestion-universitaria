<?php

namespace App\Controller\Admin;

use App\Entity\Security\Funcionalidad;
use App\Entity\Security\User;
use App\Form\Admin\FuncionalidadType;
use App\Repository\Security\FuncionalidadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_FUNC')"))]
#[Route('/administracion/funcionalidad')]
class FuncionalidadController extends AbstractController
{

    /**
     * @param FuncionalidadRepository $funcionalidadRepository
     * @return Response
     */
    #[Route('/', name: 'app_funcionalidad_index', methods: ['GET'])]
    public function index(FuncionalidadRepository $funcionalidadRepository)
    {
        try {
            return $this->render('modules/admin/funcionalidad/index.html.twig', [
                'registros' => $funcionalidadRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_funcionalidad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Funcionalidad $funcionalidad
     * @param FuncionalidadRepository $funcionalidadRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_funcionalidad_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Funcionalidad $funcionalidad, FuncionalidadRepository $funcionalidadRepository)
    {
        try {
            $form = $this->createForm(FuncionalidadType::class, $funcionalidad, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $funcionalidadRepository->edit($funcionalidad);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_funcionalidad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/admin/funcionalidad/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_funcionalidad_modificar', ['id' => $funcionalidad], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Funcionalidad $funcionalidad
     * @param FuncionalidadRepository $funcionalidadRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_funcionalidad_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, Funcionalidad $funcionalidad, FuncionalidadRepository $funcionalidadRepository)
    {
        try {
            if ($funcionalidadRepository->find($funcionalidad) instanceof Funcionalidad) {
                $funcionalidadRepository->remove($funcionalidad, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_funcionalidad_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_funcionalidad_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_funcionalidad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param User $funcionalidad
     * @param CategoriaDocenteRepository $categoriaDocenteRepository
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_funcionalidad_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, Funcionalidad $funcionalidad)
    {
        return $this->render('modules/admin/funcionalidad/detail.html.twig', [
            'item' => $funcionalidad,
        ]);
    }
}
