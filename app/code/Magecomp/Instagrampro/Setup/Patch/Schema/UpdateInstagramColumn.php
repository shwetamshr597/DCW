<?php
/**
* Copyright © 2019 Magenest. All rights reserved.
* See COPYING.txt for license details.
*/
namespace Magecomp\Instagrampro\Setup\Patch\Schema;


use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class UpdateInstagramColumn implements SchemaPatchInterface
{
   private $moduleDataSetup;


   public function __construct(
       ModuleDataSetupInterface $moduleDataSetup
   ) {
       $this->moduleDataSetup = $moduleDataSetup;
   }


   public static function getDependencies()
   {
       return [];
   }


   public function getAliases()
   {
       return [];
   }


   public function apply()
   {
       $this->moduleDataSetup->startSetup();


           $this->moduleDataSetup->getConnection()->changeColumn(
               $this->moduleDataSetup->getTable('instagrampro_image'),
               'standard_resolution_url',
               'standard_resolution_url',
               [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M'
               ]
            );

            $this->moduleDataSetup->getConnection()->changeColumn(
               $this->moduleDataSetup->getTable('instagrampro_image'),
               'thumbnail_url',
               'thumbnail_url',
               [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M'
               ]
            );

             $this->moduleDataSetup->getConnection()->changeColumn(
               $this->moduleDataSetup->getTable('instagrampro_image'),
               'caption_text',
               'caption_text',
               [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => '2M'
               ]
            );

        

       $this->moduleDataSetup->endSetup();
   }
}