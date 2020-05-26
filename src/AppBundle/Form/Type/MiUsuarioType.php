<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MiUsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombreUsuario', TextType::class, [
                'label' => 'Nombre de usuario',
                'disabled' => true
            ])
            ->add('nombre', TextType::class, [
                'label' => 'Nombre'
            ])
            ->add('apellidos', TextType::class, [
                'label' => 'Apellidos'
            ])
            ->add('correo', TextType::class, [
                'label' => 'Correo'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class
        ]);
    }
}