<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\ProductModelData;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;
use SnowIO\FredhopperDataModel\ProductDataSet;

class ProductModelToProductMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(ProductModelData $productModelData): ProductDataSet
    {
        $productId = ($this->productIdMapper)($productModelData->getCode(), $productModelData->getChannel());
        $akeneoAttributeValues = $productModelData->getAttributeValues();
        $fredhopperAttributeValues = ($this->attributeValueMapper)($akeneoAttributeValues);
        return ProductDataSet::of([FredhopperProductData::of($productId)->withAttributeValues($fredhopperAttributeValues)]);
    }

    public function withProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->productIdMapper = $fn;
        return $result;
    }

    public function withAttributeValueMapper($attributeValueMapper): self
    {
        $result = clone $this;
        $result->attributeValueMapper = $attributeValueMapper;
        return $result;
    }

    /** @var callable */
    private $productIdMapper;
    /** @var SimpleAttributeValueMapper */
    private $attributeValueMapper;

    private function __construct()
    {
        $this->productIdMapper = function (string $variantGroupCode, string $channel) {
            return $variantGroupCode;
        };
        $this->attributeValueMapper = SimpleAttributeValueMapper::create();
    }
}
