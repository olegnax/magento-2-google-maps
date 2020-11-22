<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface LocationSearchResultsInterface extends SearchResultsInterface
{

    /**
     * Get Location list.
     * @return LocationInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param LocationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
