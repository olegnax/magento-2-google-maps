<?php
/**
 * @author      Olegnax
 * @package     Olegnax_GoogleMap
 * @copyright   Copyright (c) 2020 Olegnax (http://olegnax.com/). All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Olegnax\GoogleMap\Controller\Adminhtml\Location;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Olegnax\GoogleMap\Model\Location;

class Save extends Action
{

    protected $dataPersistor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     * @noinspection PhpRedundantCatchClauseInspection
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('location_id');

            $model = $this->_objectManager->create(Location::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Location no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Location.'));
                $this->dataPersistor->clear('olegnax_googlemap_location');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['location_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Location.'));
            }

            $this->dataPersistor->set('olegnax_googlemap_location', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'location_id' => $this->getRequest()->getParam('location_id'),
                ]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
