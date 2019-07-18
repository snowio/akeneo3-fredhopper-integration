<?php

namespace SnowIO\Akeneo3Fredhopper\Test\EventMapper;

use SnowIO\Akeneo3Fredhopper\EventMapper\AttributeEventCommandMapper;
use SnowIO\FredhopperDataModel\Command\DeleteAttributeCommand;
use SnowIO\FredhopperDataModel\InternationalizedString;
use SnowIO\FredhopperDataModel\AttributeData;
use SnowIO\FredhopperDataModel\Command\SaveAttributeCommand;
use SnowIO\FredhopperDataModel\LocalizedString;

class AttributeEventCommandMapperTest extends CommandEventMapperTest
{
    public function testSaveCommandMapper()
    {
        $eventJson = [
            'new' => [
                'code' => 'diameter',
                'type' => 'pim_catalog_identifier',
                'localizable' => false,
                'scopable' => false,
                'sort_order' => 3,
                'labels' => [
                    'en_GB' => 'Diameter',
                    'fr_FR' => 'Diamètre',
                ],
                'group' => 'default',
                '@timestamp' => 1510313694,
            ],
        ];

        $expected = SaveAttributeCommand::of(AttributeData::of('diameter', 'text', InternationalizedString::of([
            LocalizedString::of('Diameter', 'en_GB'),
            LocalizedString::of('Diamètre', 'fr_FR'),
        ])))->withTimestamp(1510313694);
        $mapper = AttributeEventCommandMapper::create($this->getFredhopperConfiguration());
        $actual = $mapper->getSaveCommands($eventJson);
        self::assertEquals($expected->toJson(), $actual['diameter']->toJson());

    }

    public function testDeleteCommandMapper()
    {
        $eventJson = [
            'code' => 'diameter',
            'type' => 'pim_catalog_identifier',
            'localizable' => false,
            'scopable' => false,
            'sort_order' => 3,
            'labels' => [
                'en_GB' => 'Diameter',
                'fr_FR' => 'Diamètre',
            ],
            'group' => 'default',
            '@timestamp' => 1510313694,
        ];

        $expected = DeleteAttributeCommand::of('diameter')->withTimestamp(1510313694);
        $mapper = AttributeEventCommandMapper::create($this->getFredhopperConfiguration());
        $actual = $mapper->getDeleteCommands($eventJson);
        self::assertEquals($expected->toJson(), $actual['diameter']->toJson());
    }
}
