<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Model;

use Exception;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Olegnax\GoogleMap\Api\Data\LocationInterface;
use Olegnax\GoogleMap\Api\Data\LocationInterfaceFactory;
use Olegnax\GoogleMap\Api\Data\LocationSearchResultsInterfaceFactory;
use Olegnax\GoogleMap\Api\LocationRepositoryInterface;
use Olegnax\GoogleMap\Model\ResourceModel\Location as ResourceLocation;
use Olegnax\GoogleMap\Model\ResourceModel\Location\CollectionFactory as LocationCollectionFactory;

class LocationRepository implements LocationRepositoryInterface
{

    protected $searchResultsFactory;
    protected $locationFactory;
    protected $resource;
    protected $dataLocationFactory;
    protected $extensibleDataObjectConverter;
    protected $dataObjectProcessor;
    protected $dataObjectHelper;
    protected $locationCollectionFactory;
    protected $extensionAttributesJoinProcessor;
    private $collectionProcessor;
    private $storeManager;

    /**
     * @param ResourceLocation $resource
     * @param LocationFactory $locationFactory
     * @param LocationInterfaceFactory $dataLocationFactory
     * @param LocationCollectionFactory $locationCollectionFactory
     * @param LocationSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceLocation $resource,
        LocationFactory $locationFactory,
        LocationInterfaceFactory $dataLocationFactory,
        LocationCollectionFactory $locationCollectionFactory,
        LocationSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->locationFactory = $locationFactory;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataLocationFactory = $dataLocationFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        LocationInterface $location
    ) {
        /* if (empty($location->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $location->setStoreId($storeId);
        } */

        $locationData = $this->extensibleDataObjectConverter->toNestedArray(
            $location,
            [],
            LocationInterface::class
        );

        $locationModel = $this->locationFactory->create()->setData($locationData);

        try {
            $this->resource->save($locationModel);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the location: %1',
                $exception->getMessage()
            ));
        }
        return $locationModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->locationCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            LocationInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($locationId)
    {
        return $this->delete($this->get($locationId));
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        LocationInterface $location
    ) {
        try {
            $locationModel = $this->locationFactory->create();
            $this->resource->load($locationModel, $location->getLocationId());
            $this->resource->delete($locationModel);
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Location: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($locationId)
    {
        $location = $this->locationFactory->create();
        $this->resource->load($location, $locationId);
        if (!$location->getId()) {
            throw new NoSuchEntityException(__('Location with id "%1" does not exist.', $locationId));
        }
        return $location->getDataModel();
    }
}
