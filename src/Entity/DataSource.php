<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use App\Enum\ApiResponseContentTypeEnum;
use App\Enum\DataSourceTypeEnum;
use App\Enum\IngestionModeEnum;
use App\Repository\DataSourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DataSourceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class DataSource
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(enumType: DataSourceTypeEnum::class)]
    private ?DataSourceTypeEnum $type = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(enumType: IngestionModeEnum::class)]
    private ?IngestionModeEnum $ingestionMode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $apiEndpoint = null;

    #[ORM\Column(nullable: true)]
    private ?array $apiCredentials = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filePath = null;

    #[ORM\Column(nullable: true)]
    private ?array $columns = null;

    #[ORM\Column(nullable: true)]
    private ?array $rowFilters = null;

    /**
     * @var Collection<int, PreProcessedData>
     */
    #[ORM\OneToMany(targetEntity: PreProcessedData::class, mappedBy: 'dataSource')]
    private Collection $preProcessedData;

    /**
     * @var Collection<int, VisualizationConfiguration>
     */
    #[ORM\OneToMany(targetEntity: VisualizationConfiguration::class, mappedBy: 'dataSource')]
    private Collection $visualizationConfigurations;

    #[ORM\Column(enumType: ApiResponseContentTypeEnum::class)]
    private ?ApiResponseContentTypeEnum $apiResponseContentType = null;

    /**
     * @var Collection<int, BuilderDataSource>
     */
    #[ORM\OneToMany(targetEntity: BuilderDataSource::class, mappedBy: 'dataSource')]
    private Collection $builderDataSources;

    /**
     * @var Collection<int, VisualizationBuilderProgress>
     */
    #[ORM\OneToMany(targetEntity: VisualizationBuilderProgress::class, mappedBy: 'dataSource')]
    private Collection $visualizationBuilderProgress;

    public function __construct()
    {
        $this->preProcessedData = new ArrayCollection();
        $this->visualizationConfigurations = new ArrayCollection();
        $this->builderDataSources = new ArrayCollection();
        $this->visualizationBuilderProgress = new ArrayCollection();
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

    public function getType(): ?DataSourceTypeEnum
    {
        return $this->type;
    }

    public function setType(DataSourceTypeEnum $type): static
    {
        $this->type = $type;

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

    public function getIngestionMode(): ?IngestionModeEnum
    {
        return $this->ingestionMode;
    }

    public function setIngestionMode(IngestionModeEnum $ingestionMode): static
    {
        $this->ingestionMode = $ingestionMode;

        return $this;
    }

    public function getApiEndpoint(): ?string
    {
        return $this->apiEndpoint;
    }

    public function setApiEndpoint(?string $apiEndpoint): static
    {
        $this->apiEndpoint = $apiEndpoint;

        return $this;
    }

    public function getApiCredentials(): ?array
    {
        return $this->apiCredentials;
    }

    public function setApiCredentials(?array $apiCredentials): static
    {
        $this->apiCredentials = $apiCredentials;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getColumns(): ?array
    {
        return $this->columns;
    }

    public function setColumns(?array $columns): static
    {
        $this->columns = $columns;

        return $this;
    }

    public function getRowFilters(): ?array
    {
        return $this->rowFilters;
    }

    public function setRowFilters(?array $rowFilters): static
    {
        $this->rowFilters = $rowFilters;

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
            $preProcessedData->setDataSource($this);
        }

        return $this;
    }

    public function removePreProcessedData(PreProcessedData $preProcessedData): static
    {
        if ($this->preProcessedData->removeElement($preProcessedData)) {
            // set the owning side to null (unless already changed)
            if ($preProcessedData->getDataSource() === $this) {
                $preProcessedData->setDataSource(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VisualizationConfiguration>
     */
    public function getVisualizationConfigurations(): Collection
    {
        return $this->visualizationConfigurations;
    }

    public function addVisualizationConfiguration(VisualizationConfiguration $visualizationConfiguration): static
    {
        if (!$this->visualizationConfigurations->contains($visualizationConfiguration)) {
            $this->visualizationConfigurations->add($visualizationConfiguration);
            $visualizationConfiguration->setDataSource($this);
        }

        return $this;
    }

    public function removeVisualizationConfiguration(VisualizationConfiguration $visualizationConfiguration): static
    {
        if ($this->visualizationConfigurations->removeElement($visualizationConfiguration)) {
            // set the owning side to null (unless already changed)
            if ($visualizationConfiguration->getDataSource() === $this) {
                $visualizationConfiguration->setDataSource(null);
            }
        }

        return $this;
    }

    public function getApiResponseContentType(): ?ApiResponseContentTypeEnum
    {
        return $this->apiResponseContentType;
    }

    public function setApiResponseContentType(ApiResponseContentTypeEnum $apiResponseContentType): static
    {
        $this->apiResponseContentType = $apiResponseContentType;

        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Data Source';
    }

    /**
     * @return Collection<int, BuilderDataSource>
     */
    public function getBuilderDataSources(): Collection
    {
        return $this->builderDataSources;
    }

    public function addBuilderDataSource(BuilderDataSource $builderDataSource): static
    {
        if (!$this->builderDataSources->contains($builderDataSource)) {
            $this->builderDataSources->add($builderDataSource);
            $builderDataSource->setDataSource($this);
        }

        return $this;
    }

    public function removeBuilderDataSource(BuilderDataSource $builderDataSource): static
    {
        if ($this->builderDataSources->removeElement($builderDataSource)) {
            // set the owning side to null (unless already changed)
            if ($builderDataSource->getDataSource() === $this) {
                $builderDataSource->setDataSource(null);
            }
        }

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
            $visualizationBuilderProgress->setDataSource($this);
        }

        return $this;
    }

    public function removeVisualizationBuilderProgress(VisualizationBuilderProgress $visualizationBuilderProgress): static
    {
        if ($this->visualizationBuilderProgress->removeElement($visualizationBuilderProgress)) {
            // set the owning side to null (unless already changed)
            if ($visualizationBuilderProgress->getDataSource() === $this) {
                $visualizationBuilderProgress->setDataSource(null);
            }
        }

        return $this;
    }
}
