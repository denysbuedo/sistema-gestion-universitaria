<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Psr\Log\LoggerInterface;

/**
 */
#[Route('/profile')]
class ProfileController extends AbstractController
{
   
    /**
     * @param ContainerInterface $container
     * @return Response
     */
    #[Route('/gotochat', name: 'app_chat_redirect', methods: ['GET'])]
    public function gotoChar(ContainerInterface $container)
    {            
        $url = $container->getParameter('fullLoginUrlChat');
        return new RedirectResponse($url);
    }

}
