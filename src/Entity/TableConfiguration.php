<?php

namespace App\Entity;

use App\Repository\TableConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TableConfigurationRepository::class)]
class TableConfiguration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?array $selectedColumns = null;

    #[ORM\Column(nullable: true)]
    private ?array $sorting = null;

    #[ORM\Column(nullable: true)]
    private ?array $filters = null;

    #[ORM\ManyToOne(inversedBy: 'tableConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VisualizationConfiguration $visualizationConfiguration = null;

    #[ORM\ManyToOne(inversedBy: 'tableConfigurations')]
    private ?VisualizationBuilderProgress $visualizationBuilderProgress = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSelectedColumns(): ?array
    {
        return $this->selectedColumns;
    }

    public function setSelectedColumns(?array $selectedColumns): static
    {
        $this->selectedColumns = $selectedColumns;

        return $this;
    }

    public function getSorting(): ?array
    {
        return $this->sorting;
    }

    public function setSorting(?array $sorting): static
    {
        $this->sorting = $sorting;

        return $this;
    }

    public function getFilters(): ?array
    {
        return $this->filters;
    }

    public function setFilters(?array $filters): static
    {
        $this->filters = $filters;

        return $this;
    }

    public function getVisualizationConfiguration(): ?VisualizationConfiguration
    {
        return $this->visualizationConfiguration;
    }

    public function setVisualizationConfiguration(?VisualizationConfiguration $visualizationConfiguration): static
    {
        $this->visualizationConfiguration = $visualizationConfiguration;

        return $this;
    }

    public function getVisualizationBuilderProgress(): ?VisualizationBuilderProgress
    {
        return $this->visualizationBuilderProgress;
    }

    public function setVisualizationBuilderProgress(?VisualizationBuilderProgress $visualizationBuilderProgress): static
    {
        $this->visualizationBuilderProgress = $visualizationBuilderProgress;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getTitle() .  "($this->id)";
    }
}
