<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


//  http://api.symfony.com/4.0/Symfony/Component/Form/Extension/Core/Type.html
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use App\Entity\Categorie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, ['required' => false])
            ->add('prix', NumberType::class, ['required' => false])
            ->add('photo', TextType::class,[
                'label' => 'Photo de l evenement',
                'required' => false
            ])
            ->add('disponible', CheckboxType::class,  ['required' => false,'mapped' => true ])
            //     ->add('stock', NumberType::class, ['required' => false])
//            ->add('typeProduitId')

            ->add('categorie', EntityType::class, array(
                // query choices from this entity
                'class' => Categorie::class,

                // use the User.username property as the visible option string
                'choice_label' => 'libelle',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
                'required' => false,
                'placeholder' => 'Choisir une catégorie'
            ))


            ->add('submit', SubmitType::class)
            ->getForm()
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}