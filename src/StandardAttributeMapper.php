<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\Akeneo3DataModel\AttributeType as AkeneoAttributeType;
use SnowIO\FredhopperDataModel\AttributeDataSet;
use SnowIO\FredhopperDataModel\AttributeType as FredhopperAttributeType;
use SnowIO\FredhopperDataModel\AttributeData as FredhopperAttributeData;

class StandardAttributeMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeData $akeneoAttributeData): AttributeDataSet
    {
        $attributeId = ($this->attributeIdMapper)($akeneoAttributeData->getCode());
        $labels = ($this->nameMapper)($akeneoAttributeData->getLabels());
        if ($akeneoAttributeData->isLocalizable()) {
            return AttributeDataSet::of([FredhopperAttributeData::of($attributeId, FredhopperAttributeType::ASSET, $labels)]);
        }
        $fredhopperType = ($this->typeMapper)($akeneoAttributeData);
        return AttributeDataSet::of([FredhopperAttributeData::of($attributeId, $fredhopperType, $labels)]);
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

    public static function getDefaultTypeMapper(): callable
    {
        $typeMap = [
            AkeneoAttributeType::SIMPLESELECT => FredhopperAttributeType::LIST,
            AkeneoAttributeType::BOOLEAN => FredhopperAttributeType::INT,
            AkeneoAttributeType::NUMBER => FredhopperAttributeType::INT,
            AkeneoAttributeType::PRICE_COLLECTION => FredhopperAttributeType::FLOAT,
            AkeneoAttributeType::MULTISELECT => FredhopperAttributeType::SET,
            AkeneoAttributeType::DATE => FredhopperAttributeType::DATETIME
        ];
        return function (AkeneoAttributeData $akeneoAttributeData) use ($typeMap) {
            if ($akeneoAttributeData->getType() === AkeneoAttributeType::NUMBER && $akeneoAttributeData->isDecimalsAllowed()) {
                $type = FredhopperAttributeType::FLOAT;
            } else {
                $type = $typeMap[$akeneoAttributeData->getType()] ?? 'text';
            }
            return $type;
        };
    }

    private $attributeIdMapper;
    private $typeMapper;
    private $nameMapper;

    private function __construct()
    {
        $this->attributeIdMapper = [FredhopperAttributeData::class, 'sanitizeId'];
        $this->typeMapper = self::getDefaultTypeMapper();
        $this->nameMapper = InternationalizedStringMapper::create();
    }
}
