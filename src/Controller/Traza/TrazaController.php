<?php

namespace App\Controller\Traza;


use App\Entity\Personal\Persona;
use App\Entity\Traza\Traza;
use App\Entity\Traza\ConfiguracionTraza;
use App\Repository\Traza\ConfiguracionTrazaRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\Security\UserRepository;
use App\Repository\Traza\TrazaRepository;
use App\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_HOME_TRAZAS')"))]
#[Route('/traza/traza')]
class TrazaController extends AbstractController
{

    /**
     * @param TrazaRepository $trazaRepository
     * @param PersonaRepository $personaRepository
     * @param UserRepository $userRepository
     * @param ConfiguracionTrazaRepository $configuracionTrazaRepository
     * @return Response
     */
    #[Route('/', name: 'app_traza_index', methods: ['GET'])]
    public function index(TrazaRepository $trazaRepository, PersonaRepository $personaRepository, UserRepository $userRepository, ConfiguracionTrazaRepository $configuracionTrazaRepository)
    {
        $persona = $personaRepository->findOneBy(['usuario' => $userRepository->find($this->getUser()->getId())]);
        $idPersona = -1;
        if ($persona instanceof Persona && method_exists($persona, 'getId')) {
            $idPersona = $persona->getId();
        }
        $configuracionTraza = $configuracionTrazaRepository->findOneBy(['persona' => $idPersona]);
        $configuracionTrazaActivo = false;
        if (isset($configuracionTraza) && !empty($configuracionTraza)) {
            $configuracionTrazaActivo = $configuracionTraza->getActivo();
        }
        return $this->render('modules/traza/traza/index.html.twig', [
            'registros' => $trazaRepository->findBy([], ['creado' => 'desc']), 'configuracionTraza' => $configuracionTrazaActivo
        ]);
    }

    /**
     * @param Traza $traza
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_traza_detail', methods: ['GET', 'POST'])]
    public function detail(Traza $traza, Utils $utils)
    {
       $registros = $utils->procesarTraza(json_decode($traza->getDataAnterior(), true), json_decode($traza->getData(), true));

//        pr($registros);
        return $this->render('modules/traza/traza/detail.html.twig', [
            'data' => json_decode($traza->getData(), true),
            'traza' => $traza,
            'registros' => $registros
        ]);
    }

    /**
     * @param Request $request
     * @param \App\Services\HandlerFop $handFop
     * @param TrazaRepository $trazaRepository
     * @return Response
     */
    #[Route('/exportar_pdf', name: 'app_traza_exportar_pdf', methods: ['GET', 'POST'])]
    public function exportarPdf(Request $request, \App\Services\HandlerFop $handFop, TrazaRepository $trazaRepository)
    {
        $export = $trazaRepository->getExportarListado();
        $export = \App\Services\DoctrineHelper::toArray($export);
        return $handFop->exportToPdf(new \App\Export\Traza\ExportListTrazaToPdf($export));
    }


    /**
     * Add package entity.
     *
     * @param PersonaRepository $personaRepository
     * @param UserRepository $userRepository
     * @param ConfiguracionTrazaRepository $configuracionTrazaRepository
     * @return JsonResponse
     */
    #[Route('/configurar_traza', name: 'app_traza_configuracion', methods: ['GET', 'POST'])]
    public function configurarTraza(PersonaRepository $personaRepository, UserRepository $userRepository, ConfiguracionTrazaRepository $configuracionTrazaRepository, LoggerInterface $logger)
    {
        try {
            $persona = $personaRepository->findOneBy(['usuario' => $userRepository->find($this->getUser()->getId())]);
            $configuracionTraza = $configuracionTrazaRepository->findOneBy(['persona' => $persona->getId()]);


            if (isset($configuracionTraza) && !empty($configuracionTraza)) {
                $configuracionTraza->setActivo(!$configuracionTraza->getActivo());
                $configuracionTrazaRepository->edit($configuracionTraza, true);
                $enabled = $configuracionTraza->getActivo();
            } else {
                $newCT = new ConfiguracionTraza();
                $newCT->setActivo(true);
                $newCT->setPersona($persona);
                $configuracionTrazaRepository->add($newCT, true);
                $enabled = $newCT->getActivo();
            }

            return $this->json(['enabled' => $enabled]);
        } catch (\Throwable $exception) {
            $logger->error('No se pudo actualizar la configuración de trazas.', [
                'exception_class' => $exception::class,
            ]);

            return $this->json(['error' => 'No se pudo actualizar la configuración de trazas.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
