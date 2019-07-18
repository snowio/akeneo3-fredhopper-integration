<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\ProductData as AkeneoProductData;
use SnowIO\Akeneo3DataModel\ProductModelData;
use SnowIO\FredhopperDataModel\ProductData;
use SnowIO\FredhopperDataModel\VariantData as FredhopperVariantData;
use SnowIO\FredhopperDataModel\VariantDataSet;

class ProductToVariantMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(AkeneoProductData $akeneoProductData, ?ProductModelData $productModel = null): ?VariantDataSet
    {
        $channel = $akeneoProductData->getChannel();
        $sku = $akeneoProductData->getSku();
        $variantId = ($this->variantIdMapper)($sku, $channel);
        $variantGroupCode = $productModel !== null ? $productModel->getCode() : $akeneoProductData->getProperties()->getParent();
        if ($variantGroupCode === null) {
            $productId = ($this->skuToProductIdMapper)($sku, $channel);
        } else {
            $productId = ($this->variantGroupCodeToProductIdMapper)($variantGroupCode, $channel);
        }
        $akeneoAttributeValues = $akeneoProductData->getAttributeValues();
        $fredhopperAttributeValues = ($this->attributeValueMapper)($akeneoAttributeValues);
        return VariantDataSet::of([FredhopperVariantData::of($variantId, $productId)->withAttributeValues($fredhopperAttributeValues)]);
    }

    public function withSkuToVariantIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->variantIdMapper = $fn;
        return $result;
    }

    public function withParentCodeToProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->variantGroupCodeToProductIdMapper = $fn;
        return $result;
    }

    public function withSkuToProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->skuToProductIdMapper = $fn;
        return $result;
    }

    public function withAttributeValueMapper($attributeValueMapper): self
    {
        $result = clone $this;
        $result->attributeValueMapper = $attributeValueMapper;
        return $result;
    }

    /** @var callable */
    private $variantIdMapper;

    /** @var callable */
    private $skuToProductIdMapper;

    /** @var callable */
    private $variantGroupCodeToProductIdMapper;

    /** @var SimpleAttributeValueMapper */
    private $attributeValueMapper;

    private function __construct()
    {
        $this->skuToProductIdMapper = [ProductData::class, 'sanitizeId'];
        $this->variantGroupCodeToProductIdMapper = [ProductData::class, 'sanitizeId'];
        $this->variantIdMapper = function (string $sku, string $channel) {
            return FredhopperVariantData::sanitizeId("v_{$sku}");
        };
        $this->attributeValueMapper = SimpleAttributeValueMapper::create();
    }
}
