<?php 

namespace App\Form;

use App\Document\Group;
use Symfony\Component\Form\Extension\Core\Type\DateType; // Import the correct DateType
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResaType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('reservationDate', DateType::class, [
            'label' => 'enregistrer',
            'widget' => 'single_text',
            'html5' => true,
            'attr' => ['class' => 'js-datepicker'],
            // Add any additional attributes you might need for datepicker functionality
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'enregistrer'
        ]);



}

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
} 