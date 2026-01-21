<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank; 
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;



class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
            [
                'constraints'=>[
                    new NotBlank(['message'=>'Task Title cannot be empty']),
                ],
            ])
            ->add('priority', ChoiceType::class,
            [
                'choices'=>
                [
                    'High'=>'HIGH',
                    'Medium'=>'MEDIUM',
                    'Low'=>'LOW',
                ],
            ])
            ->add('completed', CheckboxType::class, [
            'required' => false,
            ]);

            // ->add('createdAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
