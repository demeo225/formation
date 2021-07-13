<?php

namespace App\Form;

use App\Entity\Personne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, array(
        	    'required'	    => true,
        	    'label'	    => 'Nom'
            ))
            ->add('prenom', TextType::class, array(
        	    'required'	    => true,
        	    'label'	    => 'Prénom'
            ))
            ->add('fichier', TextType::class, array(
        	    'required'	    => true,
        	    'label'	    => 'fichier'
            ))
                                ->add('save', SubmitType::class, ['label' => 'Valider'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
    public function getBlockPrefix()
    {
        return 'personne';
    }
}
