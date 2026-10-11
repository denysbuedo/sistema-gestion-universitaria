<?php

namespace App\Controller\Informatizacion;

use App\Entity\Informatizacion\Modelo;
use App\Form\Informatizacion\ModeloType;
use App\Repository\Informatizacion\ModeloRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_MODELO')"))]
#[Route('/informatizacion/modelo')]
class ModeloController extends AbstractController
{

    /**
     * @param ModeloRepository $ModeloRepository
     * @return Response
     */
    #[Route('/', name: 'app_modelo_index', methods: ['GET'])]
    public function index(ModeloRepository $ModeloRepository)
    {
        return $this->render('modules/informatizacion/modelo/index.html.twig', [
            'registros' => $ModeloRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param ModeloRepository $modeloRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_modelo_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, ModeloRepository $modeloRepository)
    {
        try {
            $entidad = new Modelo();
            $form = $this->createForm(ModeloType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $modeloRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_modelo_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/modelo/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_modelo_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Modelo $modelo
     * @param ModeloRepository $modeloRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_modelo_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Modelo $modelo, ModeloRepository $modeloRepository)
    {
        try {
            $form = $this->createForm(ModeloType::class, $modelo, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $modeloRepository->edit($modelo);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_modelo_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/modelo/edit.html.twig', [
                'form' => $form->createView(),
                'modelo' => $modelo
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_modelo_modificar', ['id' => $modelo->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param modelo $modelo
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_modelo_detail', methods: ['GET', 'POST'])]
    public function detail(Modelo $modelo)
    {
        return $this->render('modules/informatizacion/modelo/detail.html.twig', [
            'item' => $modelo,
        ]);
    }

    /**
     * @param Modelo $modelo
     * @param ModeloRepository $modeloRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_modelo_eliminar', methods: ['GET'])]
    public function eliminar(Modelo $modelo, ModeloRepository $modeloRepository)
    {
        try {
            if ($modeloRepository->find($modelo) instanceof Modelo) {
                $modeloRepository->remove($modelo, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_modelo_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_modelo_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_modelo_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
