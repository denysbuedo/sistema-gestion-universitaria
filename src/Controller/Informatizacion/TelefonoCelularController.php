<?php

namespace App\Controller\Informatizacion;

use App\Entity\Informatizacion\TelefonoCelular;
use App\Entity\Informatizacion\TelefonoCelularResponsable;
use App\Entity\Personal\Persona;
use App\Entity\Pregrado\ComisionNacional;
use App\Entity\Pregrado\MiembrosComisionNacional;
use App\Form\Informatizacion\TelefonoCelularType;
use App\Form\Pregrado\ComisionNacionalType;
use App\Repository\Informatizacion\TelefonoCelularRepository;
use App\Repository\Informatizacion\TelefonoCelularResponsableRepository;
use App\Repository\Personal\PersonaRepository;
use App\Repository\Postgrado\MiembrosComisionRepository;
use App\Repository\Postgrado\RolComisionRepository;
use App\Repository\Pregrado\ComisionNacionalRepository;
use App\Repository\Pregrado\MiembrosComisionNacionalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN", "ROLE_GEST_TELEFONO_CELULAR")
 */
#[Route('/informatizacion/telefono_celular')]
class TelefonoCelularController extends AbstractController
{

    /**
     * @param TelefonoCelularRepository $telefonoCelularRepository
     * @return Response
     */
    #[Route('/', name: 'app_telefono_celular_index', methods: ['GET'])]
    public function index(TelefonoCelularRepository $telefonoCelularRepository, TelefonoCelularResponsableRepository $telefonoCelularResponsableRepository)
    {
        $registros = $telefonoCelularRepository->findBy([], ['id' => 'desc']);
        $response = [];
        if (is_array($registros)) {
            foreach ($registros as $value) {
                $responsables = $telefonoCelularResponsableRepository->findBy(['telefonoCelular' => $value->getId()], ['id' => 'desc']);
                $res = null;
                if (isset($responsables[0])) {
                    $temp = $responsables[0]->getResponsable();
                    $res = $temp->getPrimerNombre() . ' ' . $temp->getSegundoNombre() . ' ' . $temp->getPrimerApellido() . ' ' . $temp->getSegundoApellido();
                }
                $value->responsable = $res;
                $response[] = $value;
            }
        }

        return $this->render('modules/informatizacion/telefonoCelular/index.html.twig', [
            'registros' => $response,
        ]);
    }

    /**
     * @param Request $request
     * @param TelefonoCelularRepository $telefonoCelularRepository
     * @return Response
     */
    #[Route('/registrar', name: 'app_telefono_celular_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, PersonaRepository $personaRepository, TelefonoCelularRepository $telefonoCelularRepository)
    {
//        try {
            $entidad = new TelefonoCelular();
            $form = $this->createForm(TelefonoCelularType::class, $entidad, ['action' => 'registrar']);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
//                $personaAutenticada = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()]);
//                $entidad->setEstructura($personaAutenticada->getEstructura());
                $telefonoCelularRepository->add($entidad, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_telefono_celular_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/telefonoCelular/new.html.twig', [
                'form' => $form->createView(),
            ]);
//        } catch (\Exception $exception) {
//            $this->addFlash('error', $exception->getMessage());
//            return $this->redirectToRoute('app_telefono_celular_registrar', [], Response::HTTP_SEE_OTHER);
//        }
    }


    /**
     * @param Request $request
     * @param TelefonoCelular $telefonoCelular
     * @param TelefonoCelularRepository $telefonoCelularRepository
     * @return Response
     */
    #[Route('/{id}/modificar', name: 'app_telefono_celular_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, PersonaRepository $personaRepository, TelefonoCelular $telefonoCelular, TelefonoCelularRepository $telefonoCelularRepository)
    {
        try {
            $form = $this->createForm(TelefonoCelularType::class, $telefonoCelular, ['action' => 'modificar']);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
//                if (!method_exists($telefonoCelular->getEstructura(), 'getId')) {
                $personaAutenticada = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()]);
                $telefonoCelular->setEstructura($personaAutenticada->getEstructura());
//                }
                $telefonoCelularRepository->edit($telefonoCelular);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_telefono_celular_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/informatizacion/telefonoCelular/edit.html.twig', [
                'form' => $form->createView(),
                'telefonoCelular' => $telefonoCelular
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_telefono_celular_modificar', ['id' => $telefonoCelular->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param telefonoCelular $telefonoCelular
     * @return Response
     */
    #[Route('/{id}/detail', name: 'app_telefono_celular_detail', methods: ['GET', 'POST'])]
    public function detail(TelefonoCelular $telefonoCelular, TelefonoCelularResponsableRepository $telefonoCelularResponsableRepository)
    {
        $asignadas = [];
        $temp = $telefonoCelularResponsableRepository->findBy(['telefonoCelular' => $telefonoCelular->getId()], ['id' => 'desc']);
        foreach ($temp as $value) {
            $asignadas[] = $value->getResponsable();
        }

        return $this->render('modules/informatizacion/telefonoCelular/detail.html.twig', [
            'item' => $telefonoCelular,
            'asignadas' => $asignadas,
        ]);
    }

    /**
     * @param TelefonoCelular $telefonoCelular
     * @param TelefonoCelularRepository $telefonoCelularRepository
     * @return Response
     */
    #[Route('/{id}/eliminar', name: 'app_telefono_celular_eliminar', methods: ['GET'])]
    public function eliminar(TelefonoCelular $telefonoCelular, TelefonoCelularRepository $telefonoCelularRepository)
    {
        try {
            if ($telefonoCelularRepository->find($telefonoCelular) instanceof TelefonoCelular) {
                $telefonoCelularRepository->remove($telefonoCelular, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_telefono_celular_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_telefono_celular_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_telefono_celular_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param TelefonoCelular $telefonoCelular
     * @return Response
     */
    #[Route('/{id}/responsable', name: 'app_telefono_celular_responsable', methods: ['GET', 'POST'])]
    public function responsable(Request $request, TelefonoCelular $telefonoCelular, TelefonoCelularResponsableRepository $telefonoCelularResopnsableRepository, PersonaRepository $personaRepository)
    {
        try {
            $arrayIdAsignados = [];
            $asignadas = [];
            $temp = $telefonoCelularResopnsableRepository->findBy(['telefonoCelular' => $telefonoCelular->getId()], ['id' => 'desc']);
            foreach ($temp as $value) {
                $arrayIdAsignados[] = $value->getResponsable()->getId();
                $asignadas[] = $value->getResponsable();
            }
            $request->getSession()->set('idTelefonoCelular', $telefonoCelular->getId());
            $personas = $personaRepository->getPersonasNoAsignadasDadoArrayIdPersonas($arrayIdAsignados);
            return $this->render('modules/informatizacion/telefonoCelular/responsable.html.twig', [
                'telefonoCelular' => $telefonoCelular,
                'personas' => $personas,
                'asignadas' => $asignadas,
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_telefono_celular_index', ['id' => $telefonoCelular->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param PersonaRepository $personaRepository
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/asociar_responsable', name: 'app_telefono_celular_asociar_responsable', methods: ['GET', 'POST'])]
    public function asociarResponsable(Request $request, PersonaRepository $personaRepository, TelefonoCelularRepository $telefonoCelularRepository, EntityManagerInterface $em)
    {
        try {
            $arrayIds = $request->request->get('arrayId');
            if (is_array($arrayIds)) {
                foreach ($arrayIds as $value) {
                    $responsable = new TelefonoCelularResponsable();
                    $responsable->setResponsable($personaRepository->find($value['id_persona']));
                    $responsable->setTelefonoCelular($telefonoCelularRepository->find($request->getSession()->get('idTelefonoCelular')));
                    $em->persist($responsable);
                }
                $em->flush();
            }
            return $this->json('OK');
        } catch (\Exception $exception) {
            return $this->json(true);
        }
    }

    /**
     * @param Request $request
     * @param Persona $persona
     * @param PersonaRepository $personaRepository
     * @param TelefonoCelularResponsableRepository $telefonoCelularResponsableRepository
     * @return Response
     */
    #[Route('/{id}/eliminar_responsable', name: 'app_telefono_celular_eliminar_responsable', methods: ['GET'])]
    public function eliminarResponsable(Request $request, Persona $persona, PersonaRepository $personaRepository, TelefonoCelularResponsableRepository $telefonoCelularResponsableRepository)
    {
        try {
            $telefonoCelular = $request->getSession()->get('idTelefonoCelular');
            if ($personaRepository->find($persona->getId()) instanceof Persona) {
                $miembro = $telefonoCelularResponsableRepository->findOneBy(['responsable' => $persona->getId(), 'telefonoCelular' => $telefonoCelular]);
                $telefonoCelularResponsableRepository->remove($miembro, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_telefono_celular_responsable', ['id' => $telefonoCelular], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_telefono_celular_responsable', ['id' => $telefonoCelular], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_telefono_celular_responsable', ['id' => $telefonoCelular], Response::HTTP_SEE_OTHER);
        }
    }
}
