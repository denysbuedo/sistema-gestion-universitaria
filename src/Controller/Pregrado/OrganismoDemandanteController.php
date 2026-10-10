<?php

namespace App\Controller\Pregrado;

use App\Entity\Pregrado\OrganismoDemandante;
use App\Entity\Security\User;
use App\Form\Pregrado\OrganismoDemandanteType;
use App\Repository\Pregrado\OrganismoDemandanteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_ORGDEMAND")
 */
#[Route('/pregrado/organismo_demandante')]
class OrganismoDemandanteController extends AbstractController
{

    /**
     * @param OrganismoDemandanteRepository $tipoProgramaRepository
     * @return Response
     */
    #[Route('/', name: 'app_organismo_demandante_index', methods: ['GET'])]
    public function index(OrganismoDemandanteRepository $tipoProgramaRepository)
    {
        return $this->render('modules/pregrado/organismo_demandante/index.html.twig', [
            'registros' => $tipoProgramaRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param OrganismoDemandanteRepository $tipoProgramaRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_organismo_demandante_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, OrganismoDemandanteRepository $tipoProgramaRepository)
    {
        try {
            $organismoDemandante = new OrganismoDemandante();
            $form = $this->createForm(OrganismoDemandanteType::class, $organismoDemandante, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $tipoProgramaRepository->add($organismoDemandante, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_organismo_demandante_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/organismo_demandante/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_organismo_demandante_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $tipoPrograma
     * @param OrganismoDemandanteRepository $tipoProgramaRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_organismo_demandante_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, OrganismoDemandante $tipoPrograma, OrganismoDemandanteRepository $tipoProgramaRepository)
    {
        try {
            $form = $this->createForm(OrganismoDemandanteType::class, $tipoPrograma, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $tipoProgramaRepository->edit($tipoPrograma);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_organismo_demandante_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/pregrado/organismo_demandante/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_organismo_demandante_modificar', ['id' => $tipoPrograma], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param OrganismoDemandante $tipoPrograma
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_organismo_demandante_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, OrganismoDemandante $tipoProgramaAcademico)
    {
        return $this->render('modules/pregrado/organismo_demandante/detail.html.twig', [
            'item' => $tipoProgramaAcademico,
        ]);
    }

    /**
     * @param Request $request
     * @param OrganismoDemandante $tipoPrograma
     * @param OrganismoDemandanteRepository $tipoProgramaRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_organismo_demandante_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, OrganismoDemandante $tipoPrograma, OrganismoDemandanteRepository $tipoProgramaRepository)
    {
        try {
            if ($tipoProgramaRepository->find($tipoPrograma) instanceof OrganismoDemandante) {
                $tipoProgramaRepository->remove($tipoPrograma, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_organismo_demandante_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_organismo_demandante_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_organismo_demandante_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
