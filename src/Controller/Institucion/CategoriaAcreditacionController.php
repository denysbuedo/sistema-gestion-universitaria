<?php

namespace App\Controller\Institucion;

use App\Entity\Institucion\CategoriaAcreditacion;
use App\Entity\Security\User;
use App\Form\Institucion\CategoriaAcreditacionType;
use App\Repository\Institucion\CategoriaAcreditacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_CATACRED')"))]
#[Route('/institucion/categoria_acreditacion')]
class CategoriaAcreditacionController extends AbstractController
{

    /**
     * @param CategoriaAcreditacionRepository $categoriaAcreditacionRepository
     * @return Response
     */
    #[Route('/', name: 'app_categoria_acreditacion_index', methods: ['GET'])]
    public function index(CategoriaAcreditacionRepository $categoriaAcreditacionRepository)
    {
        return $this->render('modules/institucion/categoria_acreditacion/index.html.twig', [
            'registros' => $categoriaAcreditacionRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param CategoriaAcreditacionRepository $categoriaAcreditacionRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_categoria_acreditacion_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, CategoriaAcreditacionRepository $categoriaAcreditacionRepository)
    {
        try {
            $catDocenteEntity = new CategoriaAcreditacion();
            $form = $this->createForm(CategoriaAcreditacionType::class, $catDocenteEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $categoriaAcreditacionRepository->add($catDocenteEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_categoria_acreditacion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/institucion/categoria_acreditacion/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_categoria_acreditacion_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $categoriaAcreditacion
     * @param CategoriaAcreditacionRepository $categoriaAcreditacionRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_categoria_acreditacion_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, CategoriaAcreditacion $categoriaAcreditacion, CategoriaAcreditacionRepository $categoriaAcreditacionRepository)
    {
        try {
            $form = $this->createForm(CategoriaAcreditacionType::class, $categoriaAcreditacion, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $categoriaAcreditacionRepository->edit($categoriaAcreditacion);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_categoria_acreditacion_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/institucion/categoria_acreditacion/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_categoria_acreditacion_modificar', ['id' => $categoriaAcreditacion], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $categoriaAcreditacion
     * @param CategoriaAcreditacionRepository $categoriaAcreditacionRepository
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_categoria_acreditacion_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, CategoriaAcreditacion $categoriaAcreditacion)
    {
        return $this->render('modules/institucion/categoria_acreditacion/detail.html.twig', [
            'item' => $categoriaAcreditacion,
        ]);
    }

    /**
     * @param Request $request
     * @param CategoriaAcreditacion $categoriaAcreditacion
     * @param CategoriaAcreditacionRepository $categoriaAcreditacionRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_categoria_acreditacion_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, CategoriaAcreditacion $categoriaAcreditacion, CategoriaAcreditacionRepository $categoriaAcreditacionRepository)
    {
        try {
            if ($categoriaAcreditacionRepository->find($categoriaAcreditacion) instanceof CategoriaAcreditacion) {
                $categoriaAcreditacionRepository->remove($categoriaAcreditacion, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_categoria_acreditacion_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_categoria_acreditacion_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_categoria_acreditacion_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
