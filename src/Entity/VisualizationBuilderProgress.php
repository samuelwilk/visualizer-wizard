<?php

namespace App\Entity;

use App\Enum\VisualizationBuilderStatusEnum;
use App\Repository\VisualizationBuilderProgressRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VisualizationBuilderProgressRepository::class)]
class VisualizationBuilderProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(enumType: VisualizationBuilderStatusEnum::class)]
    private ?VisualizationBuilderStatusEnum $status = null;

    #[ORM\ManyToOne(inversedBy: 'visualizationBuilderProgress')]
    private ?DataSource $dataSource = null;

    #[ORM\ManyToOne(inversedBy: 'visualizationBuilderProgress')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $lastModifiedBy = null;

    #[ORM\OneToOne(inversedBy: 'visualizationBuilderProgress', cascade: ['persist', 'remove'])]
    private ?VisualizationConfiguration $visualizationConfiguration = null;

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

    public function getDataSource(): ?DataSource
    {
        return $this->dataSource;
    }

    public function setDataSource(?DataSource $dataSource): static
    {
        $this->dataSource = $dataSource;

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
}
