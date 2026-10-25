<?php

namespace App\Controller\RRHH;

use App\Entity\RRHH\AE3;
use App\Form\RRHH\AE3Type;
use App\Repository\Personal\PersonaRepository;
use App\Repository\RRHH\AE3Repository;
use App\Services\UploadedDocumentStorage;
use App\Services\Utils;
use App\Services\RRHH\PartesEstadisticosValidator;
use App\Repository\RRHH\AE2Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_RRHH_REPORTE_AE3')"))]
#[Route('/rrhh/ae3')]
class AE3Controller extends AbstractController
{

    private NormalizerInterface $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param AE3Repository $AE3Repository
     * @return Response
     */
    #[Route('/', name: 'app_rrhh_reporte_ae3_index', methods: ['GET'])]
    public function index(AE3Repository $AE3Repository, PersonaRepository $personaRepository, Utils $utils)
    {
        $persona = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()]);
        $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
        return $this->render('modules/rrhh/reporte/ae3/index.html.twig', [
            'registros' => $AE3Repository->findBy($esAdminRRHH ? [] : ['entidad' => $persona?->getEntidad()], ['anno' => 'DESC', 'mes' => 'ASC']),
        ]);
    }

    /**
     * @param Request $request
     * @param AE3Repository $aE3Repository
     * @return Response
     */
    #[Route('/registrar', name: 'app_rrhh_reporte_ae3_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, AE3Repository $aE3Repository, AE2Repository $aE2Repository, PersonaRepository $personaRepository, UploadedDocumentStorage $storage, Utils $utils, PartesEstadisticosValidator $validator)
    {
        try {
            $ae3 = new AE3();
            if (!$ae3->getMes()) {
                $ae3->setMes((int)date('n'));
            }
            if (!$ae3->getAnno()) {
                $ae3->setAnno((int)date('Y'));
            }
            $persona = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()]);
            $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
            if (!$esAdminRRHH && $persona?->getEntidad()) {
                $ae3->setEntidad($persona->getEntidad());
            }

            $form = $this->createForm(AE3Type::class, $ae3, ['action' => 'registrar', 'administrador_rrhh' => $esAdminRRHH]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $ae2 = $ae3->getEntidad() ? $aE2Repository->findOneBy([
                    'entidad' => $ae3->getEntidad(),
                    'mes' => $ae3->getMes(),
                    'anno' => $ae3->getAnno(),
                ]) : null;
                $erroresValidacion = $validator->validarAE3($ae3, $ae2);
                if ($erroresValidacion !== []) {
                    foreach ($erroresValidacion as $error) {
                        $form->addError(new \Symfony\Component\Form\FormError($error));
                    }
                } else {
                if (($file = $form['documento']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $ae3->setDocumento($storage->storeName($file, 'uploads/rrhh/ae3/documento'));
                }

                if ($ae3->getEntidad()) {
                    $aE3Repository->add($ae3, true);
                    $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                    return $this->redirectToRoute('app_rrhh_reporte_ae3_index', [], Response::HTTP_SEE_OTHER);
                }
                $this->addFlash('error', 'Debe seleccionar una universidad válida.');
                }
            }


            return $this->render('modules/rrhh/reporte/ae3/new.html.twig', [
                'form' => $form->createView(),
                'entidad' => $ae3->getEntidad(),
                'administrador_rrhh' => $esAdminRRHH,
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_reporte_ae3_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param AE3 $ae3
     * @param AE3Repository $ae3Repository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_rrhh_reporte_ae3_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, AE3 $ae3, AE3Repository $ae3Repository, PersonaRepository $personaRepository, UploadedDocumentStorage $storage, Utils $utils)
    {
        try {
            $esAdminRRHH = $utils->esAdminRRHH($this->getUser()->getId());
            $form = $this->createForm(AE3Type::class, $ae3, ['action' => 'modificar', 'administrador_rrhh' => $esAdminRRHH]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if (($file = $form['documento']->getData()) instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                    $ae3->setDocumento($storage->replace($file, 'uploads/rrhh/ae3/documento', $ae3->getDocumento()));
                }
                if (empty($ae3->getEntidad())) {
                    $this->addFlash('error', 'La institucion no es correcta.');
                }
                $ae3Repository->edit($ae3);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_rrhh_reporte_ae3_index', [], Response::HTTP_SEE_OTHER);
            }
            $persona = $personaRepository->findBy(['usuario' => $this->getUser()->getId()]);
            $entidad = isset($persona[0]) ? $persona[0]->getEntidad() : null;
            return $this->render('modules/rrhh/reporte/ae3/edit.html.twig', [
                'form' => $form->createView(),
                'entidad' => $entidad,
                'administrador_rrhh' => $esAdminRRHH,
                'ae3' => $ae3
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_reporte_ae3_modificar', ['id' => $ae3->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param AE3 $ae3
     * @return Response
     * @throws ExceptionInterface
     */
    #[Route('/{id}/detail', name: 'app_rrhh_reporte_ae3_detail', methods: ['GET', 'POST'])]
    public function detail(AE3 $ae3)
    {
        $ae3Array = $this->normalizer->normalize($ae3, null, [
            'circular_reference_handler' => function ($object) {
                return method_exists($object, 'getId') ? $object->getId() : null;
            },
        ]);

        return $this->render('modules/rrhh/reporte/ae3/detail.html.twig', [
            'ae3' => $ae3Array,
        ]);
    }

    /**
     * @param Request $request
     * @param AE3 $ae3
     * @param AE3Repository $ae3Repository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_rrhh_reporte_ae3_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, AE3 $ae3, AE3Repository $ae3Repository)
    {
        try {
            if ($ae3Repository->find($ae3) instanceof AE3) {
                $ae3Repository->remove($ae3, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_rrhh_reporte_ae3_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_rrhh_reporte_ae3_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_rrhh_reporte_ae3_index', [], Response::HTTP_SEE_OTHER);
        }
    }
}
