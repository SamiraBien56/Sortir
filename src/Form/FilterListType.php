<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'choice_label' => 'nom',
                'label' => 'Campus : '
            ])
            ->add('nom', TextType::class, [
                'label' => 'Le nom de la sortie contient : ',
                'required'=> false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('dateMin', DateType::class, [
                'label' => 'Entre',
                'html5'=> 'true',
                'widget' => 'single_text'
            ])
            ->add('dateMax', DateType::class, [
                'label' => 'Et',
                'html5'=> 'true',
                'widget' => 'single_text'
            ])
            ->add('organisateur', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur",
                'required' => false,

            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => "Sortie auxquelles je suis inscrit/e",
                'required' => false,
            ])
            ->add('nonInscrit', CheckboxType::class, [
                'label' => "Sortie auxquelles je ne suis pas inscrit/e",
                'required' => false,
            ])
            ->add('dateHeureDebut', CheckboxType::class, [
                'label' => "Sorties passées",
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}
