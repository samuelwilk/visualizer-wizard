<?php

namespace App\Entity;

use App\Repository\BuilderDataSourceRepository;
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
    private ?DataSource $dataSource = null;

    #[ORM\Column(nullable: true)]
    private ?array $selectedColumns = null;

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

    public function getSelectedColumns(): ?array
    {
        return $this->selectedColumns;
    }

    public function setSelectedColumns(?array $selectedColumns): static
    {
        $this->selectedColumns = $selectedColumns;

        return $this;
    }
}
