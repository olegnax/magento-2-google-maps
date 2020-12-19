<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Olegnax\GoogleMap\Ui\Component\Form\Field;

use Magento\Ui\Component\Form\Field;

if (class_exists('\Magento\Store\Ui\Component\Form\Field\StoreView')) {
    class StoreView extends \Magento\Store\Ui\Component\Form\Field\StoreView
    {

    }
} else {
    class StoreView extends Field
    {

    }
}