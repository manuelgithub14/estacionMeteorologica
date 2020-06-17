<?php


namespace AppBundle\Form\Type;


use AppBundle\Repository\RegistrosRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AniosType extends AbstractType
{
    public $aniosArray = [];

    /**
     * AniosType constructor.
     * @param array $aniosArray
     */
    public function __construct(RegistrosRepository $registrosRepository)
    {
        $anios = $registrosRepository->findAnios();
        foreach ($anios as $anio){
            $this->aniosArray[$anio[1]] = $anio[1];
        };
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lista', ChoiceType::class, array(
                'label' => 'Años',
                'choices' => $this->aniosArray
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        ]);
    }
}