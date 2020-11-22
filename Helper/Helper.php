<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Helper;

use Olegnax\Core\Helper\Helper as CoreHelper;

class Helper extends CoreHelper
{
    const CONFIG_MODULE = 'olegnax_googlemaps';
    const XML_PATH_ENABLE = 'general/enabled';

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->getModuleConfig(static::XML_PATH_ENABLE);
    }
}
