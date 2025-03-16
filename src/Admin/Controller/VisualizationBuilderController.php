<?php
namespace App\Admin\Controller;

use App\Entity\DataSource;
use App\Entity\VisualizationConfiguration;
use App\Form\Builder\DataSourceSelectionFormType;
use App\Form\Builder\ColumnSelectionFormType;
use App\Form\Builder\ChartConfigurationFormType;
use App\Form\Builder\ReviewAndSaveFormType;
use App\Form\Builder\VisualizationConfigurationFormType;
use App\Service\DataFetcherService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/builder', name: 'admin_builder')]
class VisualizationBuilderController extends AbstractDashboardController
{
    public function __construct(private readonly DataFetcherService $dataFetcher, private readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('', name: '_index')]
    public function wizard(Request $request, SessionInterface $session): Response
    {
        $step = $request->query->getInt('step', 1);

        // Initialize wizard data
        if (!$session->has('wizard_data')) {
            $session->set('wizard_data', []);
        }
        $wizardData = $session->get('wizard_data');

        return match ($step) {
            1 => $this->handleDataSourceStep($request, $session, $wizardData),
            2 => $this->handleColumnSelectionStep($request, $session, $wizardData),
            3 => $this->handleChartConfigurationStep($request, $session, $wizardData),
            4 => $this->handleVisualizationConfigurationStep($request, $session, $wizardData),
            5 => $this->handleReviewStep($request, $session, $wizardData),
            default => $this->redirectToRoute('admin_wizard_index', ['step' => 1]),
        };
    }

    private function handleDataSourceStep(Request $request, SessionInterface $session, array &$wizardData): Response
    {
        $form = $this->createForm(DataSourceSelectionFormType::class, [
            'dataSource' => isset($wizardData['dataSource']) && $wizardData['dataSource'] instanceof DataSource
                ? $wizardData['dataSource']
                : null,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Store the actual DataSource entity, not just an array
            $wizardData['dataSource'] = $form->get('dataSource')->getData();
            $session->set('wizard_data', $wizardData);

            return $this->redirectToRoute('admin_wizard_index', ['step' => 2]);
        }

        return $this->render('admin/wizard/step1.html.twig', [
            'form' => $form->createView(),
            'contentTitle' => 'Step 1: Select Data Source',
            'wizard_progress' => $this->getWizardProgress(1),
        ]);
    }

    private function handleColumnSelectionStep(Request $request, SessionInterface $session, array &$wizardData): Response
    {
        if (!isset($wizardData['dataSource'])) {
            return $this->redirectToRoute('admin_wizard_index', ['step' => 1]);
        }

        $available_columns = $this->getAvailableColumns($wizardData['dataSource']);

        $form = $this->createForm(ColumnSelectionFormType::class, $wizardData, [
            'columns' => $available_columns,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Flatten array to store only selected column names
            $wizardData['columns'] = array_map('strval', $form->get('selectedColumns')->getData());
            $session->set('wizard_data', $wizardData);

            return $this->redirectToRoute('admin_wizard_index', ['step' => 3]);
        }

        return $this->render('admin/wizard/step2.html.twig', [
            'form' => $form->createView(),
            'contentTitle' => 'Step 2: Select Columns & Filters',
            'available_columns' => $available_columns,
            'wizard_progress' => $this->getWizardProgress(2),
        ]);
    }


    private function handleChartConfigurationStep(Request $request, SessionInterface $session, array &$wizardData): Response
    {
        if (!isset($wizardData['columns'])) {
            return $this->redirectToRoute('admin_wizard_index', ['step' => 2]);
        }

        $form = $this->createForm(ChartConfigurationFormType::class, $wizardData, [
            'columns' => $wizardData['columns'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wizardData['charts'] = [$form->getData()];
            $session->set('wizard_data', $wizardData);
            return $this->redirectToRoute('admin_wizard_index', ['step' => 4]);
        }

        return $this->render('admin/wizard/step3.html.twig', [
            'form' => $form->createView(),
            'contentTitle' => 'Step 3: Configure Charts',
            'wizard_progress' => $this->getWizardProgress(3),
        ]);
    }


    private function handleVisualizationConfigurationStep(Request $request, SessionInterface $session, array &$wizardData): Response
    {
        if (!isset($wizardData['charts'])) {
            return $this->redirectToRoute('admin_wizard_index', ['step' => 3]);
        }

        $form = $this->createForm(VisualizationConfigurationFormType::class, $wizardData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $wizardData['visualization'] = $form->getData();
            $session->set('wizard_data', $wizardData);
            return $this->redirectToRoute('admin_wizard_index', ['step' => 5]);
        }

        return $this->render('admin/wizard/step4.html.twig', [
            'form' => $form->createView(),
            'contentTitle' => 'Step 4: Define Visualization Layout',
            'wizard_progress' => $this->getWizardProgress(4),
        ]);
    }


    private function handleReviewStep(Request $request, SessionInterface $session, array &$wizardData): Response
    {
        if (!isset($wizardData['visualization'])) {
            return $this->redirectToRoute('admin_wizard_index', ['step' => 4]);
        }

        // Create the final save form
        $form = $this->createForm(ReviewAndSaveFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Create and persist VisualizationConfiguration entity
            $visualization = new VisualizationConfiguration();
            $visualization->setName($wizardData['visualization']['title']);
            $visualization->setDataSource($wizardData['dataSource']);
            $visualization->setConfiguration([
                'tabs' => $wizardData['visualization']['tabs'],
                'sections' => $wizardData['visualization']['sections'],
            ]);

            // Persist and flush entity
            $this->entityManager->persist($visualization);
            $this->entityManager->flush();

            // Clear wizard data
            $session->remove('wizard_data');

            $this->addFlash('success', 'Visualization successfully saved!');
            return $this->redirectToRoute('admin'); // Redirect to dashboard
        }

        return $this->render('admin/wizard/step5.html.twig', [
            'wizardData' => $wizardData,
            'contentTitle' => 'Step 5: Review & Save',
            'wizard_progress' => $this->getWizardProgress(5),
            'form' => $form->createView(),
        ]);
    }

    private function getWizardProgress(int $step): int
    {
        $totalSteps = 5; // Total number of steps in the wizard
        return (int) (($step / $totalSteps) * 100);
    }

    private function getAvailableColumns(DataSource $dataSource): array
    {
        if ($dataSource->getType()->value === 'API') {
            // Assume the API returns an array of objects (or key-value pairs)
            $apiData = $this->dataFetcher->fetchFromApi($dataSource);
            return array_keys(reset($apiData) ?: []);
        }

        if ($dataSource->getType()->value === 'FILE') {
            // Assume files are parsed into arrays
            $fileData = $this->dataFetcher->processUploadedFile($dataSource);
            return array_keys(reset($fileData) ?: []);
        }

        return [];
    }
}
