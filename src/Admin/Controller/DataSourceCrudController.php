<?php

namespace App\Admin\Controller;

use App\Entity\DataSource;
use App\Enum\ApiResponseContentTypeEnum;
use App\Enum\DataSourceTypeEnum;
use App\Enum\IngestionModeEnum;
use App\Service\DataFetcherService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DataSourceCrudController extends AbstractCrudController
{
    public function __construct(private readonly DataFetcherService $dataFetcher, private readonly AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return DataSource::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Data Source')
            ->setEntityLabelInPlural('Data Sources');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            ChoiceField::new('type')
                ->setChoices(DataSourceTypeEnum::cases()),
            ChoiceField::new('ingestionMode')
                ->setChoices(IngestionModeEnum::cases()),
            ChoiceField::new('apiResponseContentType')
                ->setChoices(ApiResponseContentTypeEnum::cases()),
            TextareaField::new('description'),
            UrlField::new('apiEndpoint')->setHelp('Enter the API endpoint URL (if applicable)')->onlyOnForms(),
            TextareaField::new('apiCredentials')->setHelp('Enter API credentials in JSON format (if applicable)')->onlyOnForms(),
            TextField::new('filePath')->setHelp('The file path or name (if using file upload)')->onlyOnForms(),
            TextareaField::new('columns')->setHelp('List of columns to use (as JSON array)')->onlyOnForms(),
            TextareaField::new('rowFilters')->setHelp('Row filtering rules (as JSON)')->onlyOnForms(),
            DateTimeField::new('createdAt')->onlyOnDetail(),
            DateTimeField::new('updatedAt')->onlyOnDetail(),
        ];
    }

    public function testDataSource(AdminContext $context): RedirectResponse
    {
        $dataSource = $context->getEntity()->getInstance();

        try {
            $testData = $this->dataFetcher->fetchFromApi($dataSource);
            $recordCount = count($testData);

            $this->addFlash('success', "✅ Data source tested successfully! Retrieved {$recordCount} records.");
        } catch (\Exception $e) {
            $this->addFlash('danger', "❌ Error fetching data source: " . $e->getMessage());
        }

        return new RedirectResponse($this->adminUrlGenerator->setController(self::class)
            ->setAction(Crud::PAGE_DETAIL)
            ->setEntityId($dataSource->getId())
            ->generateUrl());
    }

    public function configureActions($actions): Actions
    {
        $testDataSourceAction = Action::new('testDataSource', 'Test Data Source')
            ->linkToCrudAction('testDataSource')
            ->displayIf(fn ($entity) => $entity instanceof DataSource);
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $testDataSourceAction);
    }
}
