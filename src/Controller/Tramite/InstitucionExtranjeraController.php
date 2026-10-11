<?php

namespace App\Controller\Tramite;

use App\Entity\Tramite\InstitucionExtranjera;
use App\Entity\Security\User;
use App\Form\Tramite\InstitucionExtranjeraType;
use App\Repository\Estructura\PlazaRepository;
use App\Repository\Tramite\InstitucionExtranjeraRepository;
use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_INST_EXTRANJERA')"))]
#[Route('/convenio/institucion_extranjera')]
class InstitucionExtranjeraController extends AbstractController
{

    /**
     * @param institucionExtranjeraRepository $institucionExtranjeraRepository
     * @return Response
     */
    #[Route('/', name: 'app_institucion_extranjera_index', methods: ['GET'])]
    public function index(InstitucionExtranjeraRepository $institucionExtranjeraRepository)
    {
        return $this->render('modules/tramite/institucion_extranjera/index.html.twig', [
            'registros' => $institucionExtranjeraRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']),
        ]);
    }

    /**
     * @param Request $request
     * @param institucionExtranjeraRepository $institucionExtranjeraRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_institucion_extranjera_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, InstitucionExtranjeraRepository $institucionExtranjeraRepository)
    {
        try {
            $entidad = new InstitucionExtranjera();
            $form = $this->createForm(InstitucionExtranjeraType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $institucionExtranjeraRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_institucion_extranjera_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/tramite/institucion_extranjera/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_institucion_extranjera_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param InstitucionExtranjera $institucionExtranjera
     * @param institucionExtranjeraRepository $institucionExtranjeraRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_institucion_extranjera_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, InstitucionExtranjera $institucionExtranjera, InstitucionExtranjeraRepository $institucionExtranjeraRepository)
    {
        try {
            $form = $this->createForm(InstitucionExtranjeraType::class, $institucionExtranjera, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $institucionExtranjeraRepository->edit($institucionExtranjera);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_institucion_extranjera_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/tramite/institucion_extranjera/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_institucion_extranjera_modificar', ['id' => $institucionExtranjera], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param institucionExtranjera $institucionExtranjera
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_institucion_extranjera_detail', methods: ['GET', 'POST'])]
    public function detail(InstitucionExtranjera $institucionExtranjera)
    {
        return $this->render('modules/tramite/institucion_extranjera/detail.html.twig', [
            'item' => $institucionExtranjera,
        ]);
    }

    /**
     * @param institucionExtranjera $institucionExtranjera
     * @param institucionExtranjeraRepository $institucionExtranjeraRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_institucion_extranjera_eliminar', methods: ['GET'])]
    public function eliminar(InstitucionExtranjera $institucionExtranjera, InstitucionExtranjeraRepository $institucionExtranjeraRepository)
    {
        try {
            if ($institucionExtranjeraRepository->find($institucionExtranjera) instanceof InstitucionExtranjera) {
                $institucionExtranjeraRepository->remove($institucionExtranjera, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_institucion_extranjera_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_institucion_extranjera_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_institucion_extranjera_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * Add package entity.
     *
     * @param Request $request
     * @param $id
     * @param InstitucionExtranjeraRepository $institucionExtranjeraRepository
     * @return JsonResponse
     */
    #[Route('/{id}/institucion_dado_pais', name: 'app_institucion_extranjera_institucion_dado_pais', methods: ['GET'])]
    public function getInstitucionDadoPais(Request $request, $id, InstitucionExtranjeraRepository $institucionExtranjeraRepository): JsonResponse
    {
        try {
            return $this->json($institucionExtranjeraRepository->findBy(['pais' => $id]));
        } catch (\Exception $exception) {
            return new JsonResponse([]);
        }
    }
}
