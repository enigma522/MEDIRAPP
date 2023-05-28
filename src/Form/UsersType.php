<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', TextType::class, [
            'label' => 'First name',
            'attr' => ['class' => 'form-control'],
            
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Last name',
            'attr' => ['class' => 'form-control'],
            
        ])
        ->add('email', EmailType::class,[
            'label' => 'Email',
            'attr' => ['class' => 'form-control'],
        ])
        //->add('roles')
        ->add('password', PasswordType::class,[
            'label' => 'Password',
            'attr' => ['class' => 'form-control'],
        ])
        ->add('phoneNumber', TelType::class, [
            'label' => 'Phone Number',
            'attr' => ['class' => 'form-control'],
            
            ])
        
        ->add('adress', TextType::class, [
            'label' => 'Adress',
            'attr' => ['class' => 'form-control'],
        ])
        //->add('zipcode')
        ->add('city', TextType::class, [
            'label' => 'City',
            'attr' => ['class' => 'form-control'],
            ])
        //->add('created_at')
        //->add('user_type')
        //->add('Age')
        ->add('birthday',BirthdayType::class, [
            'label' => 'Birthday',
            'attr' => ['class' => 'form-control'],
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd',
             ])
            ->add('EDITER',SubmitType::class,[
            'attr' => ['class' => 'custom-btn ma'],
        ])
        
           
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
