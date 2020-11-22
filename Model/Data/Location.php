<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 * @noinspection PhpDeprecationInspection
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Olegnax\GoogleMap\Api\Data\LocationInterface;

class Location extends AbstractExtensibleObject implements LocationInterface
{

    /**
     * Get location_id
     * @return string|null
     */
    public function getLocationId()
    {
        return $this->_get(self::LOCATION_ID);
    }

    /**
     * Set location_id
     * @param string $locationId
     * @return LocationInterface
     */
    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return LocationInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return LocationInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId()
    {
        return $this->_get(self::STORE_ID);
    }

    /**
     * Set store_id
     * @param string $storeId
     * @return LocationInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get latitude
     * @return string|null
     */
    public function getLatitude()
    {
        return $this->_get(self::LATITUDE);
    }

    /**
     * Set latitude
     * @param string $latitude
     * @return LocationInterface
     */
    public function setLatitude($latitude)
    {
        return $this->setData(self::LATITUDE, $latitude);
    }

    /**
     * Get longitude
     * @return string|null
     */
    public function getLongitude()
    {
        return $this->_get(self::LONGITUDE);
    }

    /**
     * Set longitude
     * @param string $longitude
     * @return LocationInterface
     */
    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
    }

    /**
     * Get marker_style
     * @return string|null
     */
    public function getMarkerStyle()
    {
        return $this->_get(self::MARKER_STYLE);
    }

    /**
     * Set marker_style
     * @param string $markerStyle
     * @return LocationInterface
     */
    public function setMarkerStyle($markerStyle)
    {
        return $this->setData(self::MARKER_STYLE, $markerStyle);
    }

    /**
     * Get marker_color_1
     * @return string|null
     */
    public function getMarkerColor1()
    {
        return $this->_get(self::MARKER_COLOR_1);
    }

    /**
     * Set marker_color_1
     * @param string $markerColor1
     * @return LocationInterface
     */
    public function setMarkerColor1($markerColor1)
    {
        return $this->setData(self::MARKER_COLOR_1, $markerColor1);
    }

    /**
     * Get marker_color_2
     * @return string|null
     */
    public function getMarkerColor2()
    {
        return $this->_get(self::MARKER_COLOR_2);
    }

    /**
     * Set marker_color_2
     * @param string $markerColor2
     * @return LocationInterface
     */
    public function setMarkerColor2($markerColor2)
    {
        return $this->setData(self::MARKER_COLOR_2, $markerColor2);
    }

    /**
     * Get marker_image
     * @return string|null
     */
    public function getMarkerImage()
    {
        return $this->_get(self::MARKER_IMAGE);
    }

    /**
     * Set marker_image
     * @param string $markerImage
     * @return LocationInterface
     */
    public function setMarkerImage($markerImage)
    {
        return $this->setData(self::MARKER_IMAGE, $markerImage);
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription()
    {
        return $this->_get(self::DESCRIPTION);
    }

    /**
     * Set description
     * @param string $description
     * @return LocationInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get marker_size
     * @return string|null
     */
    public function getMarkerSize()
    {
        return $this->_get(self::MARKER_SIZE);
    }

    /**
     * Set marker_size
     * @param string $markerSize
     * @return LocationInterface
     */
    public function setMarkerSize($markerSize)
    {
        return $this->setData(self::MARKER_SIZE, $markerSize);
    }

    /**
     * Get marker_shadow_color
     * @return string
     */
    public function getMarkerShadowColor()
    {
        return $this->_get(self::MARKER_SHADOW_COLOR);
    }

    /**
     * Set marker_shadow_color
     * @param string $markerShadowColor
     * @return LocationInterface
     */
    public function setMarkerShadowColor($markerShadowColor)
    {
        return $this->setData(self::MARKER_SHADOW_COLOR, $markerShadowColor);
    }
}
