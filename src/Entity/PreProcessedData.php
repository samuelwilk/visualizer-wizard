<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Repository\PreProcessedDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PreProcessedDataRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PreProcessedData
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'preProcessedData')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DataSource $dataSource = null;

    #[ORM\Column(nullable: true)]
    private ?array $data = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $scheduledAt = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\ManyToOne(inversedBy: 'preProcessedData')]
    private ?VisualizationConfiguration $visualizationConfiguration = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;

        return $this;
    }

    public function getScheduledAt(): ?\DateTimeImmutable
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeImmutable $scheduledAt): static
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

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

    public function __toString(): string
    {
        return (string) $this->getId();
    }
}
