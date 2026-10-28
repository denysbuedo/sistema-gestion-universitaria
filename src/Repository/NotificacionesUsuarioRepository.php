<?php

namespace App\Repository;

use App\Entity\NotificacionesUsuario;
use App\Integration\Notifications\InteroperabilityNotificationClient;
use App\Integration\Notifications\NotificationIntegrationException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

/**
 * @extends ServiceEntityRepository<NotificacionesUsuario>
 *
 * @method NotificacionesUsuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificacionesUsuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificacionesUsuario[]    findAll()
 * @method NotificacionesUsuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificacionesUsuarioRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly InteroperabilityNotificationClient $notificationClient,
        private readonly LoggerInterface $logger,
    )
    {
        parent::__construct($registry, NotificacionesUsuario::class);
    }

    public function add(NotificacionesUsuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
            $this->sendExternalEmail($entity);
        }
    }

    private function sendExternalEmail(NotificacionesUsuario $notification): void
    {
        if (!$this->notificationClient->isEnabled()) {
            return;
        }
        $recipient = trim((string) $notification->getUsuarioRecive()?->getEmail());
        if ($recipient === '') {
            $this->logger->warning('No se envió la notificación interoperable porque el destinatario no tiene correo.', [
                'notification_id' => $notification->getId(),
            ]);

            return;
        }

        try {
            $this->notificationClient->sendEmail(
                $recipient,
                'Notificación del Sistema de Gestión Universitaria',
                trim((string) $notification->getTexto()),
            );
        } catch (NotificationIntegrationException $exception) {
            // La notificación interna ya fue persistida. No se revierte la operación
            // ni se reintenta automáticamente para evitar correos duplicados.
            $this->logger->error('No se pudo entregar una notificación mediante Interoperabilidad.', [
                'notification_id' => $notification->getId(),
                'reason' => $exception->getMessage(),
            ]);
        }
    }

    public function remove(NotificacionesUsuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return NotificacionesUsuario[] Returns an array of NotificacionesUsuario objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?NotificacionesUsuario
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
