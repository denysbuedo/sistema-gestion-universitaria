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

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_ESTRUCTURA_UNIDAD_PRESUPUESTADA_TRATAMIENTO_ESPECIAL')"))]
#[Route('/rrhh/unidad_presupuestada/tratamiento_especial')]
class UnidadPresupuestadaTratamientoEspecialController extends AbstractController
{


    /**
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/', name: 'app_estructura_unidad_presupuestada_tratamiento_especial_index', methods: ['GET'])]
    public function index(EstructuraRepository $estructuraRepository, Utils $utils)
    {
        return $this->redirectToRoute('app_estructura_entidad_index', ['tipo' => 'UPTE']);
    }


}
