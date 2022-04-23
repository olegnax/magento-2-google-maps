<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Model\ResourceModel;

use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Location extends AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('olegnax_googlemap_location', 'location_id');
    }

    /**
     * Perform actions before object save
     *
     * @param AbstractModel|DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $data = $object->getData('store_id');
        if (empty($data)) {
            $data = [0];
        }
        if (is_array($data)) {
            $data = implode(',', $data);
            $object->setData('store_id', $data);
        }

        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object load
     *
     * @param AbstractModel|DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _afterLoad(AbstractModel $object)
    {
        $data = $object->getData('store_id');
        if (!empty($data) && !is_array($data)) {
            $data = explode(',', $data);
            $object->setData('store_id', $data);
        }
        return parent::_afterLoad($object);
    }
}
