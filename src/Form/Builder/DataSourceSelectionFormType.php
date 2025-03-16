<?php

namespace App\Form\Builder;

use App\Entity\DataSource;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataSourceSelectionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dataSource', EntityType::class, [
                'class' => DataSource::class,
                'choice_label' => 'name', // Display DataSource names
                'placeholder' => 'Select a Data Source',
                'required' => true,
            ])
            ->add('next', SubmitType::class, ['label' => 'Next →']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
