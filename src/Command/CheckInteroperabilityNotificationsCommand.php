<?php

namespace App\Command;

use App\Integration\Notifications\InteroperabilityNotificationClient;
use App\Integration\Notifications\NotificationIntegrationException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:interop:notifications:check', description: 'Comprueba la autenticación máquina a máquina para notificaciones')]
final class CheckInteroperabilityNotificationsCommand extends Command
{
    public function __construct(private readonly InteroperabilityNotificationClient $client)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('test-recipient', null, InputOption::VALUE_REQUIRED, 'Envía un único correo funcional al destinatario indicado')
            ->addOption('confirm-send', null, InputOption::VALUE_NONE, 'Confirma explícitamente el envío del correo de prueba');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $result = $this->client->checkAuthentication();
            $io->success(sprintf(
                'Autenticación válida para %s; audiencia %s; rol %s; vigencia restante %d segundos.',
                $result['client_id'],
                $result['audience'],
                $result['role'],
                $result['expires_in'],
            ));

            $recipient = trim((string) $input->getOption('test-recipient'));
            if ($recipient !== '') {
                if (!$input->getOption('confirm-send')) {
                    $io->error('Para realizar el envío debe añadir --confirm-send.');

                    return Command::INVALID;
                }
                $delivery = $this->client->sendEmail(
                    $recipient,
                    'Prueba funcional de notificaciones SGU',
                    'Mensaje de prueba funcional del Sistema de Gestión Universitaria (SGU) enviado desde el entorno de producción mediante la plataforma institucional de Interoperabilidad. No requiere respuesta.',
                );
                $io->success(sprintf('Solicitud única enviada. Estado del relay: %s.', (string) ($delivery['status'] ?? 'aceptada')));
            }

            return Command::SUCCESS;
        } catch (NotificationIntegrationException $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }
}
