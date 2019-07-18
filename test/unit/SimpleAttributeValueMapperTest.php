<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper\Test;

use PHPUnit\Framework\TestCase;

use SnowIO\Akeneo3Fredhopper\SimpleAttributeValueMapper;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\Akeneo3DataModel\AttributeValueSet as AkeneoAttributeValueSet;


class SimpleAttributeValueMapperTest extends TestCase
{

    public function testWithMultipleDifferentAttributeValues()
    {
        $actual = (SimpleAttributeValueMapper::create())(AkeneoAttributeValueSet::fromJson('main', [
            'attribute_values' => [
                'size' => 'large',
                'price' => [
                    'gbp' => '30',
                    'eur' => '37.45',
                ],
                'weight' => '30',
            ],
        ]));

        $expected = FredhopperAttributeValueSet::of([
            FredhopperAttributeValue::of('size', 'large'),
            FredhopperAttributeValue::of('weight', '30'),
        ]);

        self::assertTrue($expected->equals($actual));
    }
}
