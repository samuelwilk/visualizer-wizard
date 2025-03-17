<?php

namespace App\Admin\Controller;

use App\Entity\VisualizationBuilderProgress;
use App\Enum\VisualizationBuilderStatusEnum;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class VisualizationBuilderProgressCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return VisualizationBuilderProgress::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('status')
                ->setChoices([
                    'Selecting Data Source' => VisualizationBuilderStatusEnum::SELECTING_DATA_SOURCE,
                    'Configuring Columns' => VisualizationBuilderStatusEnum::CONFIGURING_COLUMNS,
                    'Configuring Tables' => VisualizationBuilderStatusEnum::CONFIGURING_TABLES,
                    'Configuring Charts' => VisualizationBuilderStatusEnum::CONFIGURING_CHARTS,
                    'Configuring Layout' => VisualizationBuilderStatusEnum::CONFIGURING_LAYOUT,
                    'Waiting for Review' => VisualizationBuilderStatusEnum::WAITING_FOR_REVIEW,
                    'Finalized' => VisualizationBuilderStatusEnum::FINALIZED,
                    'Editing' => VisualizationBuilderStatusEnum::EDITING_EXISTING,
                    'Duplicating' => VisualizationBuilderStatusEnum::DUPLICATING,
                ])
                ->setLabel('Builder Status'),
            AssociationField::new('builderDataSource')->setLabel('Data Source'),
            AssociationField::new('visualizationConfiguration')->setLabel('Visualization Configuration'),
            AssociationField::new('lastModifiedBy')->setLabel('Last Modified By'),
            DateTimeField::new('updatedAt')->setLabel('Last Updated'),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, Action::new('continueEditing', 'Continue Editing')
                ->linkToRoute('admin_builder', fn (VisualizationBuilderProgress $progress) => [
                    'progress' => $progress->getId(),
                ])
                ->displayIf(fn (VisualizationBuilderProgress $progress) => $progress->getStatus() !== VisualizationBuilderStatusEnum::FINALIZED)
            );
    }

}
