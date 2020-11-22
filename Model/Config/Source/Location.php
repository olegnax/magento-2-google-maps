<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Model\Config\Source;

use Magento\Framework\Data\Collection;
use Magento\Framework\Option\ArrayInterface;
use Olegnax\GoogleMap\Model\ResourceModel\Location\CollectionFactory;

class Location implements ArrayInterface
{

    /**
     * @var
     */
    protected $collection;

    /**
     * Location constructor.
     * @param CollectionFactory $collection
     */
    public function __construct(
        CollectionFactory $collection
    ) {
        $this->collection = $collection->create()->addFieldToSelect('*')->setOrder('name', Collection::SORT_ORDER_ASC);
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->collection as $location) {
            $options[] = [
                'value' => $location->getLocationId(),
                'label' => $location->getName(),
            ];
        }

        return $options;
    }
}
