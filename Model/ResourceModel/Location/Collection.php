<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 * @noinspection PhpUndefinedClassInspection
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Model\ResourceModel\Location;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\GoogleMap\Model\ResourceModel\Location;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'location_id';
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * @var int
     */
    protected $_storeId;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param StoreManagerInterface $storeManager
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->_storeManager = $storeManager;
    }

    /**
     * Add store availability filter. Include availability product
     * for store website
     *
     * @param null|int|StoreInterface $store
     * @return $this
     * @throws NoSuchEntityException
     */
    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        $store = $this->_storeManager->getStore($store);

        $this->getSelect()->where(
            "(`store_id` = 0 OR
        `store_id` LIKE '?,%' OR
        `store_id` = ? OR
        `store_id` LIKE '%,?' OR
        `store_id` LIKE '%,?,%')",
            $store->getId(),
            'int'
        );

        return $this;
    }

    /**
     * Return current store id
     *
     * @return int|null
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Set store scope
     *
     * @param int|StoreInterface $store
     * @return $this
     */
    public function setStoreId($store)
    {
        if ($store instanceof StoreInterface) {
            $store = $store->getId();
        }
        $this->_storeId = (int)$store;
        return $this;
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Olegnax\GoogleMap\Model\Location::class,
            Location::class
        );
    }
}
