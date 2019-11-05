<?php

namespace App\Controller;


use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;


class IndexController extends Controller
{
    /**
     * @Route("/", name="index.index")
     */
    public function index(Request $request, Environment $twig)
    {

//        if(! is_null($this->getUser())){
//            echo "<br>";
//            echo " id: ".$this->getUser()->getId();
//            echo " roles :   ";
//            print_r($this->getUser()->getRoles());
//            die();
//        }

        if($this->isGranted('ROLE_ADMIN')) {
            //return $this->redirectToRoute('admin.index');
            return new Response($twig->render('backOff/backOFFICE.html.twig'));
        }
        if($this->isGranted('ROLE_CLIENT')) {
            // return $this->redirectToRoute('panier.index');
            return new Response($twig->render('frontOff/frontOFFICE.html.twig'));
        }
        return new Response($twig->render('accueil.html.twig'));

    }

    /**
     * @Route("/client", name="index.client")
     */
    public function indexClient(Request $request, Environment $twig)
    {
        if($this->isGranted('ROLE_ADMIN')) {
            //return $this->redirectToRoute('admin.index');
            return new Response($twig->render('backOff/backOFFICE.html.twig'));
        }
        if($this->isGranted('ROLE_CLIENT')) {
            // return $this->redirectToRoute('client.index');
            return new Response($twig->render('frontOff/frontOFFICE.html.twig'));
        }
        return new Response($twig->render('accueil.html.twig'));

    }
}