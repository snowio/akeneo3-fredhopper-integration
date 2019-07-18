<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeOption as AkeneoAttributeOption;
use SnowIO\FredhopperDataModel\AttributeData;
use SnowIO\FredhopperDataModel\AttributeOption as FredhopperAttributeOption;
use SnowIO\FredhopperDataModel\AttributeOptionSet;

class AttributeOptionMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeOption $attributeOption): AttributeOptionSet
    {
        $attributeId = ($this->attributeIdMapper)($attributeOption->getAttributeCode());
        $valueId = ($this->valueIdMapper)($attributeOption->getOptionCode());
        $labels = ($this->displayValueMapper)($attributeOption->getLabels());
        return AttributeOptionSet::of([
            FredhopperAttributeOption::of($attributeId, $valueId)->withDisplayValues($labels)
        ]);
    }

    public function withAttributeIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->attributeIdMapper = $fn;
        return $result;
    }

    public function withValueIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->valueIdMapper = $fn;
        return $result;
    }

    public function withDisplayValueMapper(callable $fn): self
    {
        $result = clone $this;
        $result->displayValueMapper = $fn;
        return $result;
    }

    private static function getDefaultValueIdMapper(): callable
    {
        return function (string $value) {
            $values = explode('-', $value);
            $value = count($values) >= 2 ? implode('-', array_slice($values, 1)) : $value;
            return FredhopperAttributeOption::sanitizeValueId($value);
        };
    }

    private $attributeIdMapper;
    private $valueIdMapper;
    private $displayValueMapper;

    private function __construct()
    {
        $this->attributeIdMapper = [AttributeData::class, 'sanitizeId'];
        $this->valueIdMapper = self::getDefaultValueIdMapper();
        $this->displayValueMapper = InternationalizedStringMapper::create();
    }
}
