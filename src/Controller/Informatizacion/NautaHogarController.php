<?php

namespace App\Controller\Informatizacion;


use App\Entity\Informatizacion\NautaHogar;
use App\Entity\Personal\Persona;
use App\Form\Informatizacion\NautaHogarType;
use App\Repository\Informatizacion\NautaHogarRepository;
use App\Repository\Personal\PersonaRepository;
use App\Services\Informatizacion\InformatizacionScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_NAUTA_HOGAR')"))]
#[Route('/informatizacion/nauta_hogar')]
class NautaHogarController extends AbstractController
{
    /**
     * @param nautaHogarRepository $nautaHogarRepository
     * @return Response
     */
    #[Route('/', name: 'app_nauta_hogar_index', methods: ['GET', 'POST'])]
    public function index(Request $request, NautaHogarRepository $nautaHogarRepository, InformatizacionScope $scope)
    {
        $institution = $scope->institution($this->getUser());
        return $this->render('modules/informatizacion/nautaHogar/index.html.twig', [
            'registros' => $nautaHogarRepository->findBy($scope->criteria($this->getUser()), ['id' => 'desc']),
            'alcance' => $institution?->getNombre() ?? 'Todas las universidades',
            'es_admin_informatizacion' => $institution === null,
        ]);
    }

    /**
     * @param Request $request
     * @param PersonaRepository $personaRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_nauta_hogar_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, PersonaRepository $personaRepository, InformatizacionScope $scope)
    {
        try {
            $allPost = $request->request->all();
            $registros = [];

            if (isset($allPost['busqueda']) && !empty($allPost['busqueda'])) {
                $criteria = ['carnetIdentidad' => trim($allPost['busqueda'])];
                $institution = $scope->institution($this->getUser());
                if ($institution !== null) {
                    $criteria['entidad'] = $institution;
                }
                $registros = $personaRepository->findBy($criteria);
            }
            return $this->render('modules/informatizacion/nautaHogar/new.html.twig', [
                'registros' => $registros,
                'busqueda' => $allPost['busqueda'] ?? null
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_nauta_hogar_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Persona $persona
     * @return Response
     */
    #[Route('/{id}/registrar-v2', name: 'app_nauta_hogar_registrar_v2', methods: ['GET', 'POST'])]
    public function registrarV2(Request $request, PersonaRepository $personaRepository, Persona $persona, NautaHogarRepository $nautaHogarRepository, InformatizacionScope $scope)
    {
        $scope->assertCanAssignPerson($this->getUser(), $persona);
        try {
            $entidad = new NautaHogar();
            $institution = $persona->getEntidad();
            if (!$institution) {
                throw $this->createAccessDeniedException('La persona seleccionada no está asociada a una universidad.');
            }
            $entidad->setEstructura($institution);
            $form = $this->createForm(NautaHogarType::class, $entidad, ['action' => 'registrar', 'institution_id' => $institution->getId()]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $entidad->setResponsable($persona);
                $nautaHogarRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_nauta_hogar_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/nautaHogar/newV2.html.twig', [
                'form' => $form->createView(),
                'persona' => $persona
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_nauta_hogar_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param NautaHogar $nautaHogar
     * @param nautaHogarRepository $nautaHogarRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_nauta_hogar_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, PersonaRepository $personaRepository, NautaHogar $nautaHogar, NautaHogarRepository $nautaHogarRepository, InformatizacionScope $scope)
    {
        $scope->assertCanManage($this->getUser(), $nautaHogar);
        try {
            $form = $this->createForm(NautaHogarType::class, $nautaHogar, ['action' => 'modificar', 'institution_id' => $nautaHogar->getEstructura()?->getId()]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $nautaHogarRepository->edit($nautaHogar);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_nauta_hogar_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/nautaHogar/edit.html.twig', [
                'form' => $form->createView(),
                'nautaHogar' => $nautaHogar,
                'persona' => $nautaHogar->getResponsable()
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_nauta_hogar_modificar', ['id' => $nautaHogar->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param nautaHogar $nautaHogar
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_nauta_hogar_detail', methods: ['GET', 'POST'])]
    public function detail(NautaHogar $nautaHogar, InformatizacionScope $scope)
    {
        $scope->assertCanManage($this->getUser(), $nautaHogar);
        return $this->render('modules/informatizacion/nautaHogar/detail.html.twig', [
            'item' => $nautaHogar,
        ]);
    }

    /**
     * @param nautaHogar $nautaHogar
     * @param nautaHogarRepository $nautaHogarRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_nauta_hogar_eliminar', methods: ['GET'])]
    public function eliminar(NautaHogar $nautaHogar, NautaHogarRepository $nautaHogarRepository, InformatizacionScope $scope)
    {
        $scope->assertCanManage($this->getUser(), $nautaHogar);
        try {
            if ($nautaHogarRepository->find($nautaHogar) instanceof NautaHogar) {
                $nautaHogarRepository->remove($nautaHogar, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_nauta_hogar_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_nauta_hogar_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_nauta_hogar_index', [], Response::HTTP_SEE_OTHER);
        }
    }
}
