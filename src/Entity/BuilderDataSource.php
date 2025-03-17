<?php

namespace App\Entity;

use App\Repository\BuilderDataSourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuilderDataSourceRepository::class)]
class BuilderDataSource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'builderDataSources')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DataSource $baseDataSource = null;

    #[ORM\Column(nullable: true)]
    private ?array $selectedColumns = null;

    /**
     * @var Collection<int, VisualizationBuilderProgress>
     */
    #[ORM\OneToMany(targetEntity: VisualizationBuilderProgress::class, mappedBy: 'builderDataSource')]
    private Collection $visualizationBuilderProgress;

    public function __construct()
    {
        $this->visualizationBuilderProgress = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBaseDataSource(): ?DataSource
    {
        return $this->baseDataSource;
    }

    public function setBaseDataSource(?DataSource $baseDataSource): static
    {
        $this->baseDataSource = $baseDataSource;

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

    /**
     * @return Collection<int, VisualizationBuilderProgress>
     */
    public function getVisualizationBuilderProgress(): Collection
    {
        return $this->visualizationBuilderProgress;
    }

    public function addVisualizationBuilderProgress(VisualizationBuilderProgress $visualizationBuilderProgress): static
    {
        if (!$this->visualizationBuilderProgress->contains($visualizationBuilderProgress)) {
            $this->visualizationBuilderProgress->add($visualizationBuilderProgress);
            $visualizationBuilderProgress->setBuilderDataSource($this);
        }

        return $this;
    }

    public function removeVisualizationBuilderProgress(VisualizationBuilderProgress $visualizationBuilderProgress): static
    {
        if ($this->visualizationBuilderProgress->removeElement($visualizationBuilderProgress)) {
            // set the owning side to null (unless already changed)
            if ($visualizationBuilderProgress->getBuilderDataSource() === $this) {
                $visualizationBuilderProgress->setBuilderDataSource(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getBaseDataSource()->getName() ?? '';
    }
}
