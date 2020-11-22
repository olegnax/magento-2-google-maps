<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Olegnax\GoogleMap\Api\Data\LocationInterface;
use Olegnax\GoogleMap\Api\Data\LocationInterfaceFactory;
use Olegnax\GoogleMap\Model\ResourceModel\Location\Collection;

/**
 * @method string getDescription();
 * @method float getLatitude();
 * @method int getLocationId();
 * @method float getLongitude();
 * @method string getMarkerColor1();
 * @method string getMarkerColor2();
 * @method string getMarkerImage();
 * @method string getMarkerShadowColor();
 * @method string getMarkerStyle();
 * @method string getName();
 * @method bool getStatus();
 * @method int[] getStoreId();
 * @method void setDescription($description);
 * @method void setLatitude($latitude);
 * @method void setLocationId($locationId);
 * @method void setLongitude($longitude);
 * @method void setMarkerColor1($markerColor1);
 * @method void setMarkerColor2($markerColor2);
 * @method void setMarkerImage($markerImage);
 * @method void setMarkerShadowColor($markerShadowColor);
 * @method void setMarkerSize($markerSize);
 * @method void setMarkerStyle($markerStyle);
 * @method void setName($name);
 * @method void setStatus($status);
 * @method void setStoreId($storeId);
 */
class Location extends AbstractModel
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var string
     */
    protected $_eventPrefix = 'olegnax_googlemap_location';

    /**
     * @var LocationInterfaceFactory
     */
    protected $locationDataFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param LocationInterfaceFactory $locationDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\Location $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LocationInterfaceFactory $locationDataFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceModel\Location $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        $this->locationDataFactory = $locationDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve location model with location data
     * @return LocationInterface
     */
    public function getDataModel()
    {
        $locationData = $this->getData();

        $locationDataObject = $this->locationDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $locationDataObject,
            $locationData,
            LocationInterface::class
        );

        return $locationDataObject;
    }

    /**
     * @param string $key
     * @param null $index
     * @return array|mixed|string|null
     */
    public function getData($key = '', $index = null)
    {
        $default = [
            'marker_color_1' => '#000000',
            'marker_color_2' => '#f8e749',
            'marker_shadow_color' => 'rbga(0,0,0,0.2)',
        ];
        if (array_key_exists($key, $default)) {
            return parent::getData($key, $index) ?: $default[$key];
        }
        return parent::getData($key, $index);
    }

    /**
     * @return float
     */
    public function getMarkerSize()
    {
        $value = (float)$this->getData(Data\Location::MARKER_SIZE);
        if (0 >= $value) {
            $value = 1;
        }
        return $value;
    }
}
