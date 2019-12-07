<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserRegisterType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $user->setRoles('ROLE_CLIENT');
        $form = $this->createForm(UserRegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile", name="user_show_self", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function showSelf(Security $security): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $security->getUser(),
            'full_profile' => true
        ]);
    }

    /**
     * @Route("/profile/{id}", name="user_show", methods={"GET"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function show(Security $security, User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'full_profile' => ($user === $security->getUser() || $security->isGranted('ROLE_ADMIN'))
        ]);
    }

    /**
     * @Route("/edit/{id}", name="user_edit", methods={"GET","POST"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit(Request $request, Security $security, User $user): Response
    {
        if ($user !== $security->getUser() && !$security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('index.index');
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            if($security->isGranted('ROLE_ADMIN'))
                return $this->redirectToRoute('user_index');
            return $this->redirectToRoute('user_show_self');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="user_delete", methods={"DELETE"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Security $security, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('user_index');
    }
}
