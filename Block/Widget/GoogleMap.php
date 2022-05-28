<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Block\Widget;

use Exception;
use finfo;
use InvalidArgumentException;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Widget\Block\BlockInterface;
use Olegnax\GoogleMap\Helper\Cache;
use Olegnax\GoogleMap\Helper\Helper;
use Olegnax\GoogleMap\Model\Config\Source\MapStyle;
use Olegnax\GoogleMap\Model\Config\Source\MarkerStyle;
use Olegnax\GoogleMap\Model\Data\Location as DataLocation;
use Olegnax\GoogleMap\Model\Location;
use Olegnax\GoogleMap\Model\ResourceModel\Location\Collection;
use Olegnax\GoogleMap\Model\ResourceModel\Location\CollectionFactory;

class GoogleMap extends Template implements BlockInterface
{
    /**
     * @var string
     */
    protected $_template = "widget/googlemap.phtml";
    /**
     * @var Context
     */
    protected $httpContext;
    /**
     * @var CollectionFactory
     */
    protected $locationFactory;
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var Escaper
     */
    protected $escaper;
    /**
     * @var string
     */
    protected $mapId;
    /**
     * @var MarkerStyle
     */
    protected $markerStyle;
    /**
     * @var Cache
     */
    protected $cacheHelper;
    /**
     * @var MapStyle
     */
    protected $mapStyle;

    /**
     * GoogleMap constructor.
     *
     * @param Template\Context $context
     * @param CollectionFactory $locationFactory
     * @param Context $httpContext
     * @param Helper $helper
     * @param Cache $cacheHelper
     * @param MarkerStyle $markerStyle
     * @param array $data
     * @param Json|null $json
     * @param Escaper|null $escaper
     */
    public function __construct(
        Template\Context $context,
        CollectionFactory $locationFactory,
        Context $httpContext,
        Helper $helper,
        Cache $cacheHelper,
        MarkerStyle $markerStyle,
        MapStyle $mapStyle,
        array $data = [],
        Json $json = null,
        Escaper $escaper = null
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->locationFactory = $locationFactory;
        $this->helper = $helper;
        $this->cacheHelper = $cacheHelper;
        $this->markerStyle = $markerStyle;
        $this->mapStyle = $mapStyle;
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        $this->escaper = $escaper ?: ObjectManager::getInstance()->get(Escaper::class);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->helper->isEnabled();
    }

    /**
     * @param array $newVal
     * @return array
     * @throws NoSuchEntityException
     */
    public function getCacheKeyInfo($newVal = [])
    {
        return array_merge([
            'OLEGNAX_GOOGLE_MAP_WIDGET',
            $this->_storeManager->getStore()->getId(),
            $this->_design->getDesignTheme()->getId(),
            $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_GROUP),
            $this->json->serialize($this->getData()),
        ], parent::getCacheKeyInfo(), $newVal);
    }

    /**
     * @param $widgetId
     * @param null $storeCode
     * @return string
     * @throws LocalizedException
     */
    public function getStyle($widgetId, $storeCode = null)
    {
        $widgetId = $widgetId ?: $this->getMapId();
        $content = $this->renderStyle($widgetId, $storeCode);
        if (!empty($content)) {
            $content = preg_replace('/[\r\n\t]/', ' ', $content);
            $content = preg_replace('/[\r\n\t ]{2,}/', ' ', $content);
            $content = preg_replace('/\s+([:;{}])\s+/', '\1', $content);
            $content = trim($content);
            return sprintf('<style>%s</style>', $content);
        }
        return '';
    }

    /**
     * @return string
     */
    public function getMapId()
    {
        if (!$this->mapId) {
            $this->mapId = preg_replace(
                '/[^a-zA-Z0-9_]/i',
                '_',
                'ox_' . $this->getNameInLayout()
            ) .
            substr(
                md5(mt_rand() . ''),
                -5
            );
        }
        return $this->mapId;
    }

    /**
     * @param string $widgetId
     * @param int $storeCode
     * @return string
     * @throws LocalizedException
     */
    public function renderStyle($widgetId, $storeCode = null)
    {
        $appearance = $this->getModuleConfig('appearance', $storeCode);

        foreach ($appearance as $key => $value) {
            $_value = $this->getData('appearance_' . $key);
            if (null !== $_value) {
                $appearance[$key] = $_value;
            }
        }

        $general = $this->getModuleConfig('general', $storeCode);
        foreach ($general as $key => $value) {
            $_value = $this->getData($key);
            if (null !== $_value) {
                $general[$key] = $_value;
            }
        }

        return $this->getLayout()->createBlock(
            Template::class,
            '',
            [
                'data' => [
                    'appearance' => $appearance,
                    'general' => $general,
                    'widgetId' => $widgetId,
                    'widgetBlock' => $this,
                    'template' => 'Olegnax_GoogleMap::style.phtml',
                ],
            ]
        )
            ->toHtml();
    }

    /**
     * @param string $path
     * @param int $storeCode
     * @return mixed
     */
    public function getModuleConfig($path = '', $storeCode = null)
    {
        return $this->helper->getModuleConfig($path, $storeCode);
    }

    /**
     * @param int $storeCode
     * @return string
     * @throws NoSuchEntityException
     */
    public function getWidgetConfig($storeCode = null)
    {
        $config = [
            'apiKey' => $this->getModuleConfig('general/google_api_key', $storeCode),
            'responsiveWidth' => (int)$this->getConfig('mobile_breakpoint', $storeCode, 768),
            'zoom_closer' => (int)$this->getConfig('zoom_closer', $storeCode, 7),
            'responsive' => [
                'zoom' => (int)$this->getConfig('zoom_mobile', $storeCode, 6),
            ],
            'map' => [],
            'locations' => [],
            'styles' => $this->getMapStyles($storeCode),
        ];

        $config['map'] = [
            'zoom' => (int)$this->getConfig('zoom', $storeCode, 7),
            'zoom_closer' => (int)$this->getConfig('zoom_closer', $storeCode, 7),
            'center' => [
                'lat' => $this->getConfig('latitude', $storeCode)
                    ? (float)$this->getConfig('latitude', $storeCode)
                    : null,
                'lng' => $this->getConfig('longitude', $storeCode)
                    ? (float)$this->getConfig('longitude', $storeCode)
                    : null,
            ],
            'streetViewControl' => (bool)$this->getConfig('street_view', $storeCode, false),
            'mapTypeId' => strtolower($this->getConfig('map_type', $storeCode, 'roadmap')),
            'mapTypeControlOptions' => [
                'mapTypeIds' => [],
            ],
        ];
        $locations = null;
        if ($this->getData('locations')) {
            $locations = trim($this->getData('locations'));
        }
        if (empty($locations)) {
            $locations = $this->helper->getModuleConfig('general/locations', $storeCode);
        }

        $locations = $this->getLocations($locations, $storeCode);
        if ($locations || 0 == count($locations)) {
            /** @var Location $location */
            foreach ($locations as $location) {
                $marker = $this->cacheHelper->load($location);
                if (!$marker) {
                    $marker = [
                        'position' => [
                            'lat' => (float)$location->getData(DataLocation::LATITUDE),
                            'lng' => (float)$location->getData(DataLocation::LONGITUDE),
                        ],
                        'icon' => $this->getMarker($location),
                        'title' => $this->escaper->escapeHtmlAttr($location->getData(DataLocation::NAME)),
                        'description' => $this->helper->getBlockTemplateProcessor(
                            $this->disabledLazy(
                                $location->getData(DataLocation::DESCRIPTION)
                            )
                        ),
                    ];
                    $this->cacheHelper->save($marker, $location);
                }

                $config['locations'][] = $marker;
            }
        }

        $module = $this->helper->isLegacyJQuery() ? 'OXGoogleMapLegacy' : 'OXGoogleMap';
        $_config = [];
        $_config[$module] = $config;

        return $this->json->serialize($_config);
    }

    /**
     * @param string $path
     * @param int $storeCode
     * @param mixed $default
     * @param string $dataPath
     * @return mixed
     */
    public function getConfig($path = '', $storeCode = null, $default = null, $dataPath = '')
    {
        if (empty($dataPath)) {
            $dataPath = $path;
        }
        $value = $this->getData($dataPath);
        if (null !== $value) {
            return $value;
        }
        $value = $this->helper->getModuleConfig('general/' . $path, $storeCode);
        if (null !== $value) {
            return $value;
        }
        return $default;
    }

    /**
     * @param null|int|StoreInterface $storeCode
     * @return array
     */
    protected function getMapStyles($storeCode = null)
    {
        $value = $this->getData('custom_css');
        if (null === $value) {
            $map_style = $this->helper->getModuleConfig('appearance/map_style', $storeCode);
            if ($map_style === MapStyle::TYPE_CUSTOM) {
                $value = $this->helper->getModuleConfig('appearance/custom_json', $storeCode);
            } else {
                $value = $this->mapStyle->getStyle($map_style);
            }
        }
        if (empty($value)) {
            $value = '[]';
        }
        try {
            $array = $this->json->unserialize($value);
        } catch (InvalidArgumentException $exception) {
            $this->_logger->warning(__('OX Google Map Widget Style error: ') . $exception->getMessage());
            $array = [];
        }

        return $array;
    }

    /**
     * @param string|array $locations
     * @param null|int|StoreInterface $store
     * @return Collection
     * @throws NoSuchEntityException
     */
    public function getLocations($locations, $store = null)
    {
        if (!is_array($locations)) {
            $locations = explode(',', $locations ?: '');
            /** @noinspection SpellCheckingInspection */
            $locations = array_map('intval', $locations);
            $locations = array_map('abs', $locations);
            $locations = array_filter($locations);
        }
        return $this->locationFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('location_id', ['in' => $locations])
            ->addFieldToFilter('status', '1')
            ->addStoreFilter($store ? $this->_storeManager->getStore($store) : '0');
    }

    /**
     * @param Location $location
     * @return string|array
     */
    private function getMarker(Location $location)
    {
        $style = $this->markerStyle->toIconArray();
        $selected_style = $location->getData('marker_style');
        if (array_key_exists($selected_style, $style)) {
            $icon = $style[$selected_style];
            if (is_array($icon)) {
                if (!empty($icon)) {
                    if (isset($icon['prepare'])) {
                        $funcName = $icon['prepare'];
                        unset($icon['prepare']);
                        $icon = $this->$funcName($icon, $location);
                    }
                } else {
                    $icon = '';
                }

            }
            return $icon;
        }
        return '';
    }

    /**
     * @param string $html
     * @return string
     */
    private function disabledLazy($html)
    {
        return preg_replace('#<img #i', '<img data-ox-image ', $html ?: '');
    }

    /**
     * @param array|string $icon
     * @param Location $location
     * @param array $data
     * @return array|string
     */
    public function prepareSvg($icon, Location $location, $data = [])
    {
        if (isset($icon['svg']) && !empty($icon['svg'])) {
            if (preg_match_all('#{{([^{}]+)}}#', $icon['svg'], $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $search = $match[0];
                    $key = $match[1];
                    $replace = '';
                    if (array_key_exists($key, $data)) {
                        $replace = $data[$key];
                    }
                    if ($location->hasData($key)) {
                        $replace = $location->getData($key);
                    }
                    $icon['svg'] = str_replace($search, $replace, $icon['svg']);
                }
                $icon['url'] = 'data:image/svg+xml;charset=UTF-8;base64,' . base64_encode(trim($icon['svg']));
                unset($icon['svg']);
            }
        } else {
            $icon = '';
        }
        return $icon;
    }

    /**
     * @param array $icon
     * @param Location $location
     * @return array|string
     * @throws LocalizedException
     */
    public function prepareTemplateSvg($icon, Location $location)
    {
        if (isset($icon['scaledSize'])) {
            $markerScale = $location->getMarkerSize();
            foreach ($icon['scaledSize'] as &$scaledSize) {
                $scaledSize *= $markerScale;
            }
        }

        $icon = $this->prepareTemplate($icon, $location);
        if (!empty($icon['html'])) {
            $icon['url'] = 'data:image/svg+xml;charset=UTF-8;base64,' . base64_encode(trim($icon['html']));
            unset($icon['html']);
        } else {
            $icon = '';
        }

        return $icon;
    }

    /**
     * @param array $icon
     * @param Location $location
     * @param array $data
     * @return array
     * @throws LocalizedException
     */
    public function prepareTemplate($icon, Location $location, $data = [])
    {
        if (!isset($icon['template'])) {
            $icon['template'] = sprintf(
                "Olegnax_GoogleMap::markers/%s.phtml",
                $location->getData(DataLocation::MARKER_STYLE)
            );
        }

        $icon['html'] = $this->getLayout()->createBlock(
            Template::class,
            '',
            [
                'data' => array_replace(
                    $data,
                    [
                        'widgetId' => $this->getMapId(),
                        'widgetBlock' => $this,
                        'template' => $icon['template'],
                    ]
                ),
            ]
        )
            ->assign([
                'location' => $location,
                'icon' => $icon,
            ])
            ->toHtml();
        unset($icon['template']);
        $icon['html'] = trim($icon['html']);

        return $icon;
    }

    /**
     * @param string $data
     * @param Location $location
     * @return string
     */
    public function contentLocalImage($data, Location $location)
    {
        $imageUrl = $this->prepareLocalImage(['data' => $data], $location);
        $imageEncoded = '';
        if (!empty($imageUrl)) {
            try {
                $imageData = file_get_contents($imageUrl);
                if (empty($imageData)) {
                    throw new Exception('No data received: ' . $imageUrl);
                }
                $fInfo = new finfo(FILEINFO_MIME);
                $fInfoMime = $fInfo->buffer($imageData);
                [$mime, $charset] = explode('; ', $fInfoMime);
                $imageDataEncoded = base64_encode($imageData);
                $imageEncoded = sprintf('data:%s;base64,%s', $mime, $imageDataEncoded);
            } catch (Exception $exception) {
                $this->_logger->error('Google Map: ' . $exception->getMessage());
                $imageEncoded = '';
            }
        }

        return $imageEncoded;
    }

    /**
     * @param array|string $icon
     * @param Location $location
     * @return string
     */
    public function prepareLocalImage($icon, Location $location)
    {
        if (isset($icon['data'])) {
            $data = $location->getData($icon['data']);
            if (!empty($data)) {
                $icon = $this->getViewFileUrl($data);
            } else {
                $icon = '';
            }
        } else {
            $icon = '';
        }
        return $icon;
    }
}
