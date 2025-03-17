<?php
namespace App\Admin\Controller;

use App\Entity\BuilderDataSource;
use App\Entity\DataSource;
use App\Entity\VisualizationBuilderProgress;
use App\Entity\VisualizationConfiguration;
use App\Enum\VisualizationBuilderStatusEnum;
use App\Form\Builder\ChartConfigurationFormType;
use App\Form\Builder\ColumnSelectionFormType;
use App\Form\Builder\DataSourceSelectionFormType;
use App\Form\Builder\ReviewAndSaveFormType;
use App\Form\Builder\TableConfigurationFormType;
use App\Form\Builder\VisualizationConfigurationFormType;
use App\Service\DataFetcherService;
use App\Service\VisualizationBuilderService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VisualizationBuilderController extends AbstractController
{
    public function __construct(
        private readonly DataFetcherService $dataFetcherService,
        private readonly VisualizationBuilderService $builderService,
        private readonly EntityManagerInterface $entityManager,
    ) {}

    #[Route('/admin/builder', name: 'admin_builder_index', options: ['expose' => true])]
    public function builder(Request $request): Response
    {
        // Fetch the current EasyAdmin context
        $easyAdminContext = $request->attributes->get('easyadmin_context');

        if (!$easyAdminContext instanceof AdminContext) {
            // If EasyAdmin context is missing, redirect with it explicitly
            return $this->redirectToRoute('admin', [
                'routeName' => 'admin_builder_index'
            ]);
        }

        $user = $this->getUser();
        $builderProgress = $this->builderService->getOrCreateBuilderProgress($user);

        return match ($builderProgress->getStatus()) {
            VisualizationBuilderStatusEnum::SELECTING_DATA_SOURCE => $this->handleSelectingDataSourceStep($request, $builderProgress),
            VisualizationBuilderStatusEnum::CONFIGURING_COLUMNS => $this->handleConfiguringColumnsStep($request, $builderProgress),
            VisualizationBuilderStatusEnum::CONFIGURING_TABLES => $this->handleConfiguringTablesStep($request, $builderProgress),
            VisualizationBuilderStatusEnum::CONFIGURING_CHARTS => $this->handleConfiguringChartsStep($request, $builderProgress),
            VisualizationBuilderStatusEnum::CONFIGURING_LAYOUT => $this->handleConfiguringLayoutStep($request, $builderProgress),
            VisualizationBuilderStatusEnum::WAITING_FOR_REVIEW => $this->handleReviewStep($request, $builderProgress),
            default => $this->redirectToRoute('admin_builder_index'),
        };
    }

    private function handleSelectingDataSourceStep(Request $request, VisualizationBuilderProgress $builderProgress): Response
    {
        $form = $this->createForm(DataSourceSelectionFormType::class, [
            'dataSource' => $builderProgress->getBuilderDataSource()?->getBaseDataSource(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $builderDataSource = $builderProgress->getBuilderDataSource() ?? new BuilderDataSource();
            $builderDataSource->setBaseDataSource($form->get('dataSource')->getData());

            $builderProgress->setBuilderDataSource($builderDataSource);
            $this->entityManager->persist($builderDataSource);
            $this->entityManager->flush();

            $this->builderService->advanceStatus($builderProgress);
            return $this->redirectToRoute('admin_builder_index', [
                'easyadmin_context' => $request->attributes->get('easyadmin_context')
            ]);
        }

        return $this->render('admin/builder/step1_data_source_selection.html.twig', [
            'form' => $form->createView(),
            'builder_progress' => $builderProgress,
        ]);
    }

    private function handleConfiguringColumnsStep(Request $request, VisualizationBuilderProgress $builderProgress): Response
    {
        $builderDataSource = $builderProgress->getBuilderDataSource();

        if (!$builderDataSource || !$builderDataSource->getBaseDataSource()) {
            return $this->redirectToRoute('admin_builder_index', [
                'easyadmin_context' => $request->attributes->get('easyadmin_context')
            ]);
        }

        $availableColumns = $this->getAvailableColumns($builderDataSource->getBaseDataSource());

        $form = $this->createForm(ColumnSelectionFormType::class, [
            'selectedColumns' => $builderDataSource->getSelectedColumns() ?? [],
        ], [
            'columns' => $availableColumns,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('prev')->isClicked()) {
                $this->builderService->regressStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }

            if ($form->isValid()) {
                $builderDataSource->setSelectedColumns($form->get('selectedColumns')->getData());
                $this->entityManager->flush();

                $this->builderService->advanceStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }
        }

        return $this->render('admin/builder/step2_column_selection.html.twig', [
            'form' => $form->createView(),
            'available_columns' => $availableColumns,
            'builder_progress' => $builderProgress,
        ]);
    }

    private function handleConfiguringTablesStep(Request $request, VisualizationBuilderProgress $builderProgress): Response
    {
        $form = $this->createForm(TableConfigurationFormType::class, [
            'tableConfigurations' => $builderProgress->getTableConfigurations(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('prev')->isClicked()) {
                $this->builderService->regressStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }

            if ($form->isValid()) {
                $this->entityManager->flush();
                $this->builderService->advanceStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }
        }

        return $this->render('admin/builder/step3_table_configuration.html.twig', [
            'form' => $form->createView(),
            'builder_progress' => $builderProgress,
        ]);
    }

    private function handleConfiguringChartsStep(Request $request, VisualizationBuilderProgress $builderProgress): Response
    {
        $form = $this->createForm(ChartConfigurationFormType::class, [
            'chartConfigurations' => $builderProgress->getChartConfigurations(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('prev')->isClicked()) {
                $this->builderService->regressStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }

            if ($form->isValid()) {
                $this->entityManager->flush();
                $this->builderService->advanceStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }
        }

        return $this->render('admin/builder/step4_chart_configuration.html.twig', [
            'form' => $form->createView(),
            'builder_progress' => $builderProgress,
        ]);
    }

    private function handleConfiguringLayoutStep(Request $request, VisualizationBuilderProgress $builderProgress): Response
    {
        $form = $this->createForm(VisualizationConfigurationFormType::class, [
            'visualizationConfiguration' => $builderProgress->getVisualizationConfiguration(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('prev')->isClicked()) {
                $this->builderService->regressStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }

            if ($form->isValid()) {
                $this->entityManager->flush();
                $this->builderService->advanceStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }
        }

        return $this->render('admin/builder/step5_layout_configuration.html.twig', [
            'form' => $form->createView(),
            'builder_progress' => $builderProgress,
        ]);
    }

    private function handleDuplicationFlow(Request $request, VisualizationBuilderProgress $builderProgress): Response
    {
        // Ensure there's an existing visualization to duplicate
        $existingVisualization = $builderProgress->getVisualizationConfiguration();

        if (!$existingVisualization) {
            return $this->redirectToRoute('admin_builder_index', ['progress' => $builderProgress->getId()]);
        }

        // Create a new VisualizationConfiguration as a copy of the existing one
        $newVisualization = new VisualizationConfiguration();
        $newVisualization->setName($existingVisualization->getName() . ' (Copy)');
        $newVisualization->setDataSource($existingVisualization->getDataSource());
        $newVisualization->setConfiguration($existingVisualization->getConfiguration());

        $this->entityManager->persist($newVisualization);

        // Update Builder Progress to link the new visualization
        $builderProgress->setVisualizationConfiguration($newVisualization);
        $builderProgress->setStatus(VisualizationBuilderStatusEnum::EDITING_EXISTING);
        $builderProgress->setLastModifiedBy($this->getUser());

        $this->entityManager->flush();

        return $this->redirectToRoute('admin_builder_index', ['progress' => $builderProgress->getId()]);
    }


    private function handleReviewStep(Request $request, VisualizationBuilderProgress $builderProgress): Response
    {
        $form = $this->createForm(ReviewAndSaveFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->get('prev')->isClicked()) {
                $this->builderService->regressStatus($builderProgress);
                return $this->redirectToRoute('admin_builder_index', [
                    'easyadmin_context' => $request->attributes->get('easyadmin_context')
                ]);
            }

            if ($form->isValid()) {
                $this->entityManager->flush();
                $this->builderService->finalizeProgress($builderProgress);
                return $this->redirectToRoute('admin');
            }
        }

        return $this->render('admin/builder/step6_review_and_save.html.twig', [
            'form' => $form->createView(),
            'builder_progress' => $builderProgress,
        ]);
    }

    private function getAvailableColumns(DataSource $dataSource): array
    {
        if ($dataSource->getType()->value === 'API') {
            $apiData = $this->dataFetcherService->fetchFromApi($dataSource);
            return array_keys(reset($apiData) ?: []);
        }

        if ($dataSource->getType()->value === 'FILE') {
            $fileData = $this->dataFetcherService->processUploadedFile($dataSource);
            return array_keys(reset($fileData) ?: []);
        }

        return [];
    }
}
