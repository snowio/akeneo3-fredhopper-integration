<?php

namespace SnowIO\Akeneo3Fredhopper\Test\EventMapper;

use SnowIO\Akeneo3Fredhopper\EventMapper\AttributeOptionEventCommandMapper;
use SnowIO\FredhopperDataModel\AttributeOption;
use SnowIO\FredhopperDataModel\Command\DeleteAttributeOptionCommand;
use SnowIO\FredhopperDataModel\Command\SaveAttributeOptionCommand;
use SnowIO\FredhopperDataModel\LocalizedString;

class AttributeOptionEventCommandMapperTest extends CommandEventMapperTest
{

    public function testSaveCommandMapper()
    {
        $eventJson = [
            'new' => [
                '@timestamp' => 1510313694,
                'attribute' => 'size',
                'code' => 'large',
                'labels' => [
                    'en_GB' => 'Large'
                ],
            ],
        ];

        $expected = SaveAttributeOptionCommand::of(
            AttributeOption::of('size', 'large')
                ->withDisplayValue(LocalizedString::of('Large', 'en_GB'))
        )->withTimestamp(1510313694);
        $mapper = AttributeOptionEventCommandMapper::create($this->getFredhopperConfiguration());
        $actual = $mapper->getSaveCommands($eventJson);
        self::assertEquals($expected->toJson(), $actual['size-large']->toJson());
    }

    public function testDeleteCommandMapper()
    {
        $eventJson = [
            '@timestamp' => 1510313694,
            'attribute' => 'size',
            'code' => 'large',
            'labels' => [
                'en_GB' => 'Large'
            ],
        ];

        $expected = DeleteAttributeOptionCommand::of('size', 'large')
            ->withTimestamp(1510313694);
        $mapper = AttributeOptionEventCommandMapper::create($this->getFredhopperConfiguration());
        $actual = $mapper->getDeleteCommands($eventJson);
        self::assertEquals($expected->toJson(), $actual['size-large']->toJson());

    }
}