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

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for card search results.
 *
 * @api
 */
interface CardSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get cards.
     *
     * @return \ParadoxLabs\TokenBase\Api\Data\CardInterface[]
     */
    public function getItems();

    /**
     * Set cards.
     *
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
