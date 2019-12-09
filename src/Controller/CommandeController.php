<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Place;
use App\Form\CommandeType;
use App\Entity\User;
use App\Repository\CommandeRepository;
use App\Repository\EtatRepository;
use App\Repository\PanierPlaceRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/commandes")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/", name="commande_index", methods={"GET"})
     */
    public function index(Security $security, CommandeRepository $commandeRepository): Response
    {
        if($security->isGranted("ROLE_ADMIN")){
            $commandes = $commandeRepository->findBy([], ['etat' => 'ASC', 'date' => 'DESC']);
        }
        else {
            $commandes = $commandeRepository->findBy(['user' => $security->getUser()]);
        }
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * @Route("/valider", name="commande_new", methods={"POST"})
     */
    public function new(Request $request, Security $security, ObjectManager $manager, PanierPlaceRepository $panierPlaceRepository, EtatRepository $etatRepository): Response
    {
        if (!$this->isCsrfTokenValid('validpanier', $request->request->get('_token')))
            return $this->redirectToRoute('index.index');

        $places = $panierPlaceRepository->findBy(['user' => $security->getUser()]);

        if ($places) {
            $etat = $etatRepository->findOneBy(['nom' => 'A préparer']);
            if (!$etat) {
                $this->redirectToRoute("index.index");
            }
            $commande = new Commande();
            $commande->setUser($security->getUser());
            $commande->setDate(new \DateTime());
            $commande->setEtat($etat);
            foreach ($places as $place) {
                $new_place = new Place();
                $new_place->copyPanierPlace($place);
                $new_place->setCommande($commande);
                $manager->remove($place);
                $manager->persist($new_place);
            }
            $manager->persist($commande);
            $manager->flush();

            $session = new Session();
            $session->getFlashBag()->add("success", "La commande a bien été effectuée !");
        }

        return $this->redirectToRoute('commande_index');
    }

    /**
     * @Route("/{id}", name="commande_show", methods={"GET"})
     */
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
}
