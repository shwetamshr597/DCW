<?php
namespace Magecomp\Instagrampro\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;


class Addcatalogattr implements DataPatchInterface
{
    private $customerSetupFactory;
    private $eavSetupFactory;
    private $categorySetupFactory;

    public function __construct(ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory, 
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->setup = $moduleDataSetup;
    }

    public function apply()
    {
        //$this->setup->startSetup();

        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        
        $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'instagrampro_source',
                [
                    'type' => 'varchar',
                    'label' => 'Used Instagram Tags',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'input' => 'multiselect',
                    'source' => 'Magecomp\Instagrampro\Model\Source\Instagrampro',
                    'required' => false,
                    'sort_order' => 6,
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'apply_to' => 'simple,configurable,virtual',
                    'group' => 'Instagram',
                    'searchable'        => false,
                    'filterable'        => false
                ]
            );
            
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'instagrampro_source_user',
                [
                    'type' => 'varchar',
                    'label' => 'Used Instagram Users',
                    'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                    'input' => 'multiselect',
                    'source' => 'Magecomp\Instagrampro\Model\Source\Instagrampro\User',
                    'required' => false,
                    'sort_order' => 7,
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                    'apply_to' => 'simple,configurable,virtual',
                    'group' => 'Instagram',
                    'searchable'        => false,
                    'filterable'        => false
                ]
            );

        //$this->setup->endSetup();

    }
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
    
}
