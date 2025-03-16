<?php

namespace App\Form\Wizard;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ColumnSelectionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $columns = $options['columns']; // Pass columns dynamically from API response

        $builder
            ->add('selectedColumns', ChoiceType::class, [
                'choices' => array_combine($columns, $columns),
                'multiple' => true,
                'expanded' => true,
                'label' => 'Select Columns to Include',
                'required' => true,
            ])
            ->add('filters', CollectionType::class, [
                'entry_type' => ChoiceType::class,
                'entry_options' => [
                    'choices' => array_combine($columns, $columns),
                    'placeholder' => 'Select a column to filter by',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Row Filters',
            ])
            ->add('prev', SubmitType::class, ['label' => '← Back'])
            ->add('next', SubmitType::class, ['label' => 'Next →']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'columns' => [], // Default empty, must be passed dynamically
        ]);
    }
}
