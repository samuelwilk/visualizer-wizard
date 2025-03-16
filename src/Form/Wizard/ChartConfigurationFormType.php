<?php

namespace App\Form\Wizard;

use App\Enum\ChartTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChartConfigurationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $columns = $options['columns'];

        $builder
            ->add('chartType', ChoiceType::class, [
                'choices' => ChartTypeEnum::cases(),
                'label' => 'Chart Type',
                'required' => true,
            ])
            ->add('title', TextType::class, ['label' => 'Chart Title', 'required' => true])
            ->add('labels', ChoiceType::class, [
                'choices' => array_combine($columns, $columns),
                'multiple' => true,
                'expanded' => true,
                'label' => 'Labels (X-Axis)',
                'required' => true,
            ])
            ->add('series', ChoiceType::class, [
                'choices' => array_combine($columns, $columns),
                'multiple' => true,
                'expanded' => true,
                'label' => 'Series (Y-Axis)',
                'required' => true,
            ])
            ->add('prev', SubmitType::class, ['label' => '← Back'])
            ->add('next', SubmitType::class, ['label' => 'Next →']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'columns' => [],
        ]);
    }
}
