<?php
/**
 * PriceCalculation
 *
 * PHP version 7
 *
 * @category  PHP
 * @package   Dw_PriceCalculation
 * @author    Rahul
 * @copyright 2021 Dotcom. All rights reserved.
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0
 * @link      https://dotcomweavers.com/
 */
namespace Dcw\CustomAttribute\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Config as EavConfig;

class AddDefaultDesignTileAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    private const GROUPCODE = "General";

    private const ATTRIBUTE_SET_ID = "Default";

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory,
        EavConfig $eavConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * Run code inside patch
     *
     * @return $this
     */
    public function apply()
    {
        
        $this->moduleDataSetup->startSetup();
        $attribute_code = 'default_design_tile';
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $attribute_code);
        if (!$attribute || !$attribute->getAttributeId()) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                $attribute_code,
                [
                    'label' => 'Default Design Tile',
                    'user_defined' => true,
                    'required' => false,
                    'input' => 'boolean',
                    'type' => 'int',
                    'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                    'visible_on_front' => false,
                    'unique' => false,
                    'used_in_product_listing' => true
                ]
            );
            $eavSetup->addAttributeToGroup(
                Product::ENTITY,
                self::ATTRIBUTE_SET_ID,
                self::GROUPCODE,
                $attribute_code,
                810
            );
        }
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Get array of patches that have to be executed prior to this.
     *
     * Example of implementation:
     *
     * [
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch1::class,
     *      \Vendor_Name\Module_Name\Setup\Patch\Patch2::class
     * ]
     *
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * Get aliases (previous names) for the patch.
     *
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }
}
