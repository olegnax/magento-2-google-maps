<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Olegnax\GoogleMap\Model\Cache;

use Magento\Framework\App\Cache\Type\FrontendPool;
use Magento\Framework\Cache\Frontend\Decorator\TagScope;

class GoogleMapLocations extends TagScope
{
    const TYPE_IDENTIFIER = 'ox_googlemap_locations';
    const CACHE_TAG = 'OX_GOOGLEMAP_LOCATIONS';
    const CACHE_KEY_PREFIX = 'ox_googlemap_location';
    const CACHE_LIFETIME = 86400;

    /**
     * @param FrontendPool $cacheFrontendPool
     */
    public function __construct(
        FrontendPool $cacheFrontendPool
    ) {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }
}
