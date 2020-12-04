<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\Akeneo3DataModel\ProductModelData;
use SnowIO\Akeneo3Fredhopper\ProductModelToProductMapper;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;
use SnowIO\FredhopperDataModel\ProductDataSet;

class ProductModelProductMapperTest extends TestCase
{
    public function testMappers()
    {
        $mapper = ProductModelToProductMapper::create()
            ->withProductIdMapper(function (string $variantGroupCode, string $channel) {
                return "{$channel}_{$variantGroupCode}";
            })
            ->withAttributeValueMapper(function(AkeneoAttributeValueSet $akeneoAttributeValues) {
                return FredhopperAttributeValueSet::create()->with(FredhopperAttributeValue::of('foo', 'bar'));
            });
        $actual = $mapper(ProductModelData::fromJson([
            'code' => '1001425',
            'channel' => "demontweeks",
            "parent" => null,
            'family_variant' => [
                "code" => "by_colour_size_width",
                "family" => "main",
                "labels" => [
                    "en_GB" => "By Colour, Size, Width"
                ],
                "variant_axes" => [
                    "1" => [
                        "size_config"
                    ],
                    "2" => []
                ],
                "variant_attributes" => [
                    "1" => [
                        "size_config"
                    ],
                    "2" => [
                        "sku",
                        "prezola_sku",
                        "title",
                        "mpn",
                        "ean_upc",
                        "price_chargeable",
                        "list_price",
                        "size_sml_config",
                        "width_num_config"
                    ]
                ]
            ],
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'attribute_values' => [
                'color' => 'blue'
            ],
            'localizations' => [],
        ]));
        $expected = ProductDataSet::of([FredhopperProductData::of('demontweeks_1001425')
            ->withAttributeValue(FredhopperAttributeValue::of('foo', 'bar'))]);
        self::assertTrue($expected->equals($actual));
    }

    public function testWithoutMappers()
    {
        $mapper = ProductModelToProductMapper::create();
        $actual = $mapper(ProductModelData::fromJson([
            'parent' => null,
            'code' => '1001425',
            'family_variant' => [
                "code" => "by_colour_size_width",
                "family" => "main",
                "labels" => [
                    "en_GB" => "By Colour, Size, Width"
                ],
                "variant_axes" => [
                    "1" => [
                        "size_config"
                    ],
                    "2" => []
                ],
                "variant_attributes" => [
                    "1" => [
                        "size_config"
                    ],
                    "2" => [
                        "sku",
                        "prezola_sku",
                        "title",
                        "mpn",
                        "ean_upc",
                        "price_chargeable",
                        "list_price",
                        "size_sml_config",
                        "width_num_config"
                    ]
                ]
            ],
            'categories' => [
                ['mens', 't_shirts'],
                ['mens', 'trousers'],
            ],
            'channel' => "demontweeks",
            'attribute_values' => [
                'color' => 'blue'
            ],
            'localizations' => [],
        ]));
        $expected = ProductDataSet::of([FredhopperProductData::of('1001425')
            ->withAttributeValue(FredhopperAttributeValue::of('color', 'blue'))]);
        self::assertTrue($expected->equals($actual));
    }
}
