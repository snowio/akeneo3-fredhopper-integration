<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\Akeneo3DataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;

class PriceAttributeMapper
{
    public static function of(array $currencies): self
    {
        return new self($currencies);
    }

    public function __invoke(AkeneoAttributeData $akeneoAttributeData): AttributeDataSet
    {
        if ($akeneoAttributeData->getType() !== AkeneoAttributeType::PRICE_COLLECTION) {
            return AttributeDataSet::create();
        }

        /** @var AttributeDataSet $attributes */
        $attributes = AttributeDataSet::create();
        foreach ($this->currencies as $currency) {
            $attributeId = "{$akeneoAttributeData->getCode()}_" . \strtolower($currency);
            $attributeNames = ($this->nameMapper)($akeneoAttributeData->getLabels());
            $attributes = $attributes->with(FredhopperAttributeData::of($attributeId, FredhopperAttributeType::FLOAT, $attributeNames));
        }
        return $attributes;
    }

    public function withNameMapper(callable $fn): self
    {
        $result = clone $this;
        $result->nameMapper = $fn;
        return $result;
    }

    /** @var string[] */
    private $currencies;
    private $nameMapper;

    private function __construct(array $currencies)
    {
        $this->currencies = $currencies;
        $this->nameMapper = InternationalizedStringMapper::create();
    }
}
