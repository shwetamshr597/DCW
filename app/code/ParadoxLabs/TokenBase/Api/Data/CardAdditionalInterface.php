<?php
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
namespace ParadoxLabs\TokenBase\Api\Data;

/**
 * Interface CardAdditionalInterface
 *
 * Info interface: Exposes data keys and extensibility to the API.
 */
interface CardAdditionalInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * @return mixed
     */
    public function getCcType();

    /**
     * @param mixed $ccType
     * @return mixed
     */
    public function setCcType($ccType);

    /**
     * @return mixed
     */
    public function getCcCid();

    /**
     * @param mixed $ccCid
     * @return mixed
     */
    public function setCcCid($ccCid);

    /**
     * @return mixed
     */
    public function getCcNumber();

    /**
     * @param mixed $ccNumber
     * @return mixed
     */
    public function setCcNumber($ccNumber);

    /**
     * @return mixed
     */
    public function getCcLast4();

    /**
     * @param mixed $ccLast4
     * @return mixed
     */
    public function setCcLast4($ccLast4);

    /**
     * @return mixed
     */
    public function getCcExpYear();

    /**
     * @param mixed $ccExpYear
     * @return mixed
     */
    public function setCcExpYear($ccExpYear);

    /**
     * @return mixed
     */
    public function getCcExpMonth();

    /**
     * @param mixed $ccExpMonth
     * @return mixed
     */
    public function setCcExpMonth($ccExpMonth);

    /**
     * @return mixed
     */
    public function getCcBin();

    /**
     * @param mixed $ccBin
     * @return mixed
     */
    public function setCcBin($ccBin);

    /**
     * @return mixed
     */
    public function getCcCountry();

    /**
     * @param $ccCountry
     * @return mixed
     */
    public function setCcCountry($ccCountry);

    /**
     * @return mixed
     */
    public function getAcceptjsKey();

    /**
     * @param mixed $acceptjsKey
     * @return mixed
     */
    public function setAcceptjsKey($acceptjsKey);

    /**
     * @return mixed
     */
    public function getAcceptjsValue();

    /**
     * @param mixed $acceptjsValue
     * @return mixed
     */
    public function setAcceptjsValue($acceptjsValue);

    /**
     * @return mixed
     */
    public function getToken();

    /**
     * @param mixed $token
     * @return mixed
     */
    public function setToken($token);

    /**
     * @return mixed
     */
    public function getEcheckAccountName();

    /**
     * @param mixed $echeckAccountName
     * @return mixed
     */
    public function setEcheckAccountName($echeckAccountName);

    /**
     * @return mixed
     */
    public function getEcheckAccountNumberLast4();

    /**
     * @param mixed $echeckAccountNumberLast4
     * @return mixed
     */
    public function setEcheckAccountNumberLast4($echeckAccountNumberLast4);

    /**
     * @return mixed
     */
    public function getEcheckAccountType();

    /**
     * @param mixed $echeckAccountType
     * @return mixed
     */
    public function setEcheckAccountType($echeckAccountType);

    /**
     * @return mixed
     */
    public function getEcheckBankName();

    /**
     * @param mixed $echeckBankName
     * @return mixed
     */
    public function setEcheckBankName($echeckBankName);

    /**
     * @return mixed
     */
    public function getObject();

    /**
     * @param $object
     * @return mixed
     */
    public function setObject($object);

    /**
     * @return mixed
     */
    public function getFunding();

    /**
     * @param $funding
     * @return mixed
     */
    public function setFunding($funding);

    /**
     * @return mixed
     */
    public function getAddressLine1Check();

    /**
     * @param $addressLine1Check
     * @return mixed
     */
    public function setAddressLine1Check($addressLine1Check);

    /**
     * @return mixed
     */
    public function getCvcCheck();

    /**
     * @param $cvcCheck
     * @return mixed
     */
    public function setCvcCheck($cvcCheck);

    /**
     * @return mixed
     */
    public function getFingerprint();

    /**
     * @param $fingerprint
     * @return mixed
     */
    public function setFingerprint($fingerprint);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \ParadoxLabs\TokenBase\Api\Data\CardAdditionalExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \ParadoxLabs\TokenBase\Api\Data\CardAdditionalExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \ParadoxLabs\TokenBase\Api\Data\CardAdditionalExtensionInterface $extensionAttributes
    );
}
