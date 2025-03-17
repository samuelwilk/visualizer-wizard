<?php
namespace App\Admin\Controller;

use App\Entity\ChartConfiguration;
use App\Entity\DataSource;
use App\Entity\PreProcessedData;
use App\Entity\TableConfiguration;
use App\Entity\VisualizationBuilderProgress;
use App\Entity\VisualizationConfiguration;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Admin Panel');
    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linkToDashboard('Home', 'fa fa-home');

        yield MenuItem::section('Builder');
        yield MenuItem::linkToRoute('Visualization Builder', 'fa fa-wand-magic-sparkles', 'admin_builder_index');
        yield MenuItem::linkToCrud('Builder', 'fa fa-wand-magic-sparkles', VisualizationBuilderProgress::class);

        yield MenuItem::section('Models');
        yield MenuItem::linkToCrud('Data Source', 'fa fa-database', DataSource::class);
        yield MenuItem::linkToCrud('Visualization', 'fa fa-palette', VisualizationConfiguration::class);
        yield MenuItem::linkToCrud('Chart', 'fa fa-chart-simple', ChartConfiguration::class);
        yield MenuItem::linkToCrud('Table', 'fa fa-table', TableConfiguration::class);
        yield MenuItem::linkToCrud('Pre-Processed Data', 'fa fa-square-binary', PreProcessedData::class);
    }
}
