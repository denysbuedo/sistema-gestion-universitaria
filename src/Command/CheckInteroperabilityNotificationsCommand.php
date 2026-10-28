<?php

namespace App\Command;

use App\Integration\Notifications\InteroperabilityNotificationClient;
use App\Integration\Notifications\NotificationIntegrationException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:interop:notifications:check', description: 'Comprueba la autenticación máquina a máquina para notificaciones')]
final class CheckInteroperabilityNotificationsCommand extends Command
{
    public function __construct(private readonly InteroperabilityNotificationClient $client)
    {
        parent::__construct();
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

            return Command::SUCCESS;
        } catch (NotificationIntegrationException $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }
}
