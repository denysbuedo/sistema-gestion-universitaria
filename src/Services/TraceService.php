<?php

namespace App\Services;

use App\Entity\Personal\Persona;
use App\Entity\Traza\Accion;
use App\Entity\Traza\ConfiguracionTraza;
use App\Entity\Traza\Objeto;
use App\Entity\Traza\TipoTraza;
use App\Entity\Traza\Traza;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;

final class TraceService
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private LoggerInterface $logger
    ) {
    }

    public function registrar($accion, $objeto, $dataAnterior, $nuevaData, $tipoTraza = 2): bool
    {
        try {
            $request = $this->requestStack->getCurrentRequest();
            if ($request === null || !$request->hasSession()) {
                return false;
            }

            $personaId = $request->getSession()->get('id_persona_usuario_autenticado');
            $configuration = $this->entityManager->getRepository(ConfiguracionTraza::class)
                ->findOneBy(['persona' => $personaId]);
            if (!$configuration instanceof ConfiguracionTraza || !$configuration->getActivo()) {
                return false;
            }

            $accionEntity = $this->entityManager->getRepository(Accion::class)->find($accion);
            $tipoEntity = $this->entityManager->getRepository(TipoTraza::class)->find($tipoTraza);
            $objetoEntity = $this->entityManager->getRepository(Objeto::class)->find($objeto);
            $persona = $this->entityManager->getRepository(Persona::class)->find($personaId);
            if (!$accionEntity || !$tipoEntity || !$objetoEntity || !$persona) {
                return false;
            }

            $client = $this->detect((string) $request->headers->get('User-Agent', ''));
            $trace = (new Traza())
                ->setDataAnterior($this->serializer->serialize($dataAnterior, 'json'))
                ->setData($this->serializer->serialize($nuevaData, 'json'))
                ->setAccion($accionEntity)
                ->setTipoTraza($tipoEntity)
                ->setObjeto($objetoEntity)
                ->setPersona($persona)
                ->setIp($request->getClientIp() ?? 'unknown')
                ->setNavegador($client['browser'])
                ->setSistemaOperativo($client['os']);

            $this->entityManager->persist($trace);
            $this->entityManager->flush();

            return true;
        } catch (\Throwable $exception) {
            $this->logger->warning('No se pudo registrar una traza de SGU.', [
                'exception_class' => $exception::class,
            ]);

            return false;
        }
    }

    /** @return array{browser: string, os: string} */
    private function detect(string $userAgent): array
    {
        $info = ['browser' => 'OTHER', 'os' => 'OTHER'];
        foreach (['IE', 'OPERA', 'MOZILLA', 'NETSCAPE', 'FIREFOX', 'SAFARI', 'CHROME'] as $browser) {
            $position = strpos(strtoupper($userAgent), $browser);
            if ($position !== false) {
                $version = substr($userAgent, $position + strlen($browser), 15);
                $version = preg_replace('/[^0-9,.]/', '', $version);
                $info['browser'] = ucfirst(strtolower($browser)) . ' ' . $version;
            }
        }
        foreach (['WIN', 'MAC', 'LINUX'] as $os) {
            if (strpos(strtoupper($userAgent), $os) !== false) {
                $info['os'] = ucfirst(strtolower($os));
            }
        }

        return $info;
    }
}
