<?php


namespace Olegnax\GoogleMap\Ui\Component\Listing\Column;

use Magento\Store\Ui\Component\Listing\Column\Store;

class LocationStore extends Store
{
    /**
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $item[$this->storeKey] = empty($item[$this->storeKey]) ? '0' : $item[$this->storeKey];

        if (!is_array($item[$this->storeKey])) {
            $item[$this->storeKey] = explode(',', $item[$this->storeKey]);
        }

        return parent::prepareItem($item);
    }
}
