<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\PanierPlace;
use App\Form\PanierPlaceType;
use App\Repository\PanierPlaceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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

/*    /**
     * @Route("/new", name="panier_place_new", methods={"GET","POST"})
     */
/*
    public function new(Request $request, Evenement $evenement = null): Response
    {
        $panierPlace = new PanierPlace();
        if ($evenement != null) {
            $panierPlace->setEvenement($evenement);
        }
        $form = $this->createForm(PanierPlaceType::class, $panierPlace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$panierPlace->getId()) {
                $panierPlace->setDateAchat(new \DateTime)
                    ->setUser($this->getUser());
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($panierPlace);
            $entityManager->flush();

            return $this->redirectToRoute('panier_place_index');
        }

        return $this->render('panier_place/new.html.twig', [
            'panier_place' => $panierPlace,
            'form' => $form->createView(),
        ]);
    }*/

    /**
     * @Route("/{id}/add", name="panier_place_add", methods={"GET", "POST"})
     */
    public function add(Request $request, Evenement $evenement, ObjectManager $manager, Security $security, $id)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->findOneBy(['id' => $id]);
        $panierPlace = $this->getDoctrine()->getRepository(PanierPlace::class)->findOneBy(['user' => $security->getUser(), 'evenement' => $evenement]);
        if(!$panierPlace){
            $panierPlace = new PanierPlace();
            $panierPlace->setEvenement($evenement);
            $panierPlace->setUser($security->getUser());
            $panierPlace->setQuantite(1);
            $panierPlace->setDateAchat(new \DateTime());
        } else {
            $panierPlace->setQuantite($panierPlace->getQuantite() + 1);
        }
        $manager->persist($panierPlace);
        $manager->flush();

        return $this->redirectToRoute('front_office');

//        $form = $this->createForm(PanierPlaceType::class, $panierPlace);
//        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
//            if (!$panierPlace->getId()) {
//                $panierPlace->setDateAchat(new \DateTime)
//                    ->setUser($this->getUser())
//                    ->setEvenement($this->getEvenement());
//            }
//            $manager->persist($panierPlace);
//            $manager->flush();
//
//            return $this->redirectToRoute('panier_place_index');
//        }
//        return $this->render('panier_place/new.html.twig', [
//            'panier_place' => $panierPlace,
//            'evenement' => $evenement,
//            'form' => $form->createView(),
//        ]);
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
     * @Route("/{id}/edit", name="panier_place_edit", methods={"POST"})
     */
    public function edit(Request $request, Security $security, ObjectManager $manager, $id): Response
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->findOneBy(['id' => $id]);
        $panierPlace = $this->getDoctrine()->getRepository(PanierPlace::class)->findOneBy(['user' => $security->getUser(), 'evenement' => $evenement]);
        if($panierPlace){
            $panierPlace->setQuantite($request->get("quantite"));
            $manager->persist($panierPlace);
            $manager->flush();
        }
        return $this->redirectToRoute("front_office");

        /*$form = $this->createForm(PanierPlaceType::class, $panierPlace);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panier_place_index');
        }

        return $this->render('panier_place/edit.html.twig', [
            'panier_place' => $panierPlace,
            'form' => $form->createView(),
        ]);*/
    }

    /**
     * @Route("/{id}/delete", name="panier_place_delete", methods={"GET"})
     */
    public function delete(Request $request, Security $security, ObjectManager $manager, $id): Response
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->findOneBy(['id' => $id]);
        $panierPlace = $this->getDoctrine()->getRepository(PanierPlace::class)->findOneBy(['user' => $security->getUser(), 'evenement' => $evenement]);
        if($panierPlace){
            $manager->remove($panierPlace);
            $manager->flush();
        }
        return $this->redirectToRoute("front_office");

        /*if ($this->isCsrfTokenValid('delete' . $panierPlace->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($panierPlace);
            $entityManager->flush();
        }

        return $this->redirectToRoute('panier_place_index');*/
    }
}
