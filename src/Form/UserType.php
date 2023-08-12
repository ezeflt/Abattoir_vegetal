<?php 

namespace App\Form;

use App\Document\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        // Existing fields for Step 1: JE SUIS
            $builder
               
            ->add('username', TextType::class)

            ->add('city', TextType::class, [
                'attr' => [
                    'placeholder' => 'Paris',
                ],
            ])
            ->add('age', IntegerType::class, [
                
            ])

            ->add('gender', ChoiceType::class, [
                'choices'  => [
                    'H' => 'Homme',
                    'F' => 'Femme',
                    'Non-binaire' => 'Non-binaire'
                ],
                'choice_attr' => [
                    'Men' => ['class' => 'form-check-input'],
                    'Women' => ['class' => 'form-check-input'],
                    'Other' => ['class' => 'form-check-input'],
                ],
                'multiple' => false,
                'expanded' => true
            ])

            ->add('language', ChoiceType::class, [
                'choices' => [
                    'Anglais' => 'GB',
                    'Français' => 'FR',
                ],
                'choice_attr' => [
                    'Anglais' => ['class' => 'langue'],
                    'Français' => ['class' => 'langue'],
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('image', TextType::class, [
                'label'=> 'add a picture',
            ])
            ->add('job', TextType::class)

            ->add('description', TextType::class,)

            ->add('diet', ChoiceType::class, [
                
                'choices'  => [
                    'Végan(ne)' => 'Végan(ne)',
                    'Curieu(x/se)' => 'Curieu(x/se)',
                    'Végétarien(ne)' => 'Végétarien(ne)',
                    'Fléxitarien(ne)' => 'Fléxitarien(ne)'
                ],
                'choice_attr' => [
                    'Végan(ne)' => ['class' => 'regime'],
                    'Curieu(x/se)' => ['class' => 'regime'],
                    'Végétarien(ne)' => ['class' => 'regime'],
                    'Fléxitarien(ne)' => ['class' => 'regime'],
                ],
                'multiple' => false,
                'expanded' => true
            ])
       
         // New fields for Step 2: J'AIME
        ->add('centerOfInterest',  ChoiceType::class, [
            'choices'  => [
                'Animals' => 'Animaux',
                'Environment' => 'Environnement',
                'Games' => 'Jeux vidéos',
                'Art & Cult' => 'Art et Culture',
                'Sports' => 'Sport',
                'Travel' => 'Voyage',
                'Music' => 'Musique',
                'Dance' => 'Danse',
                'Sciences' => 'Sciences',
                'Bien-etre' => 'Bien-etre',
                'Food' => 'Food',
                'Social Act' => 'Activités sociales'
            ],
            'choice_attr' => [
                'Animaux' => ['class' => 'regime'],
                'Environnement' => ['class' => 'regime'],
                'Jeux vidéos' => ['class' => 'regime'],
                'Art et Culture' => ['class' => 'regime'],
                'Sport' => ['class' => 'regime'],
                'Voyage' => ['class' => 'regime'],
                'Musique' => ['class' => 'regime'],
                'Danse' => ['class' => 'regime'],
                'Sciences' => ['class' => 'regime'],
                'Bien-etre' => ['class' => 'regime'],
                'Food' => ['class' => 'regime'],
                'Activités sociales' => ['class' => 'regime'],           
             ],
            'multiple' => true,
            'expanded' => true,
            'attr' => ['class'=> 'checkboxBox']
        ])

        ->add('centerOfInterestPerso', TextType::class)
        ->add('submit', SubmitType::class, [
            'label' => 'valider',
        ]);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
} 

