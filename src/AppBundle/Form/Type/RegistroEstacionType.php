<?php


namespace AppBundle\Form\Type;


use AppBundle\Entity\RegistroEstacion;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistroEstacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechaHora', DateTimeType::class, [
                'format' => 'd m Y - h i s',
                'label' => 'Fecha y hora',
                'disabled' => true
            ])
            ->add('semanaAnio', TextType::class, [
                'label' => 'Semana del año',
                'disabled' => true
            ])
            ->add('temperatura', IntegerType::class, [
                'label' => 'Temperatura',
                'scale' => 2,
                'disabled' => true
            ])
            ->add('humedad', IntegerType::class, [
                'label' => 'Humedad',
                'scale' => 2,
                'disabled' => true
            ])
            ->add('lluvia', IntegerType::class, [
                'label' => 'Lluvia',
                'scale' => 2,
                'disabled' => true
            ])
            ->add('viento', IntegerType::class, [
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