<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\Akeneo3DataModel\AttributeType as AkeneoAttributeType;
use SnowIO\Akeneo3Fredhopper\CompositeAttributeMapper;
use SnowIO\Akeneo3Fredhopper\StandardAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\InternationalizedString;

class CompositeAttributeMapperTest extends TestCase
{
    public function testMap()
    {
        $mapper = CompositeAttributeMapper::create()
            ->with(
                StandardAttributeMapper::create()->withAttributeIdMapper(function (string $attributeId) {
                    return $attributeId . '_mapper_modified_1';
                })
            )
            ->with(
                StandardAttributeMapper::create()->withAttributeIdMapper(function (string $attributeId) {
                    return $attributeId . '_mapper_modified_2';
                })
            )
            ->with(
                StandardAttributeMapper::create()->withAttributeIdMapper(function (string $attributeId) {
                    return $attributeId . '_mapper_modified_3';
                })
            );

        $attributeData = AkeneoAttributeData::fromJson([
            'code' => 'size',
            'type' => AkeneoAttributeType::IDENTIFIER,
            'localizable' => false,
            'scopable' => false,
            'sort_order' => 3,
            'labels' => [
                'en_GB' => 'Size',
                'de_DE' => 'Größe',
            ],
            'group' => 'general',
            '@timestamp' => 1508491122,
        ]);

        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size_mapper_modified_1',
                FredhopperAttributeType::TEXT,
                InternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Größe', 'de_DE')
            ),
            FredhopperAttributeData::of(
                'size_mapper_modified_2',
                FredhopperAttributeType::TEXT,
                InternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Größe', 'de_DE')
            ),
            FredhopperAttributeData::of(
                'size_mapper_modified_3',
                FredhopperAttributeType::TEXT,
                InternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Größe', 'de_DE')
            ),
        ]);

        $actual = $mapper($attributeData);
        self::assertTrue($expected->equals($actual));
    }
}
