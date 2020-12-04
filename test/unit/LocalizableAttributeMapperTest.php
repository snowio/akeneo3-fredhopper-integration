<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\Akeneo3DataModel\AttributeType as AkeneoAttributeType;
use SnowIO\Akeneo3Fredhopper\LocalizableAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;
use SnowIO\Akeneo3DataModel\InternationalizedString as AkeneoInternationalizedString;

class LocalizableAttributeMapperTest extends TestCase
{
    /**
     */
    public function testInvalidCreationOfMapper()
    {
        self::expectException(InvalidArgumentException::class);
        LocalizableAttributeMapper::of([]);
    }

    public function testMapWithLocales()
    {
        $mapper = LocalizableAttributeMapper::of(['en_GB', 'fr_FR'])
            ->withTypeMapper(function (string $type) {
                return FredhopperAttributeType::LIST;
            });

        $attributeData = AkeneoAttributeData::fromJson([
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
        ]);

        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size_en_gb',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
            FredhopperAttributeData::of(
                'size_fr_fr',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
        ]);

        $actual = $mapper($attributeData);
        self::assertTrue($expected->equals($actual));
    }

    public function testWithNameMapper()
    {
        $mapper = LocalizableAttributeMapper::create()
            ->withTypeMapper(function (string $type) {
                return FredhopperAttributeType::LIST;
            })
            ->withNameMapper(function (AkeneoInternationalizedString $akeneoInternationalisedString) {
                $result = FredhopperInternationalizedString::create();
                foreach ($akeneoInternationalisedString as $akeneoLocalizedString) {
                    $result = $result->withValue($akeneoLocalizedString->getValue() . '_test', $akeneoLocalizedString->getLocale());
                }
                return $result;
            });

        $attributeData = AkeneoAttributeData::fromJson([
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
        ]);

        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size_en_gb',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size_test', 'en_GB')
                    ->withValue('Taille_test', 'fr_FR')
            ),
            FredhopperAttributeData::of(
                'size_fr_fr',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size_test', 'en_GB')
                    ->withValue('Taille_test', 'fr_FR')
            ),
        ]);

        $actual = $mapper($attributeData);
        self::assertTrue($expected->equals($actual));
    }

    public function testWithAttributeIdMapper()
    {
        $mapper = LocalizableAttributeMapper::create()->withTypeMapper(function (string $type) {
            return FredhopperAttributeType::LIST;
        })->withAttributeIdMapper(function (string $akeneoAttributeCode, string $locale) {
            return FredhopperAttributeData::sanitizeId("{$akeneoAttributeCode}_{$locale}_test");
        });

        $attributeData = AkeneoAttributeData::fromJson([
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
        ]);

        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size_en_gb_test',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
            FredhopperAttributeData::of(
                'size_fr_fr_test',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
        ]);

        $actual = $mapper($attributeData);
        self::assertTrue($expected->equals($actual));
    }

    public function testAutomaticLocales()
    {
        $mapper = LocalizableAttributeMapper::create()->withTypeMapper(function (string $type) {
            return FredhopperAttributeType::LIST;
        });

        $attributeData = AkeneoAttributeData::fromJson([
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
        ]);

        $expected = AttributeDataSet::of([
            FredhopperAttributeData::of(
                'size_en_gb',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
            FredhopperAttributeData::of(
                'size_fr_fr',
                FredhopperAttributeType::LIST,
                FredhopperInternationalizedString::create()
                    ->withValue('Size', 'en_GB')
                    ->withValue('Taille', 'fr_FR')
            ),
        ]);

        $actual = $mapper($attributeData);
        self::assertTrue($expected->equals($actual));
    }

}
