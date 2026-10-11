<?php

namespace App\Controller\Informatizacion;

use App\Entity\Informatizacion\EnlaceConectividad;
use App\Form\Informatizacion\EnlaceConectividadType;
use App\Repository\Informatizacion\EnlaceConectividadRepository;
use App\Repository\Personal\PersonaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ENLACE_CONECTIVIDAD')"))]
#[Route('/informatizacion/enlace_conectividad')]
class EnlaceConectividadController extends AbstractController
{

    /**
     * @param EnlaceConectividadRepository $EnlaceConectividadRepository
     * @return Response
     */
    #[Route('/', name: 'app_enlace_conectividad_index', methods: ['GET'])]
    public function index(EnlaceConectividadRepository $EnlaceConectividadRepository)
    {
        return $this->render('modules/informatizacion/enlaceConectividad/index.html.twig', [
            'registros' => $EnlaceConectividadRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param EnlaceConectividadRepository $enlaceConectividadRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_enlace_conectividad_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, PersonaRepository $personaRepository, EnlaceConectividadRepository $enlaceConectividadRepository)
    {
        try {
            $entidad = new EnlaceConectividad();
            $form = $this->createForm(EnlaceConectividadType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entidad->setNombre($entidad->getEd());
                $enlaceConectividadRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_enlace_conectividad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/enlaceConectividad/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_enlace_conectividad_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param EnlaceConectividad $enlaceConectividad
     * @param EnlaceConectividadRepository $enlaceConectividadRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_enlace_conectividad_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, PersonaRepository $personaRepository, EnlaceConectividad $enlaceConectividad, EnlaceConectividadRepository $enlaceConectividadRepository)
    {
        try {
            $form = $this->createForm(EnlaceConectividadType::class, $enlaceConectividad, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $enlaceConectividadRepository->edit($enlaceConectividad);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_enlace_conectividad_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/enlaceConectividad/edit.html.twig', [
                'form' => $form->createView(),
                'enlaceConectividad' => $enlaceConectividad
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_enlace_conectividad_modificar', ['id' => $enlaceConectividad->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param enlaceConectividad $enlaceConectividad
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_enlace_conectividad_detail', methods: ['GET', 'POST'])]
    public function detail(EnlaceConectividad $enlaceConectividad)
    {
        return $this->render('modules/informatizacion/enlaceConectividad/detail.html.twig', [
            'item' => $enlaceConectividad,
        ]);
    }

    /**
     * @param EnlaceConectividad $enlaceConectividad
     * @param EnlaceConectividadRepository $enlaceConectividadRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_enlace_conectividad_eliminar', methods: ['GET'])]
    public function eliminar(EnlaceConectividad $enlaceConectividad, EnlaceConectividadRepository $enlaceConectividadRepository)
    {
        try {
            if ($enlaceConectividadRepository->find($enlaceConectividad) instanceof EnlaceConectividad) {
                $enlaceConectividadRepository->remove($enlaceConectividad, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_enlace_conectividad_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_enlace_conectividad_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_enlace_conectividad_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
