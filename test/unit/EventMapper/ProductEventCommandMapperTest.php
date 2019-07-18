<?php
namespace SnowIO\Akeneo3Fredhopper\Test\EventMapper;

use SnowIO\Akeneo3Fredhopper\EventMapper\ProductEventCommandMapper;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\Command\SaveProductCommand;
use SnowIO\FredhopperDataModel\ProductData;

class ProductEventCommandMapperTest extends CommandEventMapperTest
{
    public function testSaveCommandMapper()
    {
        $eventJson = [
            'new' => [
                'sku' => 'abc123',
                'channel' => 'main',
                'categories' => [
                    ['mens', 'tshirts'],
                    ['mens', 'trousers'],
                ],
                'family' => "mens_t_shirts",
                'attribute_values' => [
                    'size' => 'Large',
                    'product_title' => 'ABC 123 Product',
                ],
                'groups' => [],
                'localizations' => [],
                'enabled' => true,
                '@timestamp' => 1508491122,
            ],
            'old' => [
                'sku' => 'abc123',
                'channel' => 'main',
                'categories' => [
                    ['mens', 'tshirts'],
                    ['mens', 'trousers'],
                ],
                'family' => "mens_t_shirts",
                'attribute_values' => [
                    'size' => 'Medium',
                    'product_title' => 'ABC 123 Product',
                ],
                'groups' => [],
                'localizations' => [],
                'enabled' => true,
                '@timestamp' => 1508491122,
            ]
        ];

        $expected = SaveProductCommand::of(
            ProductData::of('abc123')
                ->withCategoryId('tshirts')
                ->withCategoryId('trousers')
                ->withAttributeValue(AttributeValue::of('size', 'Large'))
                ->withAttributeValue(AttributeValue::of('product_title', 'ABC 123 Product'))
        )->withTimestamp(1508491122);

        $mapper = ProductEventCommandMapper::create($this->getFredhopperConfiguration());
        $actual = $mapper->getSaveCommands($eventJson);
        self::assertEquals($expected->toJson(), $actual['abc123']->toJson());
    }

    public function testDeleteCommandMapper()
    {
        self::markTestIncomplete('TODO find how to test deletes');
    }
}