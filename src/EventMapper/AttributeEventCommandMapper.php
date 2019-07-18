<?php
namespace SnowIO\Akeneo3Fredhopper\EventMapper;

use SnowIO\Akeneo3DataModel\Event\AttributeDeletedEvent;
use SnowIO\Akeneo3DataModel\Event\AttributeSavedEvent;
use SnowIO\FredhopperDataModel\AttributeDataSet;

class AttributeEventCommandMapper
{
    public static function create(FredhopperConfiguration $configuration)
    {
        return new self($configuration);
    }

    public function getSaveCommands(array $eventJson): array {
        $event = AttributeSavedEvent::fromJson($eventJson);
        $mapper = $this->fredhopperConfiguration->getAttributeMapper();
        /** @var AttributeDataSet $fhAttributesData */
        $fhAttributesData = $mapper($event->getCurrentAttributeData());
        return $fhAttributesData->mapToSaveCommands($event->getTimestamp());
    }

    public function getDeleteCommands(array $eventJson): array {
        $event = AttributeDeletedEvent::fromJson($eventJson);
        $mapper = $this->fredhopperConfiguration->getAttributeMapper();
        /** @var AttributeDataSet $fhCategoriesData */
        $fhCategoriesData = $mapper($event->getPreviousAttributeData());
        return $fhCategoriesData->mapToDeleteCommands($event->getTimestamp());
    }

    private $fredhopperConfiguration;


    private function __construct(FredhopperConfiguration $configuration)
    {
        $this->fredhopperConfiguration = $configuration;
    }
}