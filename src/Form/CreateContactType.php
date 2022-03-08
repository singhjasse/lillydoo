<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('first_name', TextType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('last_name', TextType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add(
                'address',
                TextType::class,
                ['required' => true, 'label' => 'Street and number', 'attr' => ['class' => 'form-control']]
            )
            ->add('zip', TextType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('city', TextType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('country', CountryType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('phone_number', NumberType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('birthday', BirthdayType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('email', EmailType::class, ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add(
                'picture1',
                FileType::class,
                [
                    'required' => false,
                    'mapped' => false,
                    'data_class' => null,
                    'attr' => ['class' => 'form-control'],
                    'label' => 'Picture'
                ]
            )
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary btn-sm pull-right'],
                'label' => 'Save'
            ])
            ->add('cancel', ButtonType::class, [
                'attr' => [
                    'class' => 'btn btn-link btn-sm pull-right',
                    'onclick' => 'window.location.href="/contacts"'
                ],
                'label' => 'Cancel',

            ]);
        //->setData($options['data']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                                   'data_class' => Contact::class
                               ]);
    }
}
