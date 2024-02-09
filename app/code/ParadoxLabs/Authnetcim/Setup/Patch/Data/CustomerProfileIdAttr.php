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

namespace ParadoxLabs\Authnetcim\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class CustomerProfileIdAttr implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var \ParadoxLabs\TokenBase\Helper\Operation
     */
    private $helper;

    /**
     * @var \Magento\Eav\Api\AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository
     * @param \ParadoxLabs\TokenBase\Helper\Operation $helper
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory,
        \Magento\Eav\Api\AttributeRepositoryInterface $attributeRepository,
        \ParadoxLabs\TokenBase\Helper\Operation $helper
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeRepository = $attributeRepository;
        $this->helper = $helper;
    }

    /**
     * Run patch
     *
     * authnetcim_profile_id customer attribute: Stores CIM profile ID for each customer.
     *
     * @return $this
     */
    public function apply()
    {
        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $this->moduleDataSetup->startSetup();

        try {
            if ($customerSetup->getAttributeId('customer', 'authnetcim_profile_id') === false) {
                $this->addAttribute($customerSetup);
            } else {
                $this->updateAttribute($customerSetup);
            }
        } catch (\Exception $exception) {
            $this->helper->log('authnetcim', $exception->getMessage());
        }

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
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Exception
     */
    public function addAttribute(\Magento\Customer\Setup\CustomerSetup $customerSetup): void
    {
        $customerSetup->addAttribute(
            Customer::ENTITY,
            'authnetcim_profile_id',
            [
                'label' => 'Authorize.Net CIM: Profile ID',
                'type' => 'varchar',
                'input' => 'text',
                'default' => '',
                'position' => 70,
                'visible' => false,
                'required' => false,
                'system' => false,
                'user_defined' => false,
                'visible_on_front' => false,
            ]
        );

        $profileIdAttr = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            'authnetcim_profile_id'
        );

        $profileIdAttr->addData(
            [
                'attribute_set_id' => $customerSetup->getDefaultAttributeSetId(Customer::ENTITY),
                'attribute_group_id' => $customerSetup->getDefaultAttributeGroupId(Customer::ENTITY),
                'used_in_forms' => [],
            ]
        );

        $this->attributeRepository->save($profileIdAttr);
    }

    /**
     * @param \Magento\Customer\Setup\CustomerSetup $customerSetup
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function updateAttribute(\Magento\Customer\Setup\CustomerSetup $customerSetup): void
    {
        /**
         * is_system must be 0 in order for attribute values to save.
         */
        $attribute = $customerSetup->getAttribute(Customer::ENTITY, 'authnetcim_profile_id');
        if (!isset($attribute['is_system']) || $attribute['is_system'] != 0) {
            $customerSetup->updateAttribute(
                Customer::ENTITY,
                $attribute['attribute_id'],
                'is_system',
                0
            );

            $profileIdAttr = $customerSetup->getEavConfig()->getAttribute(
                Customer::ENTITY,
                'authnetcim_profile_id'
            );

            $profileIdAttr->addData(
                [
                    'attribute_set_id' => $customerSetup->getDefaultAttributeSetId(Customer::ENTITY),
                    'attribute_group_id' => $customerSetup->getDefaultAttributeGroupId(Customer::ENTITY),
                    'used_in_forms' => [],
                ]
            );

            $this->attributeRepository->save($profileIdAttr);
        }

        /**
         * is_visible should be 0 to prevent the attribute showing on forms.
         */
        if (!isset($attribute['is_visible']) || $attribute['is_visible'] != 0) {
            $customerSetup->updateAttribute(
                Customer::ENTITY,
                $attribute['attribute_id'],
                'is_visible',
                0
            );
        }

        /**
         * ... is_user_defined should be 0 to prevent the attribute showing on forms.
         */
        if (!isset($attribute['is_user_defined']) || $attribute['is_user_defined'] != 0) {
            $customerSetup->updateAttribute(
                Customer::ENTITY,
                $attribute['attribute_id'],
                'is_user_defined',
                0
            );
        }
    }

    /**
     * Rollback all changes, done by this patch
     *
     * @return void
     */
    public function revert()
    {
        /** @var \Magento\Customer\Setup\CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $this->moduleDataSetup->startSetup();

        $customerSetup->removeAttribute(
            Customer::ENTITY,
            'authnetcim_profile_id'
        );

        $this->moduleDataSetup->endSetup();
    }
}
