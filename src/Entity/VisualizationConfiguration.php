<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\VisualizationConfigurationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisualizationConfigurationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class VisualizationConfiguration
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToOne(mappedBy: 'visualizationConfiguration', cascade: ['persist', 'remove'])]
    private ?VisualizationBuilderProgress $visualizationBuilderProgress = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'visualizationConfigurations')]
    private ?DataSource $dataSource = null;

    #[ORM\Column(nullable: true)]
    private ?array $configuration = null;

    /**
     * @var Collection<int, ChartConfiguration>
     */
    #[ORM\OneToMany(targetEntity: ChartConfiguration::class, mappedBy: 'visualizationConfiguration')]
    private Collection $chartConfigurations;

    /**
     * @var Collection<int, TableConfiguration>
     */
    #[ORM\OneToMany(targetEntity: TableConfiguration::class, mappedBy: 'visualizationConfiguration')]
    private Collection $tableConfigurations;

    /**
     * @var Collection<int, PreProcessedData>
     */
    #[ORM\OneToMany(targetEntity: PreProcessedData::class, mappedBy: 'visualizationConfiguration')]
    private Collection $preProcessedData;

    public function __construct()
    {
        $this->chartConfigurations = new ArrayCollection();
        $this->preProcessedData = new ArrayCollection();
        $this->tableConfigurations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDataSource(): ?DataSource
    {
        return $this->dataSource;
    }

    public function setDataSource(?DataSource $dataSource): static
    {
        $this->dataSource = $dataSource;

        return $this;
    }

    public function getConfiguration(): ?array
    {
        return $this->configuration;
    }

    public function setConfiguration(?array $configuration): static
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * @return Collection<int, ChartConfiguration>
     */
    public function getChartConfigurations(): Collection
    {
        return $this->chartConfigurations;
    }

    public function addChartConfiguration(ChartConfiguration $chartConfiguration): static
    {
        if (!$this->chartConfigurations->contains($chartConfiguration)) {
            $this->chartConfigurations->add($chartConfiguration);
            $chartConfiguration->setVisualizationConfiguration($this);
        }

        return $this;
    }

    public function removeChartConfiguration(ChartConfiguration $chartConfiguration): static
    {
        if ($this->chartConfigurations->removeElement($chartConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($chartConfiguration->getVisualizationConfiguration() === $this) {
                $chartConfiguration->setVisualizationConfiguration(null);
            }
        }

        return $this;
    }

    public function getVisualizationBuilderProgress(): ?VisualizationBuilderProgress
    {
        return $this->visualizationBuilderProgress;
    }

    public function setVisualizationBuilderProgress(?VisualizationBuilderProgress $visualizationBuilderProgress): static
    {
        // unset the owning side of the relation if necessary
        if ($visualizationBuilderProgress === null && $this->visualizationBuilderProgress !== null) {
            $this->visualizationBuilderProgress->setVisualizationConfiguration(null);
        }

        // set the owning side of the relation if necessary
        if ($visualizationBuilderProgress !== null && $visualizationBuilderProgress->getVisualizationConfiguration() !== $this) {
            $visualizationBuilderProgress->setVisualizationConfiguration($this);
        }

        $this->visualizationBuilderProgress = $visualizationBuilderProgress;

        return $this;
    }

    /**
     * @return Collection<int, PreProcessedData>
     */
    public function getPreProcessedData(): Collection
    {
        return $this->preProcessedData;
    }

    public function addPreProcessedData(PreProcessedData $preProcessedData): static
    {
        if (!$this->preProcessedData->contains($preProcessedData)) {
            $this->preProcessedData->add($preProcessedData);
            $preProcessedData->setVisualizationConfiguration($this);
        }

        return $this;
    }

    public function removePreProcessedData(PreProcessedData $preProcessedData): static
    {
        if ($this->preProcessedData->removeElement($preProcessedData)) {
            // set the owning side to null (unless already changed)
            if ($preProcessedData->getVisualizationConfiguration() === $this) {
                $preProcessedData->setVisualizationConfiguration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TableConfiguration>
     */
    public function getTableConfigurations(): Collection
    {
        return $this->tableConfigurations;
    }

    public function addTableConfiguration(TableConfiguration $tableConfiguration): static
    {
        if (!$this->tableConfigurations->contains($tableConfiguration)) {
            $this->tableConfigurations->add($tableConfiguration);
            $tableConfiguration->setVisualizationConfiguration($this);
        }

        return $this;
    }

    public function removeTableConfiguration(TableConfiguration $tableConfiguration): static
    {
        if ($this->tableConfigurations->removeElement($tableConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($tableConfiguration->getVisualizationConfiguration() === $this) {
                $tableConfiguration->setVisualizationConfiguration(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
