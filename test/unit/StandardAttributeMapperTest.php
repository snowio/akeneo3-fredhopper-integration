<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\Akeneo3DataModel\AttributeType as AkeneoAttributeType;
use SnowIO\Akeneo3DataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\Akeneo3Fredhopper\StandardAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;

class StandardAttributeMapperTest extends TestCase
{
    public function testLocalisableAttribute()
    {
        $mapper = StandardAttributeMapper::create();
        $actual = $mapper(AkeneoAttributeData::fromJson([
            'code' => 'size',
            'type' => AkeneoAttributeType::SIMPLESELECT,
            'localizable' => true,
            'scopable' => true,
            'sort_order' => 34,
            'labels' => [
                'en_GB' => 'Size',
                'fr_FR' => 'Taille',
            ],
            'group' => 'general',
            '@timestamp' => 1508491122,
        ]));
        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size',
                FredhopperAttributeType::ASSET,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
        ]);
        self::assertTrue($expected->equals($actual));
    }

    public function testLocalisableDatetimeAttribute()
    {
        $mapper = StandardAttributeMapper::create();
        $actual = $mapper(AkeneoAttributeData::fromJson([
            'code' => 'size',
            'type' => AkeneoAttributeType::SIMPLESELECT,
            'localizable' => true,
            'scopable' => true,
            'sort_order' => 34,
            'labels' => [
                'en_GB' => 'Size',
                'fr_FR' => 'Taille',
            ],
            'group' => 'general',
            '@timestamp' => 1508491122,
        ]));
        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size',
                FredhopperAttributeType::ASSET,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
        ]);
        self::assertTrue($expected->equals($actual));
    }

    public function testAttributeNumberWithDec()
    {
        $mapper = StandardAttributeMapper::create();
        $actual = $mapper(AkeneoAttributeData::fromJson([
            'code' => 'width',
            'type' => AkeneoAttributeType::NUMBER,
            'localizable' => false,
            'scopable' => false,
            'sort_order' => 34,
            'labels' => [
                'en_GB' => 'Width',
                'es_ES' => 'Ancho',
            ],
            'group' => 'general',
            'decimals_allowed' => true,
            '@timestamp' => 1508491122,
        ]));
        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'width',
                FredhopperAttributeType::FLOAT,
                FredhopperInternationalizedString::create()
                    ->withValue('Width', 'en_GB')
                    ->withValue('Ancho', 'es_ES')
            ),
        ]);
        self::assertTrue($expected->equals($actual));
    }

    public function testNonLocalisableAttribute()
    {
        $mapper = StandardAttributeMapper::create();
        $actual = $mapper(AkeneoAttributeData::fromJson([
            'code' => 'size',
            'type' => AkeneoAttributeType::SIMPLESELECT,
            'localizable' => false,
            'scopable' => true,
            'sort_order' => 34,
            'labels' => [
                'en_GB' => 'Size',
                'fr_FR' => 'Taille',
            ],
            'group' => 'general',
            '@timestamp' => 1508491122,
        ]));
        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
        ]);
        self::assertTrue($expected->equals($actual));
    }

    public function testNonLocalizableAttributeWithNameMapper()
    {
        $mapper = StandardAttributeMapper::create()
            ->withAttributeIdMapper(function (string $akeneoAttributeCode) {
                return $akeneoAttributeCode . '_mapped';
            })
            ->withNameMapper(function (AkeneoInternationalizedString $labels) {
                return FredhopperInternationalizedString::create()->withValue($labels->getValue('en_GB'), 'en_GB');
            });
        $actual = $mapper(AkeneoAttributeData::fromJson([
            'code' => 'size',
            'type' => AkeneoAttributeType::SIMPLESELECT,
            'localizable' => false,
            'scopable' => true,
            'sort_order' => 34,
            'labels' => [
                'en_GB' => 'Size',
                'fr_FR' => 'Taille',
            ],
            'group' => 'general',
            '@timestamp' => 1508491122,
        ]));
        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size_mapped',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()->withValue('Size', 'en_GB')
            ),
        ]);
        self::assertTrue($expected->equals($actual));
    }
}
