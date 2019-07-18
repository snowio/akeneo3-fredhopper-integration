<?php
namespace SnowIO\Akeneo3Fredhopper\EventMapper;

use SnowIO\Akeneo3DataModel\Event\ProductSavedEvent;
use SnowIO\FredhopperDataModel\ProductDataSet;

class ProductEventCommandMapper
{
    public static function create(FredhopperConfiguration $fredhopperConfiguration): self
    {
        return new self($fredhopperConfiguration);
    }

    public function getSaveCommands(array $eventJson): array {
        $event = ProductSavedEvent::fromJson($eventJson);
        $mapper = $this->fredhopperConfiguration->getProductMapper();
        /** @var ProductDataSet $currentFhProductsData */
        $currentFhProductsData = $mapper($event->getCurrentProductData());
        /** @var ProductDataSet $previousFhProductsData */
        $previousFhProductsData = $mapper($event->getPreviousProductData());
        /** @var ProductDataSet $changedProducts */
        $changedProducts = $currentFhProductsData->diff($previousFhProductsData);
        return $changedProducts->mapToSaveCommands($event->getTimestamp());
    }
    
    public function getDeleteCommands(array $eventJson): array {
        $event = ProductSavedEvent::fromJson($eventJson);
        $mapper = $this->fredhopperConfiguration->getProductMapper();
        /** @var ProductDataSet $currentFhProductsData */
        $currentFhProductsData = $mapper($event->getCurrentProductData());
        /** @var ProductDataSet $previousFhProductsData */
        $previousFhProductsData = $mapper($event->getPreviousProductData());
        /** @var ProductDataSet $removedProducts */
        $removedProducts = $previousFhProductsData->diffByKey($currentFhProductsData);
        return $removedProducts->mapToDeleteCommands($event->getTimestamp());
    }

    private $fredhopperConfiguration;

    private function __construct(FredhopperConfiguration $configuration)
    {
        $this->fredhopperConfiguration = $configuration;
    }


}