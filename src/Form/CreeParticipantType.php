<?php


namespace App\Form;


use App\Entity\Campus;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CreeParticipantType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
{

    $builder
        ->add('pseudo')
        ->add('nom')
        ->add('prenom')
        ->add('campus', EntityType::class, [
            'class' => Campus::class,
            'choice_label' => 'nom'
        ])
        ->add('telephone')
        ->add('mail')

        ->add('image', FileType::class, [
            'label' => false,
            'mapped' => false,
            'required' => false
        ])
        ;
}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
            'form_action' => null,
        ]);
    }
}