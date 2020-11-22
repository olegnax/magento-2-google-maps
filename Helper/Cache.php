<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Olegnax\GoogleMap\Helper;

use InvalidArgumentException;
use Magento\Framework\App\Cache\State;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\GoogleMap\Model\Cache\GoogleMapLocations;
use Olegnax\GoogleMap\Model\Location;

class Cache extends AbstractHelper
{
    /**
     * @var State
     */
    protected $cacheState;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Cache
     */
    protected $cache;
    /**
     * @var Json
     */
    protected $json;

    /**
     * @param Context $context
     * @param \Magento\Framework\App\Cache $cache
     * @param State $cacheState
     * @param StoreManagerInterface $storeManager
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        \Magento\Framework\App\Cache $cache,
        State $cacheState,
        StoreManagerInterface $storeManager,
        Json $json = null
    ) {
        $this->cache = $cache;
        $this->cacheState = $cacheState;
        $this->storeManager = $storeManager;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context);
    }

    /**
     * @param Location $location
     * @return bool|string
     * @throws NoSuchEntityException
     */
    public function load(Location $location)
    {
        if (!$this->isEnabled()) {
            return false;
        }

        $cacheId = $this->getCacheKey($location->getData());
        $_data = $this->cache->load($cacheId);
        if (!$_data) {
            return false;
        }
        try {
            $data = $this->json->unserialize($_data);
        } catch (InvalidArgumentException $exception) {
            $data = $_data;
        }

        return $data;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->cacheState->isEnabled(GoogleMapLocations::TYPE_IDENTIFIER);
    }

    /**
     * @param array $key
     * @return string
     * @throws NoSuchEntityException
     */
    public function getCacheKey(array $key)
    {
        $key = array_values($key);  // ignore array keys
        $key[] = $this->storeManager->getStore()->getId();
        $key = implode('|', $key);
        $key = sha1($key); // use hashing to hide potentially private data
        return GoogleMapLocations::CACHE_KEY_PREFIX . $key;
    }

    /**
     * @param array $data
     * @param Location $location
     * @param int $cacheLifetime
     * @return bool
     * @throws NoSuchEntityException
     */
    public function save($data, Location $location, $cacheLifetime = 0)
    {
        if (!$this->isEnabled()) {
            return false;
        }
        $data = $this->json->serialize($data);
        $cacheId = $this->getCacheKey($location->getData());
        return $this->cache->save(
            $data,
            $cacheId,
            [GoogleMapLocations::CACHE_TAG],
            0 < $cacheLifetime ? $cacheLifetime : GoogleMapLocations::CACHE_LIFETIME
        );
    }
}
