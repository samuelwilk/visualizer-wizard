<?php

namespace App\Service;

use App\Entity\PreProcessedData;
use App\Entity\VisualizationBuilderProgress;
use App\Enum\VisualizationBuilderStatusEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class VisualizationBuilderService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DataProcessingService $dataProcessingService,
        private readonly Security $security
    ) {}

    public function getOrCreateBuilderProgress(UserInterface $user): VisualizationBuilderProgress
    {
        $builderProgress = $this->entityManager->getRepository(VisualizationBuilderProgress::class)
            ->findOneBy([
                'lastModifiedBy' => $user,
                'status' => [
                    VisualizationBuilderStatusEnum::SELECTING_DATA_SOURCE,
                    VisualizationBuilderStatusEnum::CONFIGURING_COLUMNS,
                    VisualizationBuilderStatusEnum::CONFIGURING_TABLES,
                    VisualizationBuilderStatusEnum::CONFIGURING_CHARTS,
                    VisualizationBuilderStatusEnum::CONFIGURING_LAYOUT,
                    VisualizationBuilderStatusEnum::WAITING_FOR_REVIEW,
                ],
            ]);

        if (!$builderProgress) {
            $builderProgress = new VisualizationBuilderProgress();
            $builderProgress->setLastModifiedBy($user);
            $builderProgress->setStatus(VisualizationBuilderStatusEnum::SELECTING_DATA_SOURCE); // Start at the first step

            $this->entityManager->persist($builderProgress);
            $this->entityManager->flush();
        }

        return $builderProgress;
    }

    public function advanceStatus(VisualizationBuilderProgress $builderProgress): void
    {
        $currentStatus = $builderProgress->getStatus();

        $nextStatus = match ($currentStatus) {
            VisualizationBuilderStatusEnum::SELECTING_DATA_SOURCE => VisualizationBuilderStatusEnum::CONFIGURING_COLUMNS,
            VisualizationBuilderStatusEnum::CONFIGURING_COLUMNS => VisualizationBuilderStatusEnum::CONFIGURING_TABLES,
            VisualizationBuilderStatusEnum::CONFIGURING_TABLES => VisualizationBuilderStatusEnum::CONFIGURING_CHARTS,
            VisualizationBuilderStatusEnum::CONFIGURING_CHARTS => VisualizationBuilderStatusEnum::CONFIGURING_LAYOUT,
            VisualizationBuilderStatusEnum::CONFIGURING_LAYOUT => VisualizationBuilderStatusEnum::WAITING_FOR_REVIEW,
            default => VisualizationBuilderStatusEnum::FINALIZED,
        };

        $builderProgress->setStatus($nextStatus);
        $this->entityManager->flush();
    }

    /**
     * Moves the builder progress back to the previous status in the workflow.
     */
    public function regressStatus(VisualizationBuilderProgress $builderProgress): void
    {
        $previousStatus = match ($builderProgress->getStatus()) {
            VisualizationBuilderStatusEnum::CONFIGURING_COLUMNS => VisualizationBuilderStatusEnum::SELECTING_DATA_SOURCE,
            VisualizationBuilderStatusEnum::CONFIGURING_TABLES => VisualizationBuilderStatusEnum::CONFIGURING_COLUMNS,
            VisualizationBuilderStatusEnum::CONFIGURING_CHARTS => VisualizationBuilderStatusEnum::CONFIGURING_TABLES,
            VisualizationBuilderStatusEnum::CONFIGURING_LAYOUT => VisualizationBuilderStatusEnum::CONFIGURING_CHARTS,
            VisualizationBuilderStatusEnum::WAITING_FOR_REVIEW => VisualizationBuilderStatusEnum::CONFIGURING_LAYOUT,
            default => VisualizationBuilderStatusEnum::SELECTING_DATA_SOURCE,
        };

        $builderProgress->setStatus($previousStatus);
        $builderProgress->setLastModifiedBy($this->security->getUser());

        $this->entityManager->flush();
    }

    /**
     * Finalizes the builder progress, processes the data, and marks the visualization as complete.
     */
    public function finalizeProgress(VisualizationBuilderProgress $builderProgress): void
    {
        // Ensure there is an associated data source
        $builderDataSource = $builderProgress->getBuilderDataSource();
        if (!$builderDataSource || !$builderDataSource->getBaseDataSource()) {
            throw new \LogicException('Cannot finalize: No data source linked to this visualization.');
        }

        // Process selected columns before storing
        $processedData = $this->dataProcessingService->processData($builderDataSource);

        // Create a new PreProcessedData entry
        $preProcessedData = new PreProcessedData();
        $preProcessedData->setDataSource($builderDataSource->getBaseDataSource());
        $preProcessedData->setVisualizationConfiguration($builderProgress->getVisualizationConfiguration());
        $preProcessedData->setScheduledAt(new \DateTimeImmutable());
        $preProcessedData->setIsActive(true);
        $preProcessedData->setData($processedData);

        // Persist all changes
        $this->entityManager->persist($preProcessedData);
        $builderProgress->setStatus(VisualizationBuilderStatusEnum::FINALIZED);
        $builderProgress->setLastModifiedBy($this->security->getUser());

        $this->entityManager->flush();
    }
}
