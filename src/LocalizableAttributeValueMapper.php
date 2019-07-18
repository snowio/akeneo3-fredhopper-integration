<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeValue as AkeneoAttributeValue;
use SnowIO\Akeneo3DataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeData;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class LocalizableAttributeValueMapper
{
    public static function create(): LocalizableAttributeValueMapper
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $akeneoAttributeValues = $akeneoAttributeValues->filter(function (AkeneoAttributeValue $attributeValue) {
            return $attributeValue->getScope()->getLocale() !== null;
        });

        /** @var FredhopperAttributeValueSet $attributeValues */
        $attributeValues = FredhopperAttributeValueSet::create();
        /** @var AkeneoAttributeValue $akeneoAttributeValue */
        foreach ($akeneoAttributeValues as $akeneoAttributeValue) {
            $attributeCode = $akeneoAttributeValue->getAttributeCode();
            $akeneoLocale = $akeneoAttributeValue->getScope()->getLocale();
            $attributeId = ($this->attributeIdMapper)($attributeCode, $akeneoLocale);
            $value = $akeneoAttributeValue->getValue();
            $attributeValues = $attributeValues->with(FredhopperAttributeValue::of($attributeId, $value));
        }
        return $attributeValues;
    }

    public function withAttributeIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->attributeIdMapper = $fn;
        return $result;
    }

    private $attributeIdMapper;

    private function __construct()
    {
        $this->attributeIdMapper = function (string $akeneoAttributeCode, string $akeneoLocale) {
            return AttributeData::sanitizeId("{$akeneoAttributeCode}_{$akeneoLocale}");
        };
    }
}
