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
use Olegnax\GoogleMap\Model\Data\Location as DataLocation;

class MarkerStyle implements ArrayInterface
{
    /**
     * @var string
     */
    const TYPE_DEFAULT = 'default';
    const TYPE_ATHLETE = 'athlete';
    const TYPE_CIRCLE = 'circle';
    const TYPE_CIRCLES = 'circles';
    const TYPE_PIN_HOLE = 'pin-hole';
    const TYPE_FLAG = 'flag';
    const TYPE_PIN_CIRCLE = 'pin-circle';
    const TYPE_SQUARE = 'square';
    const TYPE_CUSTOM = 'custom';
    /**
     * @var array
     */
    private $array;
    /**
     * @var array
     */
    private $iconArray;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        if (empty($this->array)) {
            $this->array = [];
            foreach ($this->toOptionArray() as $item) {
                $this->array[$item['value']] = $item['label'];
            }
        }

        return $this->array;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => static::TYPE_DEFAULT,
                'label' => __('Default Marker'),
            ],
            [
                'value' => static::TYPE_ATHLETE,
                'label' => __('Athlete2 Style'),
                'icon' => [
                    'prepare' => 'prepareTemplateSvg',
                    'scaledSize' => [
                        34,
                        48,
                    ],
                ],
            ],
            [
                'value' => static::TYPE_CIRCLE,
                'label' => __('Circle (with Image)'),
                'icon' => [
                    'prepare' => 'prepareTemplateSvg',
                    'scaledSize' => [
                        70,
                        70,
                    ],
                ],
            ],
            [
                'value' => static::TYPE_SQUARE,
                'label' => __('Square (with Image)'),
                'icon' => [
                    'prepare' => 'prepareTemplateSvg',
                    'scaledSize' => [
                        70,
                        79,
                    ],
                ],
            ],
            [
                'value' => static::TYPE_CIRCLES,
                'label' => __('Circle and Dot Inside'),
                'icon' => [
                    'prepare' => 'prepareTemplateSvg',
                    'scaledSize' => [
                        36,
                        36,
                    ],
                ],
            ],
            [
                'value' => static::TYPE_PIN_HOLE,
                'label' => __('Pin with Hole'),
                'icon' => [
                    'prepare' => 'prepareTemplateSvg',
                    'scaledSize' => [
                        34,
                        41,
                    ],
                ],
            ],
            [
                'value' => static::TYPE_FLAG,
                'label' => __('Flag'),
                'icon' => [
                    'prepare' => 'prepareTemplateSvg',
                    'scaledSize' => [
                        35,
                        35,
                    ],
                ],
            ],
            [
                'value' => static::TYPE_PIN_CIRCLE,
                'label' => __('Circle Pin'),
                'icon' => [
                    'prepare' => 'prepareTemplateSvg',
                    'scaledSize' => [
                        36,
                        51,
                    ],
                ],
            ],
            [
                'value' => static::TYPE_CUSTOM,
                'label' => __('Custom Image Only'),
                'icon' => [
                    'data' => DataLocation::MARKER_IMAGE,
                    'prepare' => 'prepareLocalImage',
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function toIconArray()
    {
        if (empty($this->iconArray)) {
            $this->iconArray = [];
            foreach ($this->toOptionArray() as $item) {
                $this->iconArray[$item['value']] = isset($item['icon']) ? $item['icon'] : '';
            }
        }
        return $this->iconArray;
    }
}
