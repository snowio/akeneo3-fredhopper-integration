<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\ProductData as AkeneoProductData;
use SnowIO\Akeneo3Fredhopper\ProductToVariantMapper;
use SnowIO\FredhopperDataModel\AttributeValue;
use SnowIO\FredhopperDataModel\VariantData as FredhopperVariantData;
use SnowIO\FredhopperDataModel\VariantDataSet;

class ProductToVariantMapperTest extends TestCase
{
    public function testMapVariantWithDefaultMappers()
    {
        $mapper = ProductToVariantMapper::create();
        $actual = $mapper(AkeneoProductData::fromJson([
            'sku' => 'abc123',
            'channel' => 'main',
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'family' => "mens_t_shirts",
            'attribute_values' => [
                'size' => 'Large',
            ],
            'groups' => [],
            'parent' => 'abc',
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]));
        $expected = VariantDataSet::of([FredhopperVariantData::of('v_abc123', 'abc')
            ->withAttributeValue(AttributeValue::of('size', 'Large'))]);
        self::assertTrue($expected->equals($actual));
    }

    public function testMapVariantWithCustomMappers()
    {
        $mapper = ProductToVariantMapper::create()
            ->withParentCodeToProductIdMapper(function (string $vgCode, string $channel) {
                return "{$channel}_{$vgCode}";
            });
        $actual = $mapper(AkeneoProductData::fromJson([
            'sku' => 'abc123',
            'channel' => 'main',
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'family' => "mens_t_shirts",
            'attribute_values' => [
                'size' => 'Large',
            ],
            'parent' => "abc",
            'groups' => [],
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]));
        $expected = VariantDataSet::of([FredhopperVariantData::of('v_abc123', 'main_abc')
            ->withAttributeValue(AttributeValue::of('size', 'Large'))]);
        self::assertTrue($expected->equals($actual));
    }

    public function testMapProductWithDefaultMappers()
    {
        $mapper = ProductToVariantMapper::create();
        $actual = $mapper(AkeneoProductData::fromJson([
            'sku' => 'abc123',
            'channel' => 'main',
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'family' => "mens_t_shirts",
            'attribute_values' => [
                'size' => 'Large',
            ],
            'groups' => [],
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]));
        $expected = VariantDataSet::of([FredhopperVariantData::of('v_abc123', 'abc123')
            ->withAttributeValue(AttributeValue::of('size', 'Large'))]);
        self::assertTrue($expected->equals($actual));
    }

    public function testStandaloneProductWithCustomMappers()
    {
        $mapper = ProductToVariantMapper::create()
            ->withSkuToProductIdMapper(function (string $sku, string $channel) {
                return "{$channel}_{$sku}";
            })
            ->withSkuToVariantIdMapper(function (string $sku, string $channel) {
                return "{$channel}_v_{$sku}";
            });
        $actual = $mapper(AkeneoProductData::fromJson([
            'sku' => 'abc123',
            'channel' => 'main',
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'family' => "mens_t_shirts",
            'attribute_values' => [
                'size' => 'Large',
            ],
            'groups' => [],
            'localizations' => [],
            'enabled' => true,
            '@timestamp' => 1508491122,
        ]));
        $expected = VariantDataSet::of([FredhopperVariantData::of('main_v_abc123', 'main_abc123')
            ->withAttributeValue(AttributeValue::of('size', 'Large'))]);
        self::assertTrue($expected->equals($actual));
    }
}
