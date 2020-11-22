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

use Magento\Framework\Option\ArrayInterface;

class MapType implements ArrayInterface
{
    const TYPE_DEFAULT = 'roadmap';
    const TYPE_HYBRID = 'hybrid';
    const TYPE_SATELLITE = 'satellite';
    const TYPE_TERRAIN = 'terrain';

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
        return [
            ['value' => static::TYPE_DEFAULT, 'label' => __('Roadmap')],
            ['value' => static::TYPE_SATELLITE, 'label' => __('Satellite')],
            ['value' => static::TYPE_TERRAIN, 'label' => __('Terrain')],
            ['value' => static::TYPE_HYBRID, 'label' => __('Hybrid')],
        ];
    }
}
