<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FrontOfficeController extends AbstractController
{
    /**
     * @Route("/front/office", name="front_office")
     */
    public function index(Request $request, EvenementRepository $evenementRepository)
    {
        $events = $evenementRepository->findAll();
        return $this->render('frontOff/frontOFFICE.html.twig', [
            'events' => $events,
        ]);
    }
}
