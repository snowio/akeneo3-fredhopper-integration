<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\ProductData as AkeneoProductData;
use SnowIO\Akeneo3Fredhopper\ProductToProductMapper;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\CategoryData;
use SnowIO\FredhopperDataModel\CategoryIdSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;
use SnowIO\FredhopperDataModel\ProductDataSet;

class ProductToProductMapperTest extends TestCase
{
    public function testMapWithDefaultMappers()
    {
        $mapper = ProductToProductMapper::create();
        $actual = $mapper(AkeneoProductData::fromJson([
            'sku' => 'abc123',
            'channel' => 'main',
            'groups' => [],
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'family' => "mens_t_shirts",
            'attribute_values' => [
                'size' => 'Large',
            ],
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]));
        $expected = ProductDataSet::of([FredhopperProductData::of('abc123')
            ->withCategoryIds(CategoryIdSet::of(['tshirts', 'trousers']))
            ->withAttributeValue(AttributeValue::of('size', 'Large'))]);

        self::assertTrue($expected->equals($actual));
    }

    public function testWithCustomMappers()
    {
        $mapper = ProductToProductMapper::create()
            ->withCategoryIdMapper(function (string $categoryId) {
                return CategoryData::sanitizeId($categoryId . '_mapped');
            })
            ->withProductIdMapper(function (string $sku) {
                return $sku . '_mapped';
            });
        $actual = $mapper(AkeneoProductData::fromJson([
            'sku' => 'abc123',
            'channel' => 'main',
            'groups' => [],
            'parent' => null,
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'family' => "mens_t_shirts",
            'attribute_values' => [
                'size' => 'Large',
            ],
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]));
        $expected = ProductDataSet::of([FredhopperProductData::of('abc123_mapped')
            ->withCategoryIds(CategoryIdSet::of(['tshirtsmapped', 'trousersmapped']))
            ->withAttributeValue(AttributeValue::of('size', 'Large'))]);

        self::assertTrue($expected->equals($actual));
    }
}
