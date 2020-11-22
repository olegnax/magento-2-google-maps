<?php /** @noinspection PhpDeprecationInspection */

/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 */

namespace Olegnax\GoogleMap\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class YesNo implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $array = $this->toArray();
        foreach ($array as $key => $value) {
            $optionArray[] = ['value' => $key, 'label' => $value];
        }

        return $optionArray;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            '' => __('Use Global Settings'),
            'yes' => __('Yes'),
            'no' => __('No'),
        ];
    }
}
