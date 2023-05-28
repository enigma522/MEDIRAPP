<?php

namespace App\Form;

use App\Entity\Todo;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TodoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
                'required' => true,
                'attr' => ['class' => 'form-control'],                
            ])
            ->add('datedebut',DateType::class,[
                'label' => 'datedebut',
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                 ])
            ->add('datefin',DateType::class,[
                'label' => 'datefin',
                'attr' => ['class' => 'form-control'],
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                 ])
            ->add('description', TextType::class, [
                'label' => 'description',
                'required' => true,
                'attr' => ['class' => 'form-control'],                
            ])
            ->add('patientemail',EmailType::class,['required' => true,
            'label'=>'patient email',
            'attr' => ['class' => 'form-control'],

            ])
            ->add('Ajouter',SubmitType::class,[
                'attr' => ['class' => 'custom-btn ma'],])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Todo::class,
        ]);
    }
}
