<?php


namespace AppBundle\Form\Type;


use AppBundle\Entity\RegistroEstacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistroEstacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaHora', DateTimeType::class, [
                'input' => 'datetime',
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy - HH:mm:ss',
                'label' => 'Fecha y hora',
                'disabled' => true
            ])
            ->add('temperatura', NumberType::class, [
                'label' => 'Temperatura',
                'scale' => 2,
                'disabled' => true
            ])
            ->add('humedad', NumberType::class, [
                'label' => 'Humedad',
                'scale' => 2,
                'disabled' => true
            ])
            ->add('lluvia', NumberType::class, [
                'label' => 'Lluvia',
                'scale' => 2,
                'disabled' => true
            ])
            ->add('viento', NumberType::class, [
                'label' => 'Viento',
                'scale' => 2,
                'disabled' => true
            ])
            ->add('dirViento', TextType::class, [
                'label' => 'Dirección viento',
                'disabled' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RegistroEstacion::class
        ]);
    }
}