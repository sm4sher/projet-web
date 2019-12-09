<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
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
    public function index(Request $request, Security $security, EvenementRepository $evenementRepository)
    {
        $events = $evenementRepository->findAll();
        return $this->render('frontOff/frontOFFICE.html.twig', [
            'events' => $events
        ]);
    }

    /**
     * @Route("/evenements/filtre", name="front_office_filtre", methods={"GET", "POST"})
     */
    public function filtre(Request $request, Security $security, EvenementRepository $evenementRepository, CategorieRepository $categorieRepository)
    {
        $categories = $categorieRepository->findAll();
        if($request->request->get('categorie')) {
            $categorie = $categorieRepository->find($request->request->get("categorie"));
            $events = $evenementRepository->findBy(['categorie' => $categorie]);
        }
        else {
            $events = $evenementRepository->findAll();
        }
        return $this->render('frontOff/filtre.html.twig', [
            'categories' => $categories,
            'selected' => $request->request->get('categorie'),
            'evenements' => $events
        ]);
    }
}
