<?php
namespace SnowIO\Akeneo3Fredhopper\EventMapper;

use SnowIO\Akeneo3DataModel\Event\AttributeOptionDeletedEvent;
use SnowIO\Akeneo3DataModel\Event\AttributeOptionSavedEvent;
use SnowIO\FredhopperDataModel\AttributeOptionSet;

class AttributeOptionEventCommandMapper
{

    public static function create(FredhopperConfiguration $configuration)
    {
        return new self($configuration);
    }

    public function getSaveCommands(array $eventJson): array {
        $event = AttributeOptionSavedEvent::fromJson($eventJson);
        $mapper = $this->fredhopperConfiguration->getAttributeOptionMapper();
        /** @var AttributeOptionSet $fhAttributeOptions */
        $fhAttributeOptions = $mapper($event->getCurrentAttributeOptionData());
        return $fhAttributeOptions->mapToSaveCommands($event->getTimestamp());
    }
    
    public function getDeleteCommands(array $eventJson): array {
        $event = AttributeOptionDeletedEvent::fromJson($eventJson);
        $mapper = $this->fredhopperConfiguration->getAttributeOptionMapper();
        /** @var AttributeOptionSet $fhCategoriesData */
        $fhCategoriesData = $mapper($event->getPreviousAttributeOptionData());
        return $fhCategoriesData->mapToDeleteCommands($event->getTimestamp());
    }

    private $fredhopperConfiguration;

    private function __construct(FredhopperConfiguration $configuration)
    {
        $this->fredhopperConfiguration = $configuration;
    }
}