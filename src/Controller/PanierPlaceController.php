<?php

namespace App\Controller;

use App\Entity\PanierPlace;
use App\Form\PanierPlaceType;
use App\Repository\PanierPlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/panier/place")
 */
class PanierPlaceController extends AbstractController
{
    /**
     * @Route("/", name="panier_place_index", methods={"GET"})
     */
    public function index(PanierPlaceRepository $panierPlaceRepository): Response
    {
        return $this->render('panier_place/index.html.twig', [
            'panier_places' => $panierPlaceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="panier_place_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $panierPlace = new PanierPlace();
        $form = $this->createForm(PanierPlaceType::class, $panierPlace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($panierPlace);
            $entityManager->flush();

            return $this->redirectToRoute('panier_place_index');
        }

        return $this->render('panier_place/new.html.twig', [
            'panier_place' => $panierPlace,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="panier_place_show", methods={"GET"})
     */
    public function show(PanierPlace $panierPlace): Response
    {
        return $this->render('panier_place/show.html.twig', [
            'panier_place' => $panierPlace,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="panier_place_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PanierPlace $panierPlace): Response
    {
        $form = $this->createForm(PanierPlaceType::class, $panierPlace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panier_place_index');
        }

        return $this->render('panier_place/edit.html.twig', [
            'panier_place' => $panierPlace,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="panier_place_delete", methods={"DELETE"})
     */
    public function delete(Request $request, PanierPlace $panierPlace): Response
    {
        if ($this->isCsrfTokenValid('delete'.$panierPlace->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($panierPlace);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_place_index');
    }
}
