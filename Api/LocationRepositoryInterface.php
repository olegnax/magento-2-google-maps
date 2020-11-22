<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Olegnax\GoogleMap\Api\Data\LocationInterface;
use Olegnax\GoogleMap\Api\Data\LocationSearchResultsInterface;

interface LocationRepositoryInterface
{

    /**
     * Save Location
     * @param LocationInterface $location
     * @return LocationInterface
     * @throws LocalizedException
     */
    public function save(
        LocationInterface $location
    );

    /**
     * Retrieve Location
     * @param string $locationId
     * @return LocationInterface
     * @throws LocalizedException
     */
    public function get($locationId);

    /**
     * Retrieve Location matching the specified criteria.
     * @param SearchCriteriaInterface $searchCriteria
     * @return LocationSearchResultsInterface
     * @throws LocalizedException
     */
    public function getList(
        SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Location
     * @param LocationInterface $location
     * @return bool true on success
     * @throws LocalizedException
     */
    public function delete(
        LocationInterface $location
    );

    /**
     * Delete Location by ID
     * @param string $locationId
     * @return bool true on success
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function deleteById($locationId);
}
