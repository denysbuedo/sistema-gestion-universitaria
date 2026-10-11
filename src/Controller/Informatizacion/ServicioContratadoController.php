<?php

namespace App\Controller\Informatizacion;

use App\Entity\Informatizacion\ServicioContratado;
use App\Form\Informatizacion\ServicioContratadoType;
use App\Repository\Informatizacion\ServicioContratadoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_SISTEMA_OPERATIVO')"))]
#[Route('/informatizacion/servicio_contratado')]
class ServicioContratadoController extends AbstractController
{

    /**
     * @param ServicioContratadoRepository $servicioContratadoRepository
     * @return Response
     */
    #[Route('/', name: 'app_servicio_contratado_index', methods: ['GET'])]
    public function index(ServicioContratadoRepository $servicioContratadoRepository)
    {
        return $this->render('modules/informatizacion/servicioContratado/index.html.twig', [
            'registros' => $servicioContratadoRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param ServicioContratadoRepository $servicioContratadoRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_servicio_contratado_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, ServicioContratadoRepository $servicioContratadoRepository)
    {
        try {
            $entidad = new ServicioContratado();
            $form = $this->createForm(ServicioContratadoType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $servicioContratadoRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_servicio_contratado_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/servicioContratado/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_servicio_contratado_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param ServicioContratado $servicioContratado
     * @param ServicioContratadoRepository $servicioContratadoRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_servicio_contratado_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, ServicioContratado $servicioContratado, ServicioContratadoRepository $servicioContratadoRepository)
    {
        try {
            $form = $this->createForm(ServicioContratadoType::class, $servicioContratado, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $servicioContratadoRepository->edit($servicioContratado);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_servicio_contratado_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/servicioContratado/edit.html.twig', [
                'form' => $form->createView(),
                'servicioContratado' => $servicioContratado
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_servicio_contratado_modificar', ['id' => $servicioContratado->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param servicioContratado $servicioContratado
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_servicio_contratado_detail', methods: ['GET', 'POST'])]
    public function detail(ServicioContratado $servicioContratado)
    {
        return $this->render('modules/informatizacion/servicioContratado/detail.html.twig', [
            'item' => $servicioContratado,
        ]);
    }

    /**
     * @param ServicioContratado $servicioContratado
     * @param ServicioContratadoRepository $servicioContratadoRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_servicio_contratado_eliminar', methods: ['GET'])]
    public function eliminar(ServicioContratado $servicioContratado, ServicioContratadoRepository $servicioContratadoRepository)
    {
        try {
            if ($servicioContratadoRepository->find($servicioContratado) instanceof ServicioContratado) {
                $servicioContratadoRepository->remove($servicioContratado, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_servicio_contratado_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_servicio_contratado_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_servicio_contratado_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
