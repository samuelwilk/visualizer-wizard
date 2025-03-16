<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\ChartTypeEnum;
use App\Repository\ChartConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChartConfigurationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class ChartConfiguration
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: ChartTypeEnum::class)]
    private ?ChartTypeEnum $chartType = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(nullable: true)]
    private ?array $labels = null;

    #[ORM\Column(nullable: true)]
    private ?array $series = null;

    #[ORM\ManyToOne(inversedBy: 'chartConfigurations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?VisualizationConfiguration $visualizationConfiguration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChartType(): ?ChartTypeEnum
    {
        return $this->chartType;
    }

    public function setChartType(ChartTypeEnum $chartType): static
    {
        $this->chartType = $chartType;

        return $this;
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

    public function getLabels(): ?array
    {
        return $this->labels;
    }

    public function setLabels(?array $labels): static
    {
        $this->labels = $labels;

        return $this;
    }

    public function getSeries(): ?array
    {
        return $this->series;
    }

    public function setSeries(?array $series): static
    {
        $this->series = $series;

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
}
