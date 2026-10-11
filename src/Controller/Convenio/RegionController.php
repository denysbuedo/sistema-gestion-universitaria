<?php

namespace App\Controller\Convenio;

use App\Entity\Convenio\Convenio;
use App\Entity\Convenio\Region;
use App\Form\Convenio\RegionType;
use App\Repository\Convenio\RegionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_REGION')"))]
#[Route('/convenio/region')]
class RegionController extends AbstractController
{

    /**
     * @param RegionRepository $regionRepository
     * @return Response
     */
    #[Route('/', name: 'app_region_index', methods: ['GET'])]
    public function index(RegionRepository $regionRepository)
    {
        return $this->render('modules/convenio/region/index.html.twig', [
            'registros' => $regionRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param RegionRepository $regionRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_region_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, RegionRepository $regionRepository)
    {
        try {
            $entidad = new Region();
            $form = $this->createForm(RegionType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $regionRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_region_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/convenio/region/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_region_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Region $region
     * @param RegionRepository $regionRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_region_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Region $region, RegionRepository $regionRepository)
    {
        try {
            $form = $this->createForm(RegionType::class, $region, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $regionRepository->edit($region);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_region_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/convenio/region/edit.html.twig', [
                'form' => $form->createView(),
                'convenio' => $region
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_region_modificar', ['id' => $region->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Region $region
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_region_detail', methods: ['GET', 'POST'])]
    public function detail(Region $region)
    {
        return $this->render('modules/convenio/region/detail.html.twig', [
            'item' => $region,
        ]);
    }

    /**
     * @param Region $region
     * @param RegionRepository $regionRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_region_eliminar', methods: ['GET'])]
    public function eliminar(Region $region, RegionRepository $regionRepository)
    {
        try {
            if ($regionRepository->find($region) instanceof Region) {
                $regionRepository->remove($region, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_region_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_region_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_region_index', [], Response::HTTP_SEE_OTHER);
        }
    }

}
