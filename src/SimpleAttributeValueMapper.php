<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\Akeneo3DataModel\PriceCollection;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\Akeneo3DataModel\AttributeValue as AkeneoAttributeValue;

class SimpleAttributeValueMapper
{
    public static function create()
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $akeneoAttributeValues = $akeneoAttributeValues->filter(function (AkeneoAttributeValue $attributeValue) {
            return !$attributeValue->getValue() instanceof PriceCollection;
        });

        /** @var FredhopperAttributeValueSet $attributeValues */
        $attributeValues = FredhopperAttributeValueSet::create();
        /** @var AkeneoAttributeValue $akeneoAttributeValue */
        foreach ($akeneoAttributeValues as $akeneoAttributeValue) {
            $attributeCode = $akeneoAttributeValue->getAttributeCode();
            $value = $akeneoAttributeValue->getValue();
            $locale = $akeneoAttributeValue->getScope()->getLocale();
            $fredhopperAttributeValue = FredhopperAttributeValue::of($attributeCode, $value)->withLocale($locale);
            $attributeValues = $attributeValues->with($fredhopperAttributeValue);
        }
        return $attributeValues;
    }

    private function __construct()
    {

    }
}
