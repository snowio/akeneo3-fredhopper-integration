<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\AttributeData;
use SnowIO\Akeneo3DataModel\AttributeValue;

final class AttributeBlacklist
{
    public static function of(array $attributeCodes): self
    {
        foreach ($attributeCodes as $attributeCode) {
            if (!\is_string($attributeCode)) {
                throw new \InvalidArgumentException;
            }
        }

        $blacklist = new self;
        $blacklist->attributeCodes = \array_flip($attributeCodes);
        return $blacklist;
    }

    public function getAttributeFilter(): callable
    {
        return function (AttributeData $akeneoAttributeData): bool {
            $attributeCode = $akeneoAttributeData->getCode();
            return !isset($this->attributeCodes[$attributeCode]);
        };
    }

    public function getAttributeValueFilter(): callable
    {
        return function (AttributeValue $akeneoAttributValue): bool {
            $attributeCode = $akeneoAttributValue->getAttributeCode();
            return !isset($this->attributeCodes[$attributeCode]);
        };
    }

    private function __construct()
    {

    }

    private $attributeCodes;
}
