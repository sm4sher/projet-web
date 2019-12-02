<?php

namespace App\Controller;

use App\Repository\EvenementRepository;
use App\Repository\PanierPlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class FrontOfficeController extends AbstractController
{
    /**
     * @Route("/evenements", name="front_office")
     */
    public function index(Request $request, Security $security, EvenementRepository $evenementRepository, PanierPlaceRepository $panierPlaceRepository)
    {
        $events = $evenementRepository->findAll();
        $panier = $panierPlaceRepository->findBy(['user' => $security->getUser()]);
        return $this->render('frontOff/frontOFFICE.html.twig', [
            'events' => $events,
            'panier' => $panier
        ]);
    }
}
