<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\ProductData as AkeneoProductData;
use SnowIO\FredhopperDataModel\CategoryData;
use SnowIO\FredhopperDataModel\CategoryIdSet;
use SnowIO\FredhopperDataModel\ProductData as FredhopperProductData;
use SnowIO\FredhopperDataModel\ProductDataSet;

class ProductToProductMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(AkeneoProductData $akeneoProductData): ProductDataSet
    {
        $productId = ($this->productIdMapper)($akeneoProductData->getSku(), $akeneoProductData->getChannel());
        $categoryCodes = $akeneoProductData->getProperties()->getCategories()
            ->getCategoryCodes();
        $categoryIds = \array_map(function (string $categoryCode) {
            return ($this->categoryIdMapper)($categoryCode);
        }, $categoryCodes);
        $akeneoAttributeValues = $akeneoProductData->getAttributeValues();
        $fredhopperAttributeValues = ($this->attributeValueMapper)($akeneoAttributeValues);
        return ProductDataSet::of([FredhopperProductData::of($productId)
            ->withCategoryIds(CategoryIdSet::of($categoryIds))
            ->withAttributeValues($fredhopperAttributeValues)]);
    }

    public function withProductIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->productIdMapper = $fn;
        return $result;
    }

    public function withCategoryIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->categoryIdMapper = $fn;
        return $result;
    }

    public function withAttributeValueMapper($attributeValueMapper): self
    {
        $result = clone $this;
        $result->attributeValueMapper = $attributeValueMapper;
        return $result;
    }

    /** @var callable */
    private $categoryIdMapper;
    /** @var callable */
    private $productIdMapper;
    /** @var SimpleAttributeValueMapper */
    private $attributeValueMapper;

    private function __construct()
    {
        $this->categoryIdMapper = [CategoryData::class, 'sanitizeId'];
        $this->productIdMapper = [FredhopperProductData::class, 'sanitizeId'];
        $this->attributeValueMapper = SimpleAttributeValueMapper::create();
    }
}
