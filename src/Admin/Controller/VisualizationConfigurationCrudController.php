<?php
namespace App\Admin\Controller;

use App\Entity\VisualizationConfiguration;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class VisualizationConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VisualizationConfiguration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Visualization Configuration')
            ->setEntityLabelInPlural('Visualization Configurations');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')
                ->setHelp('Enter a name for the visualization.'),

            TextareaField::new('description')
                ->setHelp('Optional: Add a description for this visualization.')
                ->hideOnIndex(),

            AssociationField::new('dataSource')
                ->setHelp('Select the data source for this visualization.')
                ->setCrudController(DataSourceCrudController::class),

            AssociationField::new('preProcessedData')
                ->setHelp('Select the Pre-Processed data for this visualization.')
                ->setCrudController(DataSourceCrudController::class),

            ArrayField::new('configuration')
                ->setHelp('Define the chart/table configuration settings in JSON format.')
                ->hideOnIndex(),

            CollectionField::new('chartConfigurations')
                ->setEntryIsComplex(true)
                ->setHelp('Manage the chart configurations for this visualization.')
                ->hideOnIndex(),

            DateTimeField::new('createdAt')->onlyOnDetail(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
        ];
    }
}
