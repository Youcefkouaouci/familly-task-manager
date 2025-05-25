<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    private UserRepository $userRepository;
    
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Task Title',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'Enter task title',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-input',
                    'rows' => 4,
                    'placeholder' => 'Enter task description',
                ],
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Household' => 'household',
                    'School' => 'school',
                    'Personal' => 'personal',
                    'Hygiene' => 'hygiene',
                    'Other' => 'other',
                ],
                'attr' => [
                    'class' => 'form-input',
                ],
            ])
            ->add('dueDate', DateTimeType::class, [
                'label' => 'Due Date',
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'form-input',
                ],
            ])
            ->add('reward', TextType::class, [
                'label' => 'Reward',
                'required' => false,
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'Enter reward (optional)',
                ],
            ])
            ->add('isUrgent', CheckboxType::class, [
                'label' => 'Mark as Urgent',
                'required' => false,
                'attr' => [
                    'class' => 'h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500',
                ],
                'label_attr' => [
                    'class' => 'ml-2 text-sm text-gray-700',
                ],
            ])
            ->add('children', EntityType::class, [
                'class' => User::class,
                'choices' => $this->userRepository->findAllChildren(),
                'choice_label' => function (User $user) {
                    return $user->getFullName();
                },
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'label' => 'Assign to children',
                'attr' => [
                    'class' => 'mt-1',
                ],
                'label_attr' => [
                    'class' => 'text-sm font-medium text-gray-700 block',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}