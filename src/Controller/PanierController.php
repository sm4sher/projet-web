<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\PanierPlace;
use App\Form\PanierPlaceType;
use App\Repository\EvenementRepository;
use App\Repository\PanierPlaceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/panier")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier.index", methods={"GET"})
     */
    public function index(Security $security, PanierPlaceRepository $panierPlaceRepository): Response
    {
        $panier = $panierPlaceRepository->findBy(['user' => $security->getUser()]);

        return $this->render('panier_place/index.html.twig', [
            'panier' => $panier
        ]);
    }

    /**
     * @Route("/index", name="panier.filtre", methods={"GET", "POST"})
     */
    public function filtre(EvenementRepository $repo)
    {
        return $this->render('admin/evenement/filtre.html.twig', [
            'evenements' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/add/{id}", name="panier.add", methods={"GET", "POST"})
     */
    public function add(Request $request, Evenement $evenement, ObjectManager $manager, Security $security, $id)
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->findOneBy(['id' => $id]);
        $panierPlace = $this->getDoctrine()->getRepository(PanierPlace::class)->findOneBy(
            ['user' => $security->getUser(), 'evenement' => $evenement]
        );
        if (!$panierPlace) {
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

        $session = new Session();
        $session->getFlashBag()->add("display_panier", "");
        return $this->redirectToRoute('front_office');
    }

    /**
     * @Route("/edit/{id}", name="panier.edit", methods={"POST"})
     */
    public function edit(Request $request, Security $security, ObjectManager $manager, $id): Response
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->findOneBy(['id' => $id]);
        $panierPlace = $this->getDoctrine()->getRepository(PanierPlace::class)->findOneBy(['user' => $security->getUser(), 'evenement' => $evenement]);
        if ($panierPlace) {
            $panierPlace->setQuantite($request->get("quantite"));
            $manager->persist($panierPlace);
            $manager->flush();
        } else {
            $panierPlace = new PanierPlace();
            $panierPlace->setEvenement($evenement);
            $panierPlace->setUser($security->getUser());
            $panierPlace->setQuantite(1);
            $panierPlace->setDateAchat(new \DateTime());

            $manager->persist($panierPlace);
            $manager->flush();
        }
        $session = new Session();
        $session->getFlashBag()->add("display_panier", "");
        return $this->redirectToRoute("front_office");
    }

    /**
     * @Route("/delete/{id}", name="panier.delete", methods={"GET"})
     */
    public function delete(Request $request, Security $security, ObjectManager $manager, $id): Response
    {
        $evenement = $this->getDoctrine()->getRepository(Evenement::class)->findOneBy(['id' => $id]);
        $panierPlace = $this->getDoctrine()->getRepository(PanierPlace::class)->findOneBy(['user' => $security->getUser(), 'evenement' => $evenement]);
        if ($panierPlace) {
            $manager->remove($panierPlace);
            $manager->flush();
        }

        $session = new Session();
        $session->getFlashBag()->add("display_panier", "");
        return $this->redirectToRoute('front_office');
    }
}
