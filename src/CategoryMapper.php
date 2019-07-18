<?php
declare(strict_types=1);
namespace SnowIO\Akeneo3Fredhopper;

use SnowIO\Akeneo3DataModel\CategoryData as AkeneoCategoryData;
use SnowIO\FredhopperDataModel\CategoryData as FredhopperCategoryData;
use SnowIO\FredhopperDataModel\CategoryDataSet;

class CategoryMapper
{
    public static function create(): self
    {
        return new self;
    }

    public function __invoke(AkeneoCategoryData $akeneoCategory): CategoryDataSet
    {
        $categoryId = ($this->categoryIdMapper)($akeneoCategory->getCode());
        $names = ($this->nameMapper)($akeneoCategory->getLabels());
        $category = FredhopperCategoryData::of($categoryId, $names);
        if ($akeneoCategory->getParent() !== null) {
            $parentId = ($this->categoryIdMapper)($akeneoCategory->getParent());
            $category = $category->withParent($parentId);
        }
        return CategoryDataSet::of([$category]);
    }

    public function withCategoryIdMapper(callable $fn): self
    {
        $result = clone $this;
        $result->categoryIdMapper = $fn;
        return $result;
    }

    public function withNameMapper(callable $fn): self
    {
        $result = clone $this;
        $result->nameMapper = $fn;
        return $result;
    }

    /** @var callable */
    private $categoryIdMapper;
    /** @var callable */
    private $nameMapper;

    private function __construct()
    {
        $this->categoryIdMapper = [FredhopperCategoryData::class, 'sanitizeId'];
        $this->nameMapper = InternationalizedStringMapper::create();
    }
}
