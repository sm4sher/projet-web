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
        if($this->isGranted('ROLE_ADMIN')) {
            //return $this->redirectToRoute('admin.index');
            return new Response($twig->render('backOff/backOFFICE.html.twig'));
        }
        if($this->isGranted('ROLE_CLIENT')) {
            return $this->redirectToRoute("front_office");
        }
        return new Response($twig->render('accueil.html.twig'));

    }
}