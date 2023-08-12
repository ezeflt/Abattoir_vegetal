<?php 

namespace App\Form;

use App\Document\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType 
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Your email adress',
                ],
            ])

            ->add('password', RepeatedType::class,[
                'attr' => [
                    'placeholder' => 'Password',
                ],
                'type' => PasswordType::class,
                ])
                ->add('submit', SubmitType::class, [
                    'attr' => [
                    ],
                ])
            ->add('terms', CheckboxType::class, [
                'label' => 'I have read and accept the general conditions',
                'property_path' => 'termsAccepted'])

                    ->add('submit', SubmitType::class, [
                        'label' => 'S\'enregistrer'
                    ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
} 