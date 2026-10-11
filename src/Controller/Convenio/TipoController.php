<?php

namespace App\Controller\Convenio;

use App\Entity\Convenio\Convenio;
use App\Entity\Convenio\Modalidad;
use App\Entity\Convenio\Tipo;
use App\Form\Convenio\ModalidadType;
use App\Form\Convenio\TipoType;
use App\Repository\Convenio\TipoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_TIPO')"))]
#[Route('/convenio/tipo')]
class TipoController extends AbstractController
{

    /**
     * @param TipoRepository $TipoRepository
     * @return Response
     */
    #[Route('/', name: 'app_tipo_index', methods: ['GET'])]
    public function index(TipoRepository $TipoRepository)
    {
        return $this->render('modules/convenio/tipo/index.html.twig', [
            'registros' => $TipoRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param TipoRepository $tipoRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_tipo_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, TipoRepository $tipoRepository)
    {
        try {
            $entidad = new Tipo();
            $form = $this->createForm(TipoType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tipoRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/convenio/tipo/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Tipo $tipo
     * @param TipoRepository $tipoRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_tipo_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Tipo $tipo, TipoRepository $tipoRepository)
    {
        try {
            $form = $this->createForm(TipoType::class, $tipo, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $tipoRepository->edit($tipo);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/convenio/tipo/edit.html.twig', [
                'form' => $form->createView(),
                'convenio' => $tipo
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_modificar', ['id' => $tipo->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Convenio $tipoPrograma
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_tipo_detail', methods: ['GET', 'POST'])]
    public function detail(Tipo $tipo)
    {
        return $this->render('modules/convenio/tipo/detail.html.twig', [
            'item' => $tipo,
        ]);
    }

    /**
     * @param Tipo $tipo
     * @param TipoRepository $tipoRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_tipo_eliminar', methods: ['GET'])]
    public function eliminar(Tipo $tipo, TipoRepository $tipoRepository)
    {
        try {
            if ($tipoRepository->find($tipo) instanceof Tipo) {
                $tipoRepository->remove($tipo, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_tipo_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_tipo_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_tipo_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
