<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;
use SnowIO\FredhopperDataModel\AttributeDataSet;

class LocalizableAttributeMapper
{
    public static function create(): self
    {
        return new self;
    }

    public static function of(array $locales): self
    {
        if (empty($locales)) {
            throw new \InvalidArgumentException;
        }

        $mapper = self::create();
        $mapper->locales = $locales;
        return $mapper;
    }

    public function __invoke(AkeneoAttributeData $akeneoAttributeData): AttributeDataSet
    {
        /** @var AttributeDataSet $attributes */
        $attributes = AttributeDataSet::create();
        $type = ($this->typeMapper)($akeneoAttributeData);
        $locales = $this->locales ?? $akeneoAttributeData->getLabels()->getLocales();
        foreach ($locales as $locale) {
            $attributeId = ($this->attributeIdMapper)($akeneoAttributeData->getCode(), $locale);
            $names = ($this->nameMapper)($akeneoAttributeData->getLabels());
            $attributes = $attributes->with(FredhopperAttributeData::of($attributeId, $type, $names));
        }
        return $attributes;
    }

    public function withAttributeIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->attributeIdMapper = $fn;
        return $result;
    }

    public function withTypeMapper(callable $fn): self
    {
        $result = clone $this;
        $result->typeMapper = $fn;
        return $result;
    }

    public function withNameMapper(callable $fn): self
    {
        $result = clone $this;
        $result->nameMapper = $fn;
        return $result;
    }

    private $locales;
    private $attributeIdMapper;
    private $typeMapper;
    private $nameMapper;

    private function __construct()
    {
        $this->attributeIdMapper = function (string $akeneoAttributeCode, string $locale) {
            return FredhopperAttributeData::sanitizeId("{$akeneoAttributeCode}_{$locale}");
        };
        $this->typeMapper = StandardAttributeMapper::getDefaultTypeMapper();
        $this->nameMapper = InternationalizedStringMapper::create();
    }
}
