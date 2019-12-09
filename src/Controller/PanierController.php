<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\PanierPlace;
use App\Form\PanierPlaceType;
use App\Repository\EvenementRepository;
use App\Repository\PanierPlaceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/panier")
 * @IsGranted("ROLE_CLIENT")
 */
class PanierController extends AbstractController
{
    /**
     * @Route("/", name="panier.index", methods={"GET"})
     */
    public function index(Security $security, PanierPlaceRepository $panierPlaceRepository): Response
    {
        $panier = $panierPlaceRepository->findBy(['user' => $security->getUser()]);
        if ($panier == null) {
            return $this->render('panier_place/index.html.twig');
        }
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
    public function add(Request $request, Evenement $evenement, ObjectManager $manager, Security $security)
    {
        if (!$this->isCsrfTokenValid('addpanier' . $evenement->getId(), $request->request->get('_token')))
            return $this->redirectToRoute('index.index');

        $session = new Session();
        if($evenement->getNombrePlaces() <= 0){
            $session->getFlashBag()->add("error", "Désolé, cet évenement n'est plus disponible");
            return $this->redirectToRoute('front_office');
        }

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
        $evenement->setNombrePlaces($evenement->getNombrePlaces()-1);
        $manager->persist($evenement);
        $manager->persist($panierPlace);
        $manager->flush();

        $session->getFlashBag()->add("display_panier", "");
        return $this->redirectToRoute('front_office');
    }

    /**
     * @Route("/edit/{id}", name="panier.edit", methods={"POST"})
     */
    public function edit(Request $request, Security $security, ObjectManager $manager, Evenement $evenement): Response
    {
        if (!$this->isCsrfTokenValid('editpanier' . $evenement->getId(), $request->request->get('_token')))
            return $this->redirectToRoute('index.index');

        $session = new Session();
        $quantite = $request->get("quantite");
        if($quantite > $evenement->getNombrePlaces()){
            $quantite = $evenement->getNombrePlaces();
            $session->getFlashBag()->add("error", "Désolé, seulement " . $quantite . " places sont disponibles pour cet évenement");
        }

        $panierPlace = $this->getDoctrine()->getRepository(PanierPlace::class)->findOneBy(['user' => $security->getUser(), 'evenement' => $evenement]);
        if ($panierPlace) {
            $diffQuantite = $quantite - $panierPlace->getQuantite();
            if($quantite == 0) {
                $manager->remove($panierPlace);
            } else {
                $panierPlace->setQuantite($quantite);
                $manager->persist($panierPlace);
            }
        } else {
            $diffQuantite = 1;
            $panierPlace = new PanierPlace();
            $panierPlace->setEvenement($evenement);
            $panierPlace->setUser($security->getUser());
            $panierPlace->setQuantite(1);
            $panierPlace->setDateAchat(new \DateTime());

            $manager->persist($panierPlace);
        }
        $evenement->setNombrePlaces($evenement->getNombrePlaces() - $diffQuantite);
        $manager->persist($evenement);
        $manager->flush();
        $session = new Session();
        $session->getFlashBag()->add("display_panier", "");
        return $this->redirectToRoute("front_office");
    }

    /**
     * @Route("/delete/{id}", name="panier.delete", methods={"POST"})
     */
    public function delete(Request $request, Security $security, ObjectManager $manager, Evenement $evenement): Response
    {
        if (!$this->isCsrfTokenValid('deletepanier' . $evenement->getId(), $request->request->get('_token')))
            return $this->redirectToRoute('index.index');

        $panierPlace = $this->getDoctrine()->getRepository(PanierPlace::class)->findOneBy(['user' => $security->getUser(), 'evenement' => $evenement]);
        if ($panierPlace) {
            $evenement->setNombrePlace($evenement->getNombrePlace() + $panierPlace->getQuantite());
            $manager->persist($evenement);
            $manager->remove($panierPlace);
            $manager->flush();
        }

        $session = new Session();
        $session->getFlashBag()->add("display_panier", "");
        return $this->redirectToRoute('front_office');
    }

    /**
     * @Route("/vider", name="panier.empty", methods={"POST"})
     */
    public function vider(Request $request, Security $security, ObjectManager $manager, PanierPlaceRepository $panierPlaceRepository): Response
    {
        if (!$this->isCsrfTokenValid('viderpanier', $request->request->get('_token')))
            return $this->redirectToRoute('index.index');

        $places = $panierPlaceRepository->findBy(['user' => $security->getUser()]);
        foreach ($places as $place) {
            $manager->remove($place);
            $manager->flush();
        }

        $session = new Session();
        $session->getFlashBag()->add("display_panier", "");
        return $this->redirectToRoute('front_office');
    }
}
