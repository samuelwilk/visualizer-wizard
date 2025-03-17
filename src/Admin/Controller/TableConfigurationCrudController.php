<?php

namespace App\Admin\Controller;

use App\Entity\TableConfiguration;
use App\Entity\VisualizationConfiguration;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

class TableConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TableConfiguration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Table Configuration')
            ->setEntityLabelInPlural('Table Configurations')
            ->setPageTitle(Crud::PAGE_NEW, 'Table Configuration');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title'),
            AssociationField::new('visualizationConfiguration')
                ->setRequired(true),
            ArrayField::new('selectedColumns')->setLabel('Columns'),
            ArrayField::new('sorting')->setLabel('Sorting'),
            ArrayField::new('filters')->setLabel('Filters'),
        ];
    }
}

