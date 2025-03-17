<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\VisualizationBuilderStatusEnum;
use App\Repository\VisualizationBuilderProgressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisualizationBuilderProgressRepository::class)]
#[ORM\HasLifecycleCallbacks]
class VisualizationBuilderProgress
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: VisualizationBuilderStatusEnum::class)]
    private ?VisualizationBuilderStatusEnum $status = null;

    #[ORM\ManyToOne(inversedBy: 'visualizationBuilderProgress')]
    private ?BuilderDataSource $builderDataSource = null;

    #[ORM\ManyToOne(inversedBy: 'visualizationBuilderProgress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $lastModifiedBy = null;

    #[ORM\OneToOne(inversedBy: 'visualizationBuilderProgress', cascade: ['persist', 'remove'])]
    private ?VisualizationConfiguration $visualizationConfiguration = null;

    /**
     * @var Collection<int, ChartConfiguration>
     */
    #[ORM\OneToMany(targetEntity: ChartConfiguration::class, mappedBy: 'visualizationBuilderProgress')]
    private Collection $chartConfigurations;

    /**
     * @var Collection<int, TableConfiguration>
     */
    #[ORM\OneToMany(targetEntity: TableConfiguration::class, mappedBy: 'visualizationBuilderProgress')]
    private Collection $tableConfigurations;

    public function __construct()
    {
        $this->chartConfigurations = new ArrayCollection();
        $this->tableConfigurations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?VisualizationBuilderStatusEnum
    {
        return $this->status;
    }

    public function setStatus(VisualizationBuilderStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLastModifiedBy(): ?User
    {
        return $this->lastModifiedBy;
    }

    public function setLastModifiedBy(?User $lastModifiedBy): static
    {
        $this->lastModifiedBy = $lastModifiedBy;

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

    public function getBuilderDataSource(): ?BuilderDataSource
    {
        return $this->builderDataSource;
    }

    public function setBuilderDataSource(?BuilderDataSource $builderDataSource): static
    {
        $this->builderDataSource = $builderDataSource;

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
            $chartConfiguration->setVisualizationBuilderProgress($this);
        }

        return $this;
    }

    public function removeChartConfiguration(ChartConfiguration $chartConfiguration): static
    {
        if ($this->chartConfigurations->removeElement($chartConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($chartConfiguration->getVisualizationBuilderProgress() === $this) {
                $chartConfiguration->setVisualizationBuilderProgress(null);
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
            $tableConfiguration->setVisualizationBuilderProgress($this);
        }

        return $this;
    }

    public function removeTableConfiguration(TableConfiguration $tableConfiguration): static
    {
        if ($this->tableConfigurations->removeElement($tableConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($tableConfiguration->getVisualizationBuilderProgress() === $this) {
                $tableConfiguration->setVisualizationBuilderProgress(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
