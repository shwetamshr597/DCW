<?php

namespace Dcw\FlooringCalculation\Block\Product\View\Type;

use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;

class Configurable
{

    protected $jsonEncoder;
    protected $jsonDecoder;

    public function __construct(
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        \Dcw\FlooringCalculation\Helper\Data $flooringCalcHelper,
    ) {

        $this->jsonDecoder = $jsonDecoder;
        $this->jsonEncoder = $jsonEncoder;
        $this->flooringCalcHelper = $flooringCalcHelper;
    }

    public function aroundGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        \Closure $proceed
    ) {
        $config = $proceed();
        $config = $this->jsonDecoder->decode($config);
        $config['roll_cut_type'] = $this->getRollCutType($subject);
        $config['min_rollcut'] = $this->getMinRollcut($subject);
        $config['max_roll_length_restriction'] = $this->getMaxRollLengthRestriction($subject);
        $config['tile_size_area'] = $this->getTileSizeArea($subject);
        $config['exact_width'] = $this->getExactWidth($subject);
        $config['exact_length'] = $this->getExactLength($subject);
        $config['exact_height'] = $this->getExactHeight($subject);
        $config['coverage_per_case'] = $this->getCoveragePerCase($subject);
        $config['min_tile_quantity'] = $this->getMinTileQuantity($subject);
        $config['pdp_shipping_text'] = $this->getPdpShippingText($subject);
        $config['expected_days_ship'] = $this->getExpectedDaysShip($subject);
        $config['quick_ship'] = $this->getQuickShip($subject);
        return $this->jsonEncoder->encode($config);
    }
    
    /*public function getTileSizeArea($subject)
    {
        $tileSizeAreaArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getAttributeText('tile_size_area'))) {
                $tile_size_area = str_replace(['"',"'"], '', $product->getAttributeText('tile_size_area'));
                $tile_size_area = str_replace('×', 'x', $product->getAttributeText('tile_size_area'));
                $tile_size_area = preg_replace('/\s+/', '', $tile_size_area);
            } else {
                $tile_size_area = '';
            }
            $tileSizeAreaArr[$product->getId()] = $tile_size_area;
        }
        return $tileSizeAreaArr;
    }*/

    public function getRollCutType($subject)
    {
        $rollCutType = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getRollCutType())) {
                $roll_cut_type = $product->getRollCutType();
            } else {
                $roll_cut_type = '';
            }
            $rollCutType[$product->getId()] = $roll_cut_type;
        }
        return $rollCutType;
    }

    public function getMaxRollLengthRestriction($subject)
    {
        $maxRollLengthRestriction = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getMaxRollLengthRestriction())) {
                $max_roll_cut = $product->getMaxRollLengthRestriction();
            } else {
                $max_roll_cut = '';
            }
            $maxRollLengthRestriction[$product->getId()] = $max_roll_cut;
        }
        return $maxRollLengthRestriction;
    }

    public function getMinRollcut($subject)
    {
        $minRollcut = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getMinRollcut())) {
                $min_roll_cut = $product->getMinRollcut();
            } else {
                $min_roll_cut = '';
            }
            $minRollcut[$product->getId()] = $min_roll_cut;
        }
        return $minRollcut;
    }

    public function getTileSizeArea($subject)
    {
        $tileSizeAreaArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getTileSizeArea())) {
                $tile_size_area = $product->getTileSizeArea();
            } else {
                $tile_size_area = '';
            }
            $tileSizeAreaArr[$product->getId()] = $tile_size_area;
        }
        return $tileSizeAreaArr;
    }

    public function getExactWidth($subject)
    {
        $exactWidthArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getExactWidth())) {
                $exact_width = $product->getExactWidth();
            } else {
                $exact_width = '';
            }
            $exactWidthArr[$product->getId()] = $exact_width;
        }
        return $exactWidthArr;
    }

    public function getExactLength($subject)
    {
        $exactLengthArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getExactLength())) {
                $exact_length = $product->getExactLength();
            } else {
                $exact_length = '';
            }
            $exactLengthArr[$product->getId()] = $exact_length;
        }
        return $exactLengthArr;
    }

    public function getExactHeight($subject)
    {
        $exactHeightArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getExactHeight())) {
                $exact_height = $product->getExactHeight();
            } else {
                $exact_height = '';
            }
            $exactHeightArr[$product->getId()] = $exact_height;
        }
        return $exactHeightArr;
    }

    public function getCoveragePerCase($subject)
    {
        $coveragePerCaseArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getCoveragePerCase())) {
                $coverage_per_case = $product->getCoveragePerCase();
            } else {
                $coverage_per_case = '';
            }
            $coveragePerCaseArr[$product->getId()] = $coverage_per_case;
        }
        return $coveragePerCaseArr;
    }

    public function getMinTileQuantity($subject)
    {
        $tileMinQty = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getMinTileQuantity())) {
                $tile_min_qty = $product->getMinTileQuantity();
            } else {
                $tile_min_qty = 1;
            }
            
            $tileMinQty[$product->getId()] = $tile_min_qty;
        }
        return $tileMinQty;
    }

    public function getPdpShippingText($subject)
    {
        $pdpShippingTextArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getPdpShippingText())) {
                $pdp_shipping_text = $product->getPdpShippingText();
            } else {
                $pdp_shipping_text = '';
            }
            $pdpShippingTextArr[$product->getId()] = $pdp_shipping_text;
        }
        return $pdpShippingTextArr;
    }

    public function getExpectedDaysShip($subject)
    {
        $pdpShippingTextArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            $expected_days_ship = $this->flooringCalcHelper->getExpectedShipDate($product->getId());
            $pdpShippingTextArr[$product->getId()] = $expected_days_ship;
        }
        return $pdpShippingTextArr;
    }

    public function getQuickShip($subject)
    {
        $pdpQuickShipArr = [];
        foreach ($subject->getAllowProducts() as $product) {
            if (!empty($product->getQuickShipForAssociatedProduct())) {
                $pdp_quick_ship = $product->getQuickShipForAssociatedProduct();
            } else {
                $pdp_quick_ship = '';
            }
            $pdpQuickShipArr[$product->getId()] = $pdp_quick_ship;
        }
        return $pdpQuickShipArr;
    }
}
