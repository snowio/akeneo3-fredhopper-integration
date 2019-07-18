<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeData as AkeneoAttributeData;
use SnowIO\FredhopperDataModel\AttributeDataSet;

class CompositeAttributeMapper
{
    public static function create()
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeData $akeneoAttributeData): AttributeDataSet
    {
        /** @var AttributeDataSet $fredhopperAttributes */
        $fredhopperAttributes = AttributeDataSet::create();
        foreach ($this->mappers as $mapper) {
            $fredhopperAttributes = $fredhopperAttributes->add($mapper($akeneoAttributeData));
        }
        return $fredhopperAttributes;
    }

    public function with(callable $attributeMapper): self
    {
        $result = clone $this;
        $result->mappers[] = $attributeMapper;
        return $result;
    }

    /** @var callable[] */
    private $mappers = [];

    private function __construct()
    {

    }
}
