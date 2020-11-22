<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface LocationInterface extends ExtensibleDataInterface
{

    const STATUS = 'status';
    const STORE_ID = 'store_id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const LOCATION_ID = 'location_id';
    const LATITUDE = 'latitude';
    const LONGITUDE = 'longitude';
    const MARKER_STYLE = 'marker_style';
    const MARKER_SIZE = 'marker_size';
    const MARKER_IMAGE = 'marker_image';
    const MARKER_COLOR_1 = 'marker_color_1';
    const MARKER_COLOR_2 = 'marker_color_2';
    const MARKER_SHADOW_COLOR = 'marker_shadow_color';

    /**
     * Get location_id
     * @return string|null
     */
    public function getLocationId();

    /**
     * Set location_id
     * @param string $locationId
     * @return LocationInterface
     */
    public function setLocationId($locationId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return LocationInterface
     */
    public function setName($name);

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return LocationInterface
     */
    public function setStatus($status);

    /**
     * Get store_id
     * @return string|null
     */
    public function getStoreId();

    /**
     * Set store_id
     * @param string $storeId
     * @return LocationInterface
     */
    public function setStoreId($storeId);

    /**
     * Get latitude
     * @return string|null
     */
    public function getLatitude();

    /**
     * Set latitude
     * @param string $latitude
     * @return LocationInterface
     */
    public function setLatitude($latitude);

    /**
     * Get longitude
     * @return string|null
     */
    public function getLongitude();

    /**
     * Set longitude
     * @param string $longitude
     * @return LocationInterface
     */
    public function setLongitude($longitude);

    /**
     * Get marker_style
     * @return string|null
     */
    public function getMarkerStyle();

    /**
     * Set marker_style
     * @param string $markerStyle
     * @return LocationInterface
     */
    public function setMarkerStyle($markerStyle);

    /**
     * Get marker_color_1
     * @return string|null
     */
    public function getMarkerColor1();

    /**
     * Set marker_color_1
     * @param string $markerColor1
     * @return LocationInterface
     */
    public function setMarkerColor1($markerColor1);

    /**
     * Get marker_color_2
     * @return string|null
     */
    public function getMarkerColor2();

    /**
     * Set marker_color_2
     * @param string $markerColor2
     * @return LocationInterface
     */
    public function setMarkerColor2($markerColor2);

    /**
     * Get marker_image
     * @return string|null
     */
    public function getMarkerImage();

    /**
     * Set marker_image
     * @param string $markerImage
     * @return LocationInterface
     */
    public function setMarkerImage($markerImage);

    /**
     * Get description
     * @return string|null
     */
    public function getDescription();

    /**
     * Set description
     * @param string $description
     * @return LocationInterface
     */
    public function setDescription($description);

    /**
     * Get marker_size
     * @return string|null
     */
    public function getMarkerSize();

    /**
     * Set marker_size
     * @param string $markerSize
     * @return LocationInterface
     */
    public function setMarkerSize($markerSize);

    /**
     * Get marker_shadow_color
     * @return string|null
     */
    public function getMarkerShadowColor();

    /**
     * Set marker_shadow_color
     * @param string $markerShadowColor
     * @return LocationInterface
     */
    public function setMarkerShadowColor($markerShadowColor);
}
