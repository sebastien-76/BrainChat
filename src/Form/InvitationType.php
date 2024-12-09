<?php
// src/Form/InvitationType.php

namespace App\Form;

use App\Entity\Invitation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Entity\Room;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
            // In buildForm method:
            $builder
                ->add('user', EntityType::class, [
                    'class' => User::class,
                    'choice_label' => 'author',
                    'required' => true,
                ])
                ->add('room', EntityType::class, [
                    'class' => Room::class,
                    'choice_label' => 'title',
                    'required' => true,
                ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invitation::class,
        ]);
    }
}