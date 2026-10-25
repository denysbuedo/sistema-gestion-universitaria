<?php

namespace App\Controller\Estructura;

use App\Entity\Estructura\Estructura;
use App\Entity\Estructura\Plaza;
use App\Entity\RRHH\Grupo;
use App\Entity\Security\User;
use App\Export\Estructura\ExportListEstructuraToPdf;
use App\Form\Estructura\EstructuraType;
use App\Form\Estructura\PlazaType;
use App\Repository\Estructura\CategoriaEstructuraRepository;
use App\Repository\Estructura\EstructuraRepository;
use App\Repository\Estructura\MunicipioRepository;
use App\Repository\Estructura\PlazaRepository;
use App\Repository\Estructura\ProvinciaRepository;
use App\Repository\Estructura\TipoEstructuraRepository;
use App\Repository\Personal\PersonaRepository;
use App\Services\HandlerFop;
use App\Services\Utils;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 */
#[Route('/rrhh/estructura')]
class EstructuraController extends AbstractController
{

    private $dbname;
    private $user;
    private $password;
    private $host;
    private $driver;
    private $connection;
    private $port;

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws Exception
     */
    public function __construct(ContainerBagInterface $container, $dbname, $user, $password, $host, $driver, $port)
    {
        $this->env = $container->get('env_config');
        $this->dbname = $dbname;
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->driver = $driver;
        $this->port = $port;


        $connectionParams = array(
            'dbname' => $this->dbname,
            'user' => $this->user,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'driver' => $this->driver,
            'charset' => 'UTF8'
        );
        $this->connection = DriverManager::getConnection($connectionParams);
    }

    /**
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/', name: 'app_estructura_index', methods: ['GET'])]
    public function index(EstructuraRepository $estructuraRepository, Utils $utils, PersonaRepository $personaRepository)
    {
        try {
            $persona = $personaRepository->findBy(['usuario' => $this->getUser()->getId()]);
            $entidad = isset($persona[0]) ? $persona[0]->getEntidad() : null;
            $isAdmin = $this->isGranted('ROLE_ADMIN') || $utils->esAdminRRHH($this->getUser()->getId());
            $estructuraNegocio = $entidad ? $entidad->getId() : null;

            if (!$isAdmin) {
                $registros = $estructuraNegocio
                    ? $estructuraRepository->getEstructurasNegocios($estructuraNegocio)
                    : [];
            } else {
                $registros = $estructuraRepository->findBy([], ['activo' => 'desc', 'id' => 'desc']);
            }


            $data = [];
            $filtroSuperiores = [];
            $ancestrosPorEstructura = [];
            foreach ($registros as $value) {
                $data[$value->getId()] = $value->getId() !== $estructuraNegocio;
                $tokensAncestros = [];
                for ($superior = $value->getEstructura(); $superior !== null; $superior = $superior->getEstructura()) {
                    $tokensAncestros[] = 'estructura-' . $superior->getId();
                    $prefijo = $superior->getEstructura()?->getSiglas();
                    $filtroSuperiores[$superior->getId()] = ($prefijo ? sprintf('(%s) ', $prefijo) : '') . $superior->getNombre();
                }
                $ancestrosPorEstructura[$value->getId()] = implode(' ', $tokensAncestros);
            }
            asort($filtroSuperiores, SORT_NATURAL | SORT_FLAG_CASE);
            return $this->render('modules/estructura/estructura/index.html.twig', [
                'registros' => $registros,
                'dataShow' => $data,
                'filtroSuperiores' => $filtroSuperiores,
                'ancestrosPorEstructura' => $ancestrosPorEstructura,
                'entidad' => $entidad,
                'administrador_rrhh' => $utils->esAdminRRHH($this->getUser()->getId()),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_estructura_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/arbol', name: 'app_estructura_index_arbol', methods: ['GET'])]
    public function indexArbol(EstructuraRepository $estructuraRepository, Utils $utils, PersonaRepository $personaRepository): Response
    {
        $persona = $personaRepository->findBy(['usuario' => $this->getUser()->getId()]);
        $entidad = isset($persona[0]) ? $persona[0]->getEntidad() : null;
        $isAdmin = $this->isGranted('ROLE_ADMIN') || $utils->esAdminRRHH($this->getUser()->getId());

        $estructuraNegocio = $entidad ? $entidad->getId() : -1;

//        $isAdmin = false;
        $elementos = !$isAdmin
            ? $estructuraRepository->getEstructurasNegocios($estructuraNegocio)
            : $estructuraRepository->findBy(['activo' => true], ['orden' => 'ASC', 'nombre' => 'ASC']);

        $registros = array_map(function (Estructura $el) use ($estructuraNegocio, $isAdmin) {
            $color = $el->getCategoriaEstructura() ? $el->getCategoriaEstructura()->getColor() : '#333';
            $parent = $el->getEstructura() ? $el->getEstructura()->getId() : '#';
            if ($el->getId() == $estructuraNegocio && !$isAdmin) {
                $parent = '#';
            }
            return [
                'id' => $el->getId(),
                'parent' => $parent,
                'text' => $el->getNombre(),
                'color' => $color,
                'movable' => $isAdmin || $el->getId() !== $estructuraNegocio,
                'a_attr' => [
                    'style' => "color: {$color};",
                    'data-id' => $el->getId()
                ]
            ];
        }, $elementos);

        return $this->render('modules/estructura/estructura/arbol.html.twig', [
            'registros' => $registros,
            'entidad' => $entidad,
            'administrador_rrhh' => $utils->esAdminRRHH($this->getUser()->getId()),
        ]);
    }

    #[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/mover', name: 'app_estructura_mover', methods: ['POST'])]
    public function mover(Request $request, EstructuraRepository $estructuraRepository, PersonaRepository $personaRepository, EntityManagerInterface $entityManager, Utils $utils): JsonResponse
    {
        if (!$this->isCsrfTokenValid('mover_estructura', (string) $request->request->get('_token'))) {
            return $this->json(['success' => false, 'message' => 'La operación ha expirado. Recargue la página.'], 403);
        }

        $estructura = $estructuraRepository->find($request->request->getInt('estructura'));
        $nuevoPadreId = $request->request->getInt('padre');
        $esAdmin = $this->isGranted('ROLE_ADMIN') || $utils->esAdminRRHH($this->getUser()->getId());
        $persona = $personaRepository->findOneBy(['usuario' => $this->getUser()->getId()]);
        $raizInstitucional = $persona?->getEntidad();

        if (!$estructura) {
            return $this->json(['success' => false, 'message' => 'La estructura seleccionada no existe.'], 404);
        }

        if (!$esAdmin) {
            if (!$raizInstitucional) {
                return $this->json(['success' => false, 'message' => 'Su usuario no está asociado a una universidad.'], 403);
            }
            $permitidas = $estructuraRepository->getEstructurasNegocios($raizInstitucional->getId());
            $idsPermitidos = array_fill_keys(array_map(static fn (Estructura $item): int => $item->getId(), $permitidas), true);
            if (!isset($idsPermitidos[$estructura->getId()]) || $estructura->getId() === $raizInstitucional->getId()) {
                return $this->json(['success' => false, 'message' => 'No puede mover la raíz institucional ni estructuras ajenas.'], 403);
            }
            $nuevoPadre = $nuevoPadreId > 0 ? $estructuraRepository->find($nuevoPadreId) : $raizInstitucional;
            if (!$nuevoPadre || !isset($idsPermitidos[$nuevoPadre->getId()])) {
                return $this->json(['success' => false, 'message' => 'El destino no pertenece a su universidad.'], 403);
            }
        } else {
            $nuevoPadre = $nuevoPadreId > 0 ? $estructuraRepository->find($nuevoPadreId) : null;
            if ($nuevoPadreId > 0 && !$nuevoPadre) {
                return $this->json(['success' => false, 'message' => 'La estructura de destino no existe.'], 404);
            }
        }

        if ($nuevoPadre === $estructura) {
            return $this->json(['success' => false, 'message' => 'Una estructura no puede depender de sí misma.'], 422);
        }
        for ($ascendiente = $nuevoPadre; $ascendiente !== null; $ascendiente = $ascendiente->getEstructura()) {
            if ($ascendiente->getId() === $estructura->getId()) {
                return $this->json(['success' => false, 'message' => 'El movimiento crearía un ciclo en la jerarquía.'], 422);
            }
        }

        $padreAnterior = $estructura->getEstructura()?->getNombre() ?? 'Nivel principal';
        $padreNuevo = $nuevoPadre?->getNombre() ?? 'Nivel principal';
        $estructura->setEstructura($nuevoPadre);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => sprintf('%s ahora depende de %s.', $estructura->getNombre(), $padreNuevo),
            'anterior' => $padreAnterior,
            'nuevo' => $padreNuevo,
        ]);
    }

    /**
     */
    #[Route('/estructura/detalles-arbol', name: 'app_estructura_detalles_arbol', methods: ['POST'])]
    public function detallesArbol(Request $request, EstructuraRepository $estructuraRepository): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->json(['error' => 'Petición no válida'], 400);
        }

        $id = $request->request->get('id');
        if (!$id) {
            return $this->json(['error' => 'ID de estructura no proporcionado'], 400);
        }

        $estructura = $estructuraRepository->find($id);
        if (!$estructura) {
            return $this->json(['error' => 'Estructura no encontrada'], 404);
        }

        return $this->render('modules/estructura/estructura/arbol-modal.html.twig', [
            'estructura' => $estructura
        ]);
    }

    /**
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/estructuras/mapa', name: 'app_estructura_index_mapa', methods: ['GET'])]
    public function indexMapa(EstructuraRepository $estructuraRepository, PersonaRepository $personaRepository, Utils $utils): Response
    {
        $persona = $personaRepository->findBy(['usuario' => $this->getUser()->getId()]);
        $entidad = isset($persona[0]) ? $persona[0]->getEntidad() : null;
        $isAdmin = $this->isGranted('ROLE_ADMIN') || $utils->esAdminRRHH($this->getUser()->getId());
        // Obtener registros según el rol
        if ($isAdmin) {
            $registros = $estructuraRepository->findBy(['activo' => true], ['nombre' => 'ASC']);
        } else {
            $estructuraNegocio = $entidad ? $entidad->getId() : null;
            $registros = $estructuraNegocio ? $estructuraRepository->getEstructurasNegocios($estructuraNegocio) : [];
        }

        return $this->render('modules/estructura/estructura/mapa.html.twig', [
            'registros' => $registros,
        ]);
    }

    /**
     * @param Request $request
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/registrar', name: 'app_estructura_registrar', methods: ['GET', 'POST'])]
    public function registrar(Request $request, PersonaRepository $personaRepository, EstructuraRepository $estructuraRepository, MunicipioRepository $municipioRepository, Utils $utils)
    {
        $persona = $personaRepository->findBy(['usuario' => $this->getUser()->getId()]);
        $entidad = isset($persona[0]) ? $persona[0]->getEntidad() : null;
        $isAdmin = $this->isGranted('ROLE_ADMIN') || $utils->esAdminRRHH($this->getUser()->getId());
        $estructuraNegocio = [$entidad ? $entidad->getId() : -1];
        if ($isAdmin) {
            $estructuraNegocio = [];
        }
        try {
            $estructuraEntity = new Estructura();
            $form = $this->createForm(EstructuraType::class, $estructuraEntity, ['action' => 'registrar', 'data_choices' => -1, 'estructuraNegocio' => $estructuraNegocio]);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $estructuraEntity->setFechaActivacion(\DateTime::createFromFormat('d/m/Y', $request->request->all()['estructura']['fechaActivacion']));
                $estructuraEntity->setMunicipio($municipioRepository->find($request->request->all()['estructura']['municipio']));
                $estructuraRepository->add($estructuraEntity, true);

                try {
                    $data['nombre_estructura'] = $request->request->all()['estructura']['nombre'];
                    $data['siglas'] = $request->request->all()['estructura']['siglas'];
                    $data['codigo_estructura'] = $request->request->all()['estructura']['siglas'];
                    $data['telefono'] = $request->request->all()['estructura']['telefono'];
                    $data['ubicacion'] = $request->request->all()['estructura']['ubicacion'];
                    $data['correo_electronico'] = $request->request->all()['estructura']['email'];
                    $data['id_categoria_estructura'] = $request->request->all()['estructura']['categoriaEstructura'];
                    $data['fecha_activacion'] = $request->request->all()['estructura']['fechaActivacion'];
                    $data['activo'] = 1;
                    $this->connection->insert('sq_estructura_composicion.tb_destructura', $data);
                } catch (\Exception $exception) {
                    $this->addFlash('error', $exception->getMessage());
                }

                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_estructura_index', [], Response::HTTP_SEE_OTHER);
            }


            return $this->render('modules/estructura/estructura/new.html.twig', [
                'form' => $form->createView(),
                'accion' => 'add',
                'entidad' => $entidad,
                'administrador_rrhh' => $utils->esAdminRRHH($this->getUser()->getId()),
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_estructura_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param User $estructura
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/{id}/modificar', name: 'app_estructura_modificar', methods: ['GET', 'POST'])]
    public function modificar(Request $request, Estructura $estructura, PersonaRepository $personaRepository, EstructuraRepository $estructuraRepository, MunicipioRepository $municipioRepository, Utils $utils)
    {
        try {
            $persona = $personaRepository->findBy(['usuario' => $this->getUser()->getId()]);
            $entidad = isset($persona[0]) ? $persona[0]->getEntidad() : null;
            $isAdmin = $this->isGranted('ROLE_ADMIN') || $utils->esAdminRRHH($this->getUser()->getId());
            $estructuraNegocio = [$entidad ? $entidad->getId() : -1];
            if ($isAdmin) {
                $estructuraNegocio = [];
            }
            $entidadEstructura = $estructura;
            while ($entidadEstructura && !$entidadEstructura->getEsEntidad()) {
                $entidadEstructura = $entidadEstructura->getEstructura();
            }

            $form = $this->createForm(EstructuraType::class, $estructura, ['action' => 'modificar', 'data_choices' => $estructura->getProvincia()->getId(), 'estructuraNegocio' => $estructuraNegocio]);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $estructura->setMunicipio($municipioRepository->find($request->request->all()['estructura']['municipio']));
                $estructura->setFechaActivacion(\DateTime::createFromFormat('d/m/Y', $request->request->all()['estructura']['fechaActivacion']));
                $estructuraRepository->edit($estructura);
                $this->addFlash('success', 'El elemento ha sido actualizado satisfactoriamente.');
                return $this->redirectToRoute('app_estructura_index', [], Response::HTTP_SEE_OTHER);
            }
            // Si es AJAX, solo devolvemos el pedazo de HTML del formulario
            if ($request->isXmlHttpRequest()) {
                return $this->render('modules/estructura/estructura/edit_ajax.html.twig', [
                    'estructura' => $estructura,
                    'entidad' => $entidadEstructura,
                    'form' => $form->createView(),
                ]);
            }
            return $this->render('modules/estructura/estructura/edit.html.twig', [
                'form' => $form->createView(),
                'estructura' => $estructura,
                'entidad' => $entidadEstructura,
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_estructura_modificar', ['id' => $estructura], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Estructura $estructura
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT') or is_granted('ROLE_GEST_CENTRO_AUTORIZADO') or is_granted('ROLE_GEST_FORMACION_DOCTORES')"))]
    #[Route('/{id}/eliminar', name: 'app_estructura_eliminar', methods: ['GET'])]
    public function eliminar(Request $request, Estructura $estructura, EstructuraRepository $estructuraRepository)
    {
        try {
            if ($estructuraRepository->find($estructura) instanceof Estructura) {
                $estructuraRepository->remove($estructura, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_estructura_index', [], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_estructura_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_estructura_index', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Estructura $estructura
     * @param Plaza $plazaRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT') or is_granted('ROLE_GEST_CENTRO_AUTORIZADO') or is_granted('ROLE_GEST_FORMACION_DOCTORES')"))]
    #[Route('/{id}/detail', name: 'app_estructura_detail', methods: ['GET', 'POST'])]
    public function detail(Request $request, Estructura $estructura, PlazaRepository $plazaRepository)
    {
        return $this->render('modules/estructura/estructura/detail.html.twig', [
            'item' => $estructura,
            'registros' => $plazaRepository->findBy(['estructura' => $estructura], ['responsabilidad' => 'asc']),
        ]);
    }

    /**
     * @param Request $request
     * @param Estructura $estructura
     * @param PlazaRepository $plazaRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/{id}/plaza', name: 'app_estructura_plaza', methods: ['GET', 'POST'])]
    public function plaza(Request $request, Estructura $estructura, PlazaRepository $plazaRepository)
    {
        try {
            $plaza = new Plaza();
            $form = $this->createForm(PlazaType::class, $plaza);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $plaza->setEstructura($estructura);
                $plazaRepository->add($plaza, true);
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_estructura_plaza', ['id' => $estructura->getId()], Response::HTTP_SEE_OTHER);
            }
            return $this->render('modules/estructura/estructura/plaza.html.twig', [
                'form' => $form->createView(),
                'registros' => $plazaRepository->findBy(['estructura' => $estructura], ['responsabilidad' => 'asc']),
                'estructura' => $estructura
            ]);
        } catch (UniqueConstraintViolationException $exception) {
            $this->addFlash('error', "El elemento ya existe ");
            return $this->redirectToRoute('app_estructura_plaza', ['id' => $estructura->getId()], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', 'Han ocurrido errores al procesar el formulario.');
            return $this->redirectToRoute('app_estructura_plaza', ['id' => $estructura->getId()], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     * @param Request $request
     * @param Plaza $plaza
     * @param PlazaRepository $plazaRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/{id}/eliminar-plaza', name: 'app_estructura_eliminar_plaza', methods: ['GET'])]
    public function eliminarPlaza(Request $request, Plaza $plaza, PlazaRepository $plazaRepository)
    {
        try {
            if ($plazaRepository->find($plaza) instanceof Plaza) {
                $plazaRepository->remove($plaza, true);
                $this->addFlash('success', 'El elemento ha sido eliminado satisfactoriamente.');
                return $this->redirectToRoute('app_estructura_plaza', ['id' => $plaza->getEstructura()->getId()], Response::HTTP_SEE_OTHER);
            }
            $this->addFlash('error', 'Error en la entrada de datos');
            return $this->redirectToRoute('app_estructura_plaza', ['id' => $plaza->getEstructura()->getId()], Response::HTTP_SEE_OTHER);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_estructura_plaza', ['id' => $plaza->getEstructura()->getId()], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * @param Request $request
     * @param Estructura $estructura
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/{id}/registrar-estructura-hija', name: 'app_estructura_registrar_hija', methods: ['GET', 'POST'])]
    public function registrarEstructuraHija(Request $request, ProvinciaRepository $provinciaRepository, MunicipioRepository $municipioRepository, TipoEstructuraRepository $tipoEstructuraRepository, CategoriaEstructuraRepository $categoriaEstructuraRepository, Estructura $estructura, EstructuraRepository $estructuraRepository, EntityManagerInterface $entityManager, Utils $utils)
    {
        try {
            $nuevaEstruc = new Estructura();
            $nuevaEstruc->setEstructura($estructura);

            $form = $this->createForm(EstructuraType::class, $nuevaEstruc, ['action' => 'modificar', 'data_choices' => -1, 'estructuraNegocio' => $utils->procesarRolesUsuarioAutenticado($this->getUser()->getId())]);
            $form->handleRequest($request);
            if ($form->isSubmitted()) {
                $nueva = new Estructura();

                $nueva->setNombre($request->request->all()['estructura']['nombre']);
                $nueva->setDireccion($request->request->all()['estructura']['direccion']);
                $nueva->setUbicacion($request->request->all()['estructura']['ubicacion']);
                $nueva->setSiglas($request->request->all()['estructura']['siglas']);
                $nueva->setTelefono($request->request->all()['estructura']['telefono']);
                $nueva->setActivo($request->request->all()['estructura']['activo']);
                $nueva->setEmail($request->request->all()['estructura']['email']);
                $nueva->setFechaActivacion(\DateTime::createFromFormat('d/m/Y', $request->request->all()['estructura']['fechaActivacion']));
                $nueva->setEstructura($estructuraRepository->find($request->request->all()['estructura']['estructura']));
                $nueva->setTipoEstructura($tipoEstructuraRepository->find($request->request->all()['estructura']['tipoEstructura']));
                $nueva->setCategoriaEstructura($categoriaEstructuraRepository->find($request->request->all()['estructura']['categoriaEstructura']));
                $nueva->setProvincia($provinciaRepository->find($request->request->all()['estructura']['provincia']));
                $nueva->setMunicipio($municipioRepository->find($request->request->all()['estructura']['municipio']));

                $entityManager->persist($nueva);
                $entityManager->flush();
                $this->addFlash('success', 'El elemento ha sido creado satisfactoriamente.');
                return $this->redirectToRoute('app_estructura_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('modules/estructura/estructura/new.html.twig', [
                'form' => $form->createView(),
                'accion' => 'add-hija'
            ]);
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_estructura_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }


    /**
     * Add package entity.
     *
     * @param $id
     * @param EstructuraRepository $estructuraRepository
     * @param Utils $utils
     * @return JsonResponse
     */
    #[Route('/{id}/estructura_dado_categoria', name: 'app_estructura_dado_categoria', methods: ['GET'])]
    public function getEstructuraDadoCategoria($id, EstructuraRepository $estructuraRepository, Utils $utils): JsonResponse
    {
        try {
//            $estructurasNegocio = $utils->procesarRolesUsuarioAutenticado($this->getUser()->getId());
//            return $this->json($utils->procesarNomenclador($estructuraRepository->geEstructurasDadoArrayEstructuras($id, $estructurasNegocio)));
            return $this->json($utils->procesarEstructuraV2($estructuraRepository->geEstructurasDadoArrayEstructurasTemp($id)));

        } catch (\Exception $exception) {
            return new JsonResponse([]);
        }
    }

    /**
     * Add package entity.
     *
     * @param $id
     * @param EstructuraRepository $estructuraRepository
     * @param Utils $utils
     * @return JsonResponse
     */
    #[Route('/{id}/estructura_dado_entidad', name: 'app_estructura_dado_entidad', methods: ['GET'])]
    public function getEstructuraDadoEntidad($id, EstructuraRepository $estructuraRepository, Utils $utils): JsonResponse
    {
        try {
//            $estructurasNegocio = $utils->procesarRolesUsuarioAutenticado($this->getUser()->getId());
//            return $this->json($utils->procesarNomenclador($estructuraRepository->geEstructurasDadoArrayEstructuras($id, $estructurasNegocio)));
            return $this->json($estructuraRepository->getEstructuraDadoEntidad($id));

        } catch (\Exception $exception) {
            return new JsonResponse([]);
        }
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/exportar_pdf', name: 'app_estructura_exportar_pdf', methods: ['GET', 'POST'])]
    public function exportarPdf(Request $request, HandlerFop $handFop, EstructuraRepository $estructuraRepository)
    {
        $export = $estructuraRepository->getExportarListado();
        $export = \App\Services\DoctrineHelper::toArray($export);
        return $handFop->exportToPdf(new ExportListEstructuraToPdf($export));
    }

    /**
     * Add package entity.
     *
     * @param Request $request
     * @param $id
     * @param MunicipioRepository $estructuraRepository
     * @param Utils $utils
     * @return JsonResponse
     */
    #[Route('/{id}/estructura_dado_id', name: 'app_estructura_dado_id', methods: ['GET'])]
    public function getEstructuraDadoId(Request $request, $id, EstructuraRepository $estructuraRepository, Utils $utils): JsonResponse
    {
        try {
            return $this->json($utils->procesarEstructura($estructuraRepository->find($id)));
        } catch (\Exception $exception) {
            return new JsonResponse([]);
        }
    }


    /**
     * @param EstructuraRepository $estructuraRepository
     * @return Response
*/
#[IsGranted(new Expression("is_granted('ROLE_ADMIN') or is_granted('ROLE_GEST_ESTRUCT')"))]
    #[Route('/{id}/exportar-estructuras-hijas', name: 'app_estructura_exportar_estructuras_hijas', methods: ['GET', 'POST'])]
    public function exportarEstructurasHija($id, HandlerFop $handFop, EstructuraRepository $estructuraRepository)
    {
        try {
            $export = $estructuraRepository->getExportarListadoDadoId($id);
            $export = \App\Services\DoctrineHelper::toArray($export);
            return $handFop->exportToPdf(new ExportListEstructuraToPdf($export));
        } catch (\Exception $exception) {
            $this->addFlash('error', $exception->getMessage());
            return $this->redirectToRoute('app_estructura_registrar', [], Response::HTTP_SEE_OTHER);
        }
    }

    /**
     */
    #[Route('/asignar-grupo', name: 'app_estructura_asignar_grupo', methods: ['POST'])]
    public function asignarGrupo(Request $request, EntityManagerInterface $entityManager): Response
    {
        $estructuraId = $request->request->get('estructuraId');
        $grupoId = $request->request->get('grupoId');
        $nuevoGrupoNombre = $request->request->get('nuevoGrupoNombre');

        $estructura = $entityManager->getRepository(Estructura::class)->find($estructuraId);

        if (!$estructura) {
            return $this->json(['success' => false, 'message' => 'Estructura no encontrada']);
        }

        // Si se está creando un nuevo grupo
        if ($nuevoGrupoNombre) {
            $grupo = new Grupo();
            $grupo->setNombre($nuevoGrupoNombre);
            $entityManager->persist($grupo);
            $entityManager->flush();

            $estructura->setGrupo($grupo);
        } // Si se está asignando un grupo existente
        elseif ($grupoId) {
            $grupo = $entityManager->getRepository(Grupo::class)->find($grupoId);
            if ($grupo) {
                $estructura->setGrupo($grupo);
            }
        } // Si no se envió ningún grupo (para eliminar la asignación)
        else {
            $estructura->setGrupo(null);
        }

        $entityManager->flush();

        return $this->json(['success' => true, 'message' => 'Grupo asignado correctamente']);
    }

    /**
     */
    #[Route('/grupos', name: 'app_grupos_index')]
    public function listarGrupos(EntityManagerInterface $entityManager): Response
    {
        $grupos = $entityManager->getRepository(Grupo::class)->findAll();

        return $this->render('estructura/grupos.html.twig', [
            'grupos' => $grupos,
        ]);
    }

    /**
     */
    #[Route('/obtener-grupos', name: 'app_estructura_obtener_grupos_all', methods: ['GET'])]
    public function obtenerGrupos(EntityManagerInterface $entityManager): JsonResponse
    {
        $grupos = $entityManager->getRepository(Grupo::class)->findBy(['activo' => true]);

        $gruposArray = [];
        foreach ($grupos as $grupo) {
            $gruposArray[] = [
                'id' => $grupo->getId(),
                'nombre' => $grupo->getNombre()
            ];
        }

        return $this->json($gruposArray);
    }


    /**
     */
    #[Route('/obtener-grupos/{idEstructura}', name: 'app_estructura_obtener_grupos', methods: ['GET'])]
    public function obtenerGruposDadoId(EntityManagerInterface $entityManager, $idEstructura = null): JsonResponse
    {
        $grupos = $entityManager->getRepository(Grupo::class)->findBy(['activo' => true]);

        $gruposArray = [];
        foreach ($grupos as $grupo) {
            $gruposArray[] = [
                'id' => $grupo->getId(),
                'nombre' => $grupo->getNombre()
            ];
        }

        // Obtener el grupo actual de la estructura si se proporciona ID
        $grupoActual = null;
        if ($idEstructura) {
            $estructura = $entityManager->getRepository(Estructura::class)->find($idEstructura);
            if ($estructura && $estructura->getGrupo() !== null) {
                // Ahora es una relación ManyToOne, así que obtenemos el grupo directamente
                $grupoActual = $estructura->getGrupo()->getId();
            }
        }

        return $this->json([
            'grupos' => $gruposArray,
            'grupoActual' => $grupoActual
        ]);
    }
}

