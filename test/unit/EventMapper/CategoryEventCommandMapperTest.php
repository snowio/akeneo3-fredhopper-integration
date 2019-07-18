<?php

namespace SnowIO\Akeneo3Fredhopper\Test\EventMapper;

use SnowIO\FredhopperDataModel\Command\DeleteCategoryCommand;
use SnowIO\FredhopperDataModel\InternationalizedString;
use SnowIO\Akeneo3Fredhopper\EventMapper\CategoryEventCommandMapper;
use SnowIO\FredhopperDataModel\CategoryData;
use SnowIO\FredhopperDataModel\Command\SaveCategoryCommand;
use SnowIO\FredhopperDataModel\LocalizedString;

class CategoryEventCommandMapperTest extends CommandEventMapperTest
{

    public function testSaveCommandMapper()
    {
        $eventJson = [
            'new' => [
                'code' => 'menstrousers',
                '@timestamp' => 1510313694,
                'labels' => [
                    'en_GB' => 'Men\'s Trousers',
                ],
                'parent' => 'menswear',
                'path' => ['clothes', 'mens_wear', 'mens_trousers']
            ],
        ];

        $expected = SaveCategoryCommand::of(CategoryData::of('menstrousers', InternationalizedString::of([
            LocalizedString::of('Men\'s Trousers', 'en_GB'),
        ]))->withParent('menswear'))->withTimestamp(1510313694);
        $mapper = CategoryEventCommandMapper::create($this->getFredhopperConfiguration());
        $actual = $mapper->getSaveCommands($eventJson);
        self::assertEquals($expected->toJson(), $actual['menstrousers']->toJson());

    }

    public function testDeleteCommandMapper()
    {
        $eventJson = [
            '@timestamp' => 1510313694,
            'categoryCode' => 'menstrousers',
            'path' => ['clothes', 'menswear', 'menstrousers'],
            'labels' => [
                'en_GB' => 'Men\'s Trousers',
            ],
        ];

        $expected = DeleteCategoryCommand::of('menstrousers')->withTimestamp(1510313694);
        $mapper = CategoryEventCommandMapper::create($this->getFredhopperConfiguration());
        $actual = $mapper->getDeleteCommands($eventJson);
        self::assertEquals($expected->toJson(), $actual['menstrousers']->toJson());
    }
}