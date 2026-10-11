<?php

namespace App\Controller\Economia;

use App\Entity\Economia\Obsequio;
use App\Entity\Security\User;
use App\Form\Economia\ObsequioType;
use App\Repository\Economia\ObsequioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_OBSEQUIOS')"))]
#[Route('/economia/obsequio')]
class ObsequioController extends AbstractController
{

    /**
     * @param obsequioRepository $obsequioRepository
     * @return Response
     */
    #[Route('/', name: 'app_obsequio_index', methods: ['GET'])]
    public function index(ObsequioRepository $obsequioRepository)
    {
        return $this->render('modules/economia/obsequio/index.html.twig', [
            'registros' => $obsequioRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param obsequioRepository $obsequioRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_obsequio_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, ObsequioRepository $obsequioRepository)
    {
        try {
            $entidad = new obsequio();
            $form = $this->createForm(ObsequioType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $obsequioRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_obsequio_index', [], Response::HTTP_SEE_OTHER);
            }
            return $this->render('modules/economia/obsequio/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_obsequio_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $obsequio
     * @param obsequioRepository $obsequioRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_obsequio_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Obsequio $obsequio, ObsequioRepository $obsequioRepository)
    {
        try {
            $form = $this->createForm(ObsequioType::class, $obsequio, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $obsequioRepository->edit($obsequio);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_obsequio_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/economia/obsequio/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_obsequio_modificar', ['id' => $obsequio], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param obsequio $obsequio
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_obsequio_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, Obsequio $obsequio)
    {
        return $this->render('modules/economia/obsequio/detail.html.twig', [
            'item' => $obsequio,
        ]);
    }

    /**
     * @param Request $request
     * @param obsequio $obsequio
     * @param obsequioRepository $obsequioRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_obsequio_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, Obsequio $obsequio, ObsequioRepository $obsequioRepository)
    {
        try {
            if ($obsequioRepository->find($obsequio) instanceof Obsequio) {
                $obsequioRepository->remove($obsequio, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_obsequio_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_obsequio_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_obsequio_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
