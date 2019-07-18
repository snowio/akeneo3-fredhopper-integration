<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeValueSet as AkeneoAttributeValueSet;
use SnowIO\FredhopperDataModel\AttributeValueSet as FredhopperAttributeValueSet;

class CompositeAttributeValueMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(AkeneoAttributeValueSet $akeneoAttributeValues): FredhopperAttributeValueSet
    {
        /** @var FredhopperAttributeValueSet $result */
        $result = FredhopperAttributeValueSet::create();
        foreach ($this->mappers as $mapper) {
            $fredhopperAttributeValues = $mapper($akeneoAttributeValues);
            $result = $result->add($fredhopperAttributeValues);
        }
        return $result;
    }

    public function with(callable $attributeValueMapper): self
    {
        $result = clone $this;
        $result->mappers[] = $attributeValueMapper;
        return $result;
    }

    /** @var callable[] */
    private $mappers = [];

    private function __construct()
    {

    }
}
