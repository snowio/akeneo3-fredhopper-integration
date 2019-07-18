<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeData;
use SnowIO\FredhopperDataModel\AttributeDataSet;

class AttributeMapperWithFilter
{
    public static function of(callable $mapper, callable $filter): self
    {
        $filterableAttributeMapper = new self;
        $filterableAttributeMapper->mapper = $mapper;
        $filterableAttributeMapper->filter = $filter;
        return $filterableAttributeMapper;
    }

    public function __invoke(AttributeData $akeneoAttributeData): AttributeDataSet
    {
        $filterResult = ($this->filter)($akeneoAttributeData);
        if (!$filterResult) {
            return AttributeDataSet::create();
        }
        return ($this->mapper)($akeneoAttributeData);
    }

    /** @var callable */
    private $mapper;
    /** @var callable */
    private $filter;

    private function __construct()
    {

    }
}
