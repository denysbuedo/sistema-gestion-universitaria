<?php

namespace App\Controller\Pregrado;

use App\Entity\Pregrado\Oace;
use App\Entity\Security\User;
use App\Form\Pregrado\OaceType;
use App\Repository\Pregrado\OaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_CATDOC')"))]
#[Route('/pregrado/oace')]
class OaceController extends AbstractController
{

    /**
     * @param OaceRepository $tipoProgramaRepository
     * @return Response
     */
    #[Route('/', name: 'app_oace_index', methods: ['GET'])]
    public function index(OaceRepository $tipoProgramaRepository)
    {
        return $this->render('modules/pregrado/oace/index.html.twig', [
            'registros' => $tipoProgramaRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param OaceRepository $tipoProgramaRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_oace_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, OaceRepository $tipoProgramaRepository)
    {
        try {
            $catDocenteEntity = new Oace();
            $form = $this->createForm(OaceType::class, $catDocenteEntity, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tipoProgramaRepository->add($catDocenteEntity, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_oace_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/oace/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_oace_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Oace $oace
     * @param OaceRepository $tipoProgramaRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_oace_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Oace $oace, OaceRepository $oaceRepository)
    {
        try {
            $form = $this->createForm(OaceType::class, $oace, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $oaceRepository->edit($oace);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_oace_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/oace/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_oace_modificar', ['id' => $oace], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Oace $tipoPrograma
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_oace_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, Oace $tipoProgramaAcademico)
    {
        return $this->render('modules/pregrado/oace/detail.html.twig', [
            'item' => $tipoProgramaAcademico,
        ]);
    }

    /**
     * @param Request $request
     * @param Oace $tipoPrograma
     * @param OaceRepository $tipoProgramaRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_oace_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, Oace $tipoPrograma, OaceRepository $tipoProgramaRepository)
    {
        try {
            if ($tipoProgramaRepository->find($tipoPrograma) instanceof Oace) {
                $tipoProgramaRepository->remove($tipoPrograma, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_oace_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_oace_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_oace_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
