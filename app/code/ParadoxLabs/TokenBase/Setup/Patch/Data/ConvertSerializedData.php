<?php declare(strict_types=1);
/**
 * Copyright © 2015-present ParadoxLabs, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Need help? Try our knowledgebase and support system:
 * @link https://support.paradoxlabs.com
 */

namespace ParadoxLabs\TokenBase\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class ConvertSerializedData implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var \Magento\Framework\Unserialize\Unserialize
     */
    private $unserialize;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Framework\Unserialize\Unserialize $unserialize
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Framework\Unserialize\Unserialize $unserialize
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->unserialize = $unserialize;
    }

    /**
     * Run patch
     *
     * Convert serialized data to JSON. Magento did this across the core in 2.2.
     *
     * @return $this
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->convertSerializedColsToJson();
        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * Get array of patches that have to be executed prior to this.
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

    /**
     * This version associates the patch with Magento setup version.
     *
     * @return string
     */
    public static function getVersion()
    {
        return '4.0.0';
    }

    /**
     * Convert serialized data to JSON. Magento did this across the core in 2.2.
     *
     * @return $this
     */
    public function convertSerializedColsToJson()
    {
        $setup = $this->moduleDataSetup;

        /**
         * Fix paradoxlabs_stored_card.address and paradoxlabs_stored_card.additional
         */
        $select = $setup->getConnection()->select()
                        ->from($setup->getTable('paradoxlabs_stored_card'), 'id')
                        ->columns(['address', 'additional'])
                        ->where('additional LIKE ?', 'a:%')
                        ->orWhere('address LIKE ?', 'a:%');

        $items = $setup->getConnection()->fetchAll($select);
        foreach ($items as $item) {
            // For each result, convert the two columns and update the row.
            $setup->getConnection()->update(
                $setup->getTable('paradoxlabs_stored_card'),
                [
                    'additional' => $this->jsonify($item['additional']),
                    'address'    => $this->jsonify($item['address']),
                ],
                ['id = ?' => $item['id']]
            );
        }

        return $this;
    }

    /**
     * Convert serialized string to JSON.
     *
     * @param string $string
     * @return string
     */
    private function jsonify($string)
    {
        $string = (string)$string;

        // We're using serialize for array storage--all valid data will start with 'a:'.
        // If our string starts with 'a', attempt to decode, otherwise pass through as-is.
        if ($string[0] === 'a') {
            if (strpos($string, 'O:27:"Mage_Customer_Model_Address"') !== false) {
                // Sometimes addresses from M1 were legitimately stored with customer object. Codepath unclear.
                // Can't skip them entirely, can't easily clean, but try to look for it specifically.
                $array = [];
            } else {
                $array = $this->unserialize->unserialize($string);
            }

            return json_encode($array);
        }

        return $string;
    }
}
