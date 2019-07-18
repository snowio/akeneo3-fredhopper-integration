<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\Akeneo3DataModel\AttributeOptionIdentifier;
use SnowIO\Akeneo3DataModel\InternationalizedString as AkeneoInternationalizedString;
use SnowIO\Akeneo3DataModel\LocalizedString as AkeneoLocalizedString;
use SnowIO\Akeneo3Fredhopper\AttributeOptionMapper;
use SnowIO\FredhopperDataModel\AttributeOption as FredhopperAttributeOption;
use SnowIO\FredhopperDataModel\AttributeOptionSet;
use SnowIO\FredhopperDataModel\InternationalizedString as FredhopperInternationalizedString;
use SnowIO\FredhopperDataModel\LocalizedString as FredhopperLocalizedString;

class AttributeOptionMapperTest extends TestCase
{
    /**
     * @dataProvider mapDataProvider
     */
    public function testMap(
        AttributeOptionMapper $mapper,
        AkeneoAttributeOption $input,
        AttributeOptionSet $expectedOutput
    ) {
        $actualOutput = $mapper($input);
        self::assertTrue($actualOutput->equals($expectedOutput));
    }

    public function testMapWithNoLabels()
    {
        $mapper = AttributeOptionMapper::create();
        $attributeOption = AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'));
        $expected = AttributeOptionSet::of([FredhopperAttributeOption::of('size', 'large')]);
        $actual = $mapper($attributeOption);
        self::assertTrue($expected->equals($actual));
    }

    public function testMapWithNoLabelsAndSuffixedAttributeCode()
    {
        $mapper = AttributeOptionMapper::create();
        $attributeOption = AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'size-large'));
        $expected = AttributeOptionSet::of([FredhopperAttributeOption::of('size', 'large')]);
        $actual = $mapper($attributeOption);
        self::assertTrue($expected->equals($actual));
    }

    public function testMapWithLabels()
    {

        $mapper = AttributeOptionMapper::create()
            ->withDisplayValueMapper(function (AkeneoInternationalizedString $optionLabels) {
                return FredhopperInternationalizedString::create()
                    ->withValue($optionLabels->getValue('en_GB'), 'en_GB')
                    ->withValue($optionLabels->getValue('de_DE'), 'de_DE')
                    ->withValue($optionLabels->getValue('eu_FR'), 'fr_FR');
            });
        $attributeOption = AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
            ->withLabel(AkeneoLocalizedString::of('Groß', 'de_DE'))
            ->withLabel(AkeneoLocalizedString::of('Grand', 'eu_FR'))
            ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB'));
        $expected = AttributeOptionSet::of([
            FredhopperAttributeOption::of('size', 'large')
                ->withDisplayValue(FredhopperLocalizedString::of('Groß', 'de_DE'))
                ->withDisplayValue(FredhopperLocalizedString::of('Grand', 'fr_FR'))
                ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB'))
        ]);
        $actual = $mapper($attributeOption);
        self::assertTrue($expected->equals($actual));
    }

    public function testNoLabelsWithIdMappers()
    {
        $mapper = AttributeOptionMapper::create()
            ->withAttributeIdMapper(function (string $akeneoAttributeCode) {
                return $akeneoAttributeCode . '_modified';
            })
            ->withValueIdMapper(function (string $akeneoOptionCode) {
                return $akeneoOptionCode . '_modified';
            });

        $attributeOption = AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
            ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB'));

        $expected = AttributeOptionSet::of([
            FredhopperAttributeOption::of('size_modified', 'large_modified')
                ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB'))
        ]);
        $actual = $mapper($attributeOption);
        self::assertTrue($expected->equals($actual));
    }

    public function testLabelsWithDisplayValueMapper()
    {
        $mapper = AttributeOptionMapper::create()
            ->withDisplayValueMapper(function (AkeneoInternationalizedString $optionLabels) {
                return FredhopperInternationalizedString::create()
                    ->withValue($optionLabels->getValue('en_GB'), 'en_GB')
                    ->withValue($optionLabels->getValue('de_DE'), 'de_DE');
            });

        $attributeOption = AkeneoAttributeOption::of(AttributeOptionIdentifier::of('size', 'large'))
            ->withLabel(AkeneoLocalizedString::of('Groß', 'de_DE'))
            ->withLabel(AkeneoLocalizedString::of('Grand', 'eu_FR'))
            ->withLabel(AkeneoLocalizedString::of('Large', 'en_GB'));

        $expected = AttributeOptionSet::of([FredhopperAttributeOption::of('size', 'large')
            ->withDisplayValue(FredhopperLocalizedString::of('Groß', 'de_DE'))
            ->withDisplayValue(FredhopperLocalizedString::of('Large', 'en_GB'))]);
        $actual = $mapper($attributeOption);
        self::assertTrue($expected->equals($actual));
    }

    public function testIdSanitization()
    {
        $mapper = AttributeOptionMapper::create();

        $attributeOption = AkeneoAttributeOption::of(AttributeOptionIdentifier::of('Size!', 'LARGE!'));

        $expected = AttributeOptionSet::of([FredhopperAttributeOption::of('size', 'large')]);
        $actual = $mapper($attributeOption);
        self::assertTrue($expected->equals($actual));

    }

    public function mapDataProvider()
    {
        return [
            'idSanitization' => [
                AttributeOptionMapper::create(),
                AkeneoAttributeOption::of(AttributeOptionIdentifier::of('Size!', 'LARGE!')),
                AttributeOptionSet::of([FredhopperAttributeOption::of('size', 'large')]),
            ],
        ];
    }
}
