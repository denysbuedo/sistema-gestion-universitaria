<?php

namespace App\Controller\Estructura;

use App\Entity\Estructura\Entidad;
use App\Entity\Estructura\Estructura;
use App\Form\Estructura\EntidadType;
use App\Repository\Estructura\EntidadRepository;
use App\Repository\Estructura\EstructuraRepository;
use App\Repository\Estructura\MunicipioRepository;
use App\Repository\Personal\PersonaRepository;
use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ENTITY')"))]
#[Route('/rrhh/entidad')]
class EntidadController extends AbstractController
{


    /**
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/', name: 'app_estructura_entidad_index', methods: ['GET'])]
    public function indexEstructurasEntidades(Request $request, EstructuraRepository $estructuraRepository, Utils $utils)
    {
        try {
            $registros = $estructuraRepository->findBy(['esEntidad' => 1], ['activo' => 'desc', 'id' => 'desc']);
            $tipo = strtoupper((string) $request->query->get('tipo', ''));
            if ($tipo !== '') {
                $registros = array_values(array_filter($registros, static function (Estructura $estructura) use ($tipo): bool {
                    return match ($tipo) {
                        'UP', 'UPTE' => $estructura->getClasificacionPresupuestaria() === $tipo,
                        'EMPRESA' => $estructura->getCategoriaEstructura()?->getId() === 7,
                        'INTERFAZ' => $estructura->getCategoriaEstructura()?->getId() === 20,
                        default => true,
                    };
                }));
            }
            return $this->render('modules/estructura/estructura/entidad.html.twig', [
                'registros' => $registros,
                'tipoSeleccionado' => $tipo,
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_estructura_entidad_index', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param EntidadRepository $entidadRepository
     * @param MunicipioRepository $municipioRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_entidad_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, PersonaRepository $personaRepository, EstructuraRepository $estructuraRepository, EntidadRepository $entidadRepository, MunicipioRepository $municipioRepository, Utils $utils)
    {
        $persona = $personaRepository->findBy(['usuario' => $this->getUser()->getId()]);
        $entidad = isset($persona[0]) ? $persona[0]->getEntidad() : null;
        $isAdmin = $this->isGranted('ROLE_ADMIN') || $utils->esAdminRRHH($this->getUser()->getId());
        $estructuraNegocio = !$isAdmin && $entidad ? [$entidad->getId()] : [];

        $form = $this->createForm(EntidadType::class, null, ['data_choices' => -1, 'estructuraNegocio' => $estructuraNegocio]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $estructura = $estructuraRepository->find($_POST['entidad']['estructura']);
            $estructura->setEsEntidad(true);
            $estructura->setClasificacionPresupuestaria($form->get('clasificacionPresupuestaria')->getData());
            $estructuraRepository->edit($estructura, true);
            $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
            return $this->redirectToRoute('app_estructura_entidad_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('modules/estructura/entidad/new.html.twig', [
            'form' => $form->createView(),
            'entidad' => $isAdmin ? null : $entidad
        ]);

    }

}
