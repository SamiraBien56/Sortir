<?php

namespace App\Form;

use App\Entity\Etat;
use App\Entity\Sortie;



use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label' => "Date limite d'inscription",
                'html5' => true,
                'widget' => 'single_text',
            ])
            ->add('nbInsriptionsMax', IntegerType::class,[
                'label'=>'Nombre de places'

            ])
            ->add('duree', IntegerType::class, [

                'label'=>'Durée'
            ])


            ->add('infosSortie',textType::class,[
                'label'=>'Description et infos'
            ] )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
