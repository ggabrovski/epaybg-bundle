<?php

namespace Otobul\EpaybgBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EpayButtonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('POST')
            ->setAction($options['actionUrl'])
        ;
        foreach ($options['params'] as $key => $value) {
            $builder->add($key, HiddenType::class, ['data' => $value, 'mapped' => false]);
        }
    }

    /**
     * This will remove formTypeName from the form
     * @return null
     */
    public function getBlockPrefix() {
        return '';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
            'params' => [],
            'actionUrl' => null,
        ]);
    }
}
