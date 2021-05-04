<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;


use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\ChoiceList\Factory\Cache\ChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function Sodium\add;

class CreerUneSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null,[
                'label'=> 'Nom de la sortie:',

            ])
            ->add('dateHeureDebut', DateTimeType::class,[
                'label'=>'Date et heure de la sortie:',
                'input'=>'datetime',
                'html5' => true,
                'widget' => 'choice'
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => "Date limite d'inscription",
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('nbInsriptionsMax', IntegerType::class,[
                'label'=>'Nombre de places'

            ])
            ->add('duree', TimeType::class, [

                'label'=>'Durée'
            ])


            ->add('infosSortie', TextType::class,[
                'label'=>'Description et infos'
            ] )
            ->add('ville', EntityType::class,[
                'class'=>Ville::class,
                'choice_label'=>'nom',
                'label'=>'Ville :'
                ])

            ->add('lieu', EntityType::class,[
                'class'=>Lieu::class,
                'choice_label'=>'nom',
                'label'=>'Lieu :'
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
