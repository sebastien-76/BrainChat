<?php
// src/Form/InvitationType.php
namespace App\Form;

use App\Entity\Room;
use App\Entity\User;
use App\Entity\Invitation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('recipient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
                'label' => 'Destinataire',
                'required' => true
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'choice_label' => 'title',
                'label' => 'Salle',
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invitation::class,
        ]);
    }
}