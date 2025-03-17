<?php

namespace App\Form\Builder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TableConfigurationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $columns = $options['columns'] ?? [];

        $builder
            ->add('tableTitle', TextType::class, [
                'label' => 'Table Title',
                'required' => true,
                'attr' => ['placeholder' => 'Enter table title...'],
            ])
            ->add('selectedColumns', ChoiceType::class, [
                'choices' => array_combine($columns, $columns),
                'expanded' => true, // Display as checkboxes
                'multiple' => true, // Allow multiple selections
                'label' => 'Select Columns for the Table',
                'required' => true,
            ])
            ->add('prev', SubmitType::class, [
                'label' => '← Back',
                'attr' => ['class' => 'btn btn-primary'],
            ])
            ->add('next', SubmitType::class, [
                'label' => 'Next →',
                'attr' => ['class' => 'btn btn-primary'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'columns' => [],
        ]);
    }
}
