<?php

namespace App\Controller\Informatizacion;

use App\Entity\Informatizacion\CentroDatoVirtual;
use App\Form\Informatizacion\CentroDatoVirtualType;
use App\Repository\Informatizacion\CentroDatoVirtualRepository;
use App\Repository\Personal\PersonaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_CENTRO_DATO_VIRTUAL')"))]
#[Route('/informatizacion/centro_dato_virtual')]
class CentroDatoVirtualController extends AbstractController
{

    /**
     * @param CentroDatoVirtualRepository $centroDatoVirtualRepository
     * @return Response
     */
    #[Route('/', name: 'app_centro_dato_virtual_index', methods: ['GET'])]
    public function index(CentroDatoVirtualRepository $centroDatoVirtualRepository)
    {
        return $this->render('modules/informatizacion/centroDatoVirtual/index.html.twig', [
            'registros' => $centroDatoVirtualRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param CentroDatoVirtualRepository $centroDatoVirtualRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_centro_dato_virtual_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, PersonaRepository $personaRepository, CentroDatoVirtualRepository $centroDatoVirtualRepository)
    {
        try {
            $entidad = new CentroDatoVirtual();
            $form = $this->createForm(CentroDatoVirtualType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $centroDatoVirtualRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_centro_dato_virtual_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/centroDatoVirtual/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_centro_dato_virtual_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param CentroDatoVirtual $centroDatoVirtual
     * @param CentroDatoVirtualRepository $centroDatoVirtualRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_centro_dato_virtual_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, PersonaRepository $personaRepository, CentroDatoVirtual $centroDatoVirtual, CentroDatoVirtualRepository $centroDatoVirtualRepository)
    {
        try {
            $form = $this->createForm(CentroDatoVirtualType::class, $centroDatoVirtual, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $centroDatoVirtualRepository->edit($centroDatoVirtual);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_centro_dato_virtual_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/centroDatoVirtual/edit.html.twig', [
                'form' => $form->createView(),
                'centroDatoVirtual' => $centroDatoVirtual
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_centro_dato_virtual_modificar', ['id' => $centroDatoVirtual->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param centroDatoVirtual $centroDatoVirtual
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_centro_dato_virtual_detail', methods: ['GET', 'POST'])]
    public function detail(CentroDatoVirtual $centroDatoVirtual)
    {
        return $this->render('modules/informatizacion/centroDatoVirtual/detail.html.twig', [
            'item' => $centroDatoVirtual,
        ]);
    }

    /**
     * @param CentroDatoVirtual $centroDatoVirtual
     * @param CentroDatoVirtualRepository $centroDatoVirtualRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_centro_dato_virtual_eliminar', methods: ['GET'])]
    public function eliminar(CentroDatoVirtual $centroDatoVirtual, CentroDatoVirtualRepository $centroDatoVirtualRepository)
    {
        try {
            if ($centroDatoVirtualRepository->find($centroDatoVirtual) instanceof CentroDatoVirtual) {
                $centroDatoVirtualRepository->remove($centroDatoVirtual, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_centro_dato_virtual_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_centro_dato_virtual_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_centro_dato_virtual_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
