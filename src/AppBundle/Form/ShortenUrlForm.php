<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ShortenUrlForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('origin', TextType::class, array(
                'required' => true,
                'attr' => array(
                    'placeholder' => 'homepage.input.placeholder',
                    'autocomplete' => 'off'
                )
            ))
            ->add('submit', SubmitType::class, array('label' => 'form.shorten'));
    }
}