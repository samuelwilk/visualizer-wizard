<?php

namespace App\Admin\Controller;

use App\Entity\ChartConfiguration;
use App\Enum\ChartTypeEnum;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ChartConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ChartConfiguration::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('chartType')
                ->setChoices(ChartTypeEnum::cases())
                ->setHelp('Select the type of chart to display.'),

            TextField::new('title')
                ->setHelp('Enter a title for the chart.'),

            ArrayField::new('labels')
                ->setHelp('Define the labels for the X-axis (if applicable).')
                ->hideOnIndex(),

            ArrayField::new('series')
                ->setHelp('Define the data series for the chart.')
                ->hideOnIndex(),

            AssociationField::new('visualizationConfiguration')
                ->setHelp('Link this chart to a visualization configuration.')
                ->setCrudController(VisualizationConfigurationCrudController::class),

            DateTimeField::new('createdAt')->onlyOnDetail(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
        ];
    }
}
