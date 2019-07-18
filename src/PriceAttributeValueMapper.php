<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\Akeneo3DataModel\Price;
use SnowIO\Akeneo3DataModel\PriceCollection;
use SnowIO\FredhopperDataModel\AttributeData;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;
use SnowIO\Akeneo3DataModel\AttributeValue as AkeneoAttributeValue;
use SnowIO\FredhopperDataModel\AttributeValue as FredhopperAttributeValue;

class PriceAttributeValueMapper
{
    public static function create(): PriceAttributeValueMapper
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        $akeneoAttributeValues = $akeneoAttributeValues->filter(function (AkeneoAttributeValue $attributeValue) {
            return $attributeValue->getValue() instanceof PriceCollection;
        });

        /** @var FredhopperAttributeValueSet $attributeValues */
        $attributeValues = FredhopperAttributeValueSet::create();
        /** @var AkeneoAttributeValue $akeneoAttributeValue */
        foreach ($akeneoAttributeValues as $akeneoAttributeValue) {
            /** @var PriceCollection $prices */
            $prices = $akeneoAttributeValue->getValue();
            /** @var Price $price */
            foreach ($prices as $price) {
                $attributeId = ($this->attributeIdMapper)($akeneoAttributeValue->getAttributeCode(), $price->getCurrency());
                $attributeValues = $attributeValues->with(FredhopperAttributeValue::of($attributeId, $price->getAmount()));
            }
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
        $this->attributeIdMapper = function (string $akeneoAttributeCode, string $akeneoCurrency) {
            return AttributeData::sanitizeId("{$akeneoAttributeCode}_{$akeneoCurrency}");
        };
    }
}
