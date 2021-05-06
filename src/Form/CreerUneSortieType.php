<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;


use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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
                'label'=>'Date et heure de la sortie',
                'input'=>'datetime',
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
            ->get('ville')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $this->addLieuField($form->getParent(), $form->getData());
                }
            );
            $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $data = $event->getData();
                    /* @var $lieu Lieu */
                    $lieu = $data->getLieu();
                    $form = $event->getForm();
                    if ($lieu) {
                        // On récupère la ville
                        $ville = $lieu->getVille();
                        // On crée les 1 champs supplémentaires
                        $this->addLieuField($form, $ville);
                        // On set les données
                        $form->get('ville')->setData($ville);
                    } else {
                        // On crée le champs en les laissant vide (champs utilisé pour le JavaScript)
                        $this->addLieuField($form, null);
                    }
                }
            )
        ;
    }
    private function addLieuField(FormInterface $form, ?Ville $ville)
    {
        $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
            'lieu',
            EntityType::class,
            null,
            [
                'class'           => 'App\Entity\Lieu',
                'placeholder'     => $ville ? 'Sélectionnez votre ville' : 'Sélectionnez votre lieu',
                'auto_initialize' => false,
                'choices'         => $ville ? $ville->getLieux() : []
            ]
        );
        $form->add($builder->getForm());
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
