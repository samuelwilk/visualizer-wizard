<?php

namespace App\Admin\Controller;

use App\Entity\PreProcessedData;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PreProcessedDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PreProcessedData::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('dataSource')
                ->setHelp('Select the data source for this processed data.')
                ->setCrudController(DataSourceCrudController::class),

            ArrayField::new('data')
                ->setHelp('Pre-processed dataset stored as JSON.')
                ->hideOnIndex(),

            DateTimeField::new('scheduledAt')
                ->setHelp('When this dataset was scheduled for processing.')
                ->setFormat('yyyy-MM-dd HH:mm'),

            BooleanField::new('isActive')
                ->setHelp('Indicates whether this dataset is currently in use.'),

            DateTimeField::new('createdAt')->onlyOnDetail(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
        ];
    }
}
