<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Formulaire non lié à une entité : on récupère la motivation,
 * puis c'est InscriptionManager qui crée l'Inscription.
 *
 * @extends AbstractType<array{motivation: string}>
 */
final class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('motivation', TextareaType::class, [
            'label' => 'Votre motivation',
            'help' => 'Entre 20 et 500 caractères.',
            'constraints' => [
                new Assert\NotBlank(message: 'Merci d\'indiquer votre motivation.'),
                new Assert\Length(min: 20, max: 500),
            ],
        ]);
    }
}
