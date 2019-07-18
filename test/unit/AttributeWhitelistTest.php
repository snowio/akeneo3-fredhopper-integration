<?php
declare(strict_types = 1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use PHPUnit\Framework\TestCase;
use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\Akeneo3DataModel\AttributeType as AkeneoAttributeType;
use SnowIO\Akeneo3DataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\Akeneo3Fredhopper\AttributeValueMapperWithFilter;
use SnowIO\Akeneo3Fredhopper\SimpleAttributeValueMapper;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\Akeneo3Fredhopper\AttributeWhitelist;
use SnowIO\Akeneo3Fredhopper\AttributeMapperWithFilter;
use SnowIO\Akeneo3Fredhopper\StandardAttributeMapper;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class AttributeWhitelistTest extends TestCase
{
    public function testAttributeFiltration()
    {
        $attributeBlacklist = AttributeWhitelist::of(['size']);
        $attributeMapperWithFilter = AttributeMapperWithFilter::of(StandardAttributeMapper::create(),
            $attributeBlacklist->getAttributeFilter());

        $akeneoAttributeData = AkeneoAttributeData::fromJson([
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
        ]);

        /** @var AttributeDataSet $dataSet */
        $dataSet = $attributeMapperWithFilter($akeneoAttributeData);
        self::assertEquals(1, $dataSet->count());

        $akeneoAttributeData = AkeneoAttributeData::fromJson([
            'code' => 'color',
            'type' => AkeneoAttributeType::SIMPLESELECT,
            'localizable' => false,
            'scopable' => true,
            'sort_order' => 34,
            'labels' => [
                'en_GB' => 'Color',
            ],
            'group' => 'general',
            '@timestamp' => 1508491122,
        ]);

        /** @var AttributeDataSet $actualOutput */
        $dataSet = $attributeMapperWithFilter($akeneoAttributeData);
        self::assertEquals(0, $dataSet->count());
    }

    public function testAttributeValueFiltration()
    {
        $attributeBlacklist = AttributeWhitelist::of(['size']);
        $attributeValueMapperWithFilter = AttributeValueMapperWithFilter::of(SimpleAttributeValueMapper::create(),
            $attributeBlacklist->getAttributeValueFilter());

        $akeneoAttributeValueData = AkeneoAttributeValueSet::fromJson('main', [
            'attribute_values' => [
                'size' => 'large',
                'weight' =>  '30'
            ],
        ]);
        /** @var FredhopperAttributeValueSet $dataSet */
        $dataSet = $attributeValueMapperWithFilter($akeneoAttributeValueData);
        self::assertTrue(FredhopperAttributeValueSet::of([
            FredhopperAttributeValue::of('size', 'large')
        ])->equals($dataSet));
    }
}