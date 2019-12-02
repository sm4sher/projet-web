<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use App\Entity\Evenement;
use App\Entity\Categorie;
use App\Entity\User;
use DateTime;

//use App\Entity\Commentaire;
//use App\Entity\Etat;
//use App\Entity\Panier;
//use App\Entity\Commande;
//use App\Entity\LigneCommande;

class AppFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }


    public function load(ObjectManager $manager)
    {
        $this->loadCategories($manager);
        $this->loadUsers($manager);
        $this->loadEvenements($manager);

        //        $this->loadEtatsCommandes($manager);
    }

    public function loadCategories(objectManager $manager)
    {
        echo "\n\nLoading categories:\n";
        $categories = [
            ['id' => 1, 'libelle' => 'conference'],
            ['id' => 2, 'libelle' => 'concert'],
            ['id' => 3, 'libelle' => 'théatre'],
            ['id' => 4, 'libelle' => 'danse'],
            ['id' => 5, 'libelle' => 'divers'],
        ];
        foreach ($categories as $categorie) {
            $new_type = new Categorie();
            $new_type->setLibelle($categorie['libelle']);
            echo $new_type;
            $manager->persist($new_type);
            $manager->flush();
        }
    }

    public function loadEvenements(objectManager $manager)
    {
        echo " \n\nLoading events:\n";
        $events = [
            ['id' => 1, 'nom' => 'Symfony Conference', 'description' => 'présentation de la conférence sur Symfony ', 'date' => '2019-02-20', 'prix' => '10.5', 'categorie' => 'conference', 'image' => 'symfony.png'],
            ['id' => 2, 'nom' => 'Laravel Conference', 'description' => 'présentation de la conférence sur Laravel ', 'date' => '2019-03-02', 'prix' => NULL, 'categorie' => 'conference', 'image' => 'laravel.jpg'],
            ['id' => 3, 'nom' => 'Django Conference', 'description' => 'présentation de la conférence sur Django ', 'date' => '2019-03-25', 'prix' => '20', 'categorie' => 'conference'],
            ['id' => 4, 'nom' => 'JAVA2EE Conference', 'description' => 'présentation de la conférence sur Java J2EE ', 'date' => '2019-04-02', 'prix' => '30', 'categorie' => 'conference'],
            ['id' => 5, 'nom' => 'Rails Conference', 'description' => 'présentation de la conférence sur Ruby on Rails ', 'date' => '2019-04-26', 'prix' => '12', 'categorie' => 'conference'],
            ['id' => 6, 'nom' => 'Concert tri yan', 'description' => 'concert tri yan axonne ', 'date' => '2019-04-26', 'prix' => '30', 'categorie' => 'concert', 'image' => 'TriYann2019.jpg'],
            ['id' => 7, 'nom' => 'Le Lac Des Cygnes', 'description' => 'danse le lac des Cygnes', 'date' => '2019-04-26', 'prix' => '30', 'categorie' => 'danse', 'image' => 'LacDesCygnes2020.jpg'],
        ];

        foreach ($events as $event) {
            $new_event = new Evenement();
            $new_event->setNom($event['nom']);
            $new_event->setDescription($event['description']);
            $new_event->setDate(new DateTime($event['date']));
            $new_event->setPrix($event['prix']);
            $new_event->setNombrePlaces(3);
            $new_event->setDisponible(True);
            if (isset($event['image']))
                $new_event->setPhoto($event['image']);
            else
                $new_event->setPhoto('noImage.jpg');
            $categorie = $manager->getRepository(Categorie::class)->findOneBy(['libelle' => $event['categorie']]);
            $new_event->setCategorie($categorie);

            echo $new_event;
            $manager->persist($new_event);
            $manager->flush();
        }
    }

    public function loadUsers(objectManager $manager)
    {
        echo "\n\nLoading users:\n";
        $users = [
            ['username' => 'admin', 'email' => 'admin@example.com', "password_plain" => 'admin', "role" => "ROLE_ADMIN"],
            ['username' => 'client', 'email' => 'client@example.com', "password_plain" => 'client', "role" => "ROLE_CLIENT"],
            ['username' => 'client2', 'email' => 'client2@example.com', "password_plain" => 'client2', "role" => "ROLE_CLIENT"]
        ];

        foreach ($users as $user) {
            $new_user = new User();
            $new_user->setRoles($user['role'])
                ->setUsername($user['username'])
                ->setEmail($user['email'])
                ->setIsActive('1');
            $password = $this->encoder->encodePassword($new_user, $user['password_plain']);
            $new_user->setPassword($password);
            $manager->persist($new_user);
            echo $new_user;
            $manager->flush();
        }
    }
    //    public function loadEtatsCommandes(objectManager $manager)
    //    {
    //        // état de la commande
    //        $etat1 = new Etat();
    //        $etat1->setNom('A préparer');
    //        $manager->persist($etat1);
    //        $etat2 = new Etat();
    //        $etat2->setNom('Expédié');
    //        $manager->persist($etat2);
    //        $manager->flush();
    //    }

}
