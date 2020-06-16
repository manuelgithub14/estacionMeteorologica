<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['nuevo'] === false) {
            $builder
                ->add('nombre', TextType::class, [
                    'label' => 'Nombre'
                ])
                ->add('apellidos', TextType::class, [
                    'label' => 'Apellidos'
                ])
                ->add('correo', TextType::class, [
                    'label' => 'Correo'
                ])
                ->add('nombreUsuario', TextType::class, [
                    'label' => 'Usuario'
                ])
                ->add('admin', CheckboxType::class, [
                    'required' => false,
                    'label' => '¿Es admin?'
                ]);
        }else{
            $builder
                ->add('nombre', TextType::class, [
                    'label' => 'Nombre'
                ])
                ->add('apellidos', TextType::class, [
                    'label' => 'Apellidos'
                ])
                ->add('correo', TextType::class, [
                    'label' => 'Correo'
                ])
                ->add('nombreUsuario', TextType::class, [
                    'label' => 'Usuario'
                ])
                ->add('password', RepeatedType::class, [
                    'type' => PasswordType::class,
                    'invalid_message' => 'Las dos contraseñas no coinciden',
                    'first_options'  => [
                        'label' => 'Contraseña'
                    ],
                    'second_options' => [
                        'label' => 'Repita la contraseña'
                    ]
                ])
                ->add('admin', CheckboxType::class, [
                    'required' => false,
                    'label' => '¿Es admin?'
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'nuevo' => false
        ]);
    }
}