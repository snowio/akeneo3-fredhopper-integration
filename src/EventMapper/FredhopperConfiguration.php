<?php
namespace SnowIO\Akeneo3Fredhopper\EventMapper;

use SnowIO\FredhopperDataModel\AttributeDataSet;

abstract class FredhopperConfiguration
{
    public abstract function getCategoryMapper(): callable;

    public abstract function getCategoryIdMapper(): callable;

    public abstract function getAttributeMapper(): callable;

    public abstract function getAttributeOptionMapper(): callable;

    public abstract function getProductMapper(): callable;

    abstract function getInternationalizedStringMapper(): callable;

    abstract function getStaticAttributes(): AttributeDataSet;

    abstract function getPriceAttributes(): AttributeDataSet;
}