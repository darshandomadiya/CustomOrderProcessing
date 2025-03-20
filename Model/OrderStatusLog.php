<?php
namespace DarshanDomadiya\CustomOrderProcessing\Model;

use Magento\Framework\Model\AbstractModel;
use DarshanDomadiya\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as OrderStatusLogResource;

class OrderStatusLog extends AbstractModel
{
    const CACHE_TAG = 'order_status_log';

    protected $_cacheTag = self::CACHE_TAG;
    protected $_eventPrefix = 'order_status_log';

    protected function _construct()
    {
        $this->_init(OrderStatusLogResource::class);
    }

    public function getOrderId()
    {
        return $this->getData('order_id');
    }

    public function setOrderId($orderId)
    {
        return $this->setData('order_id', $orderId);
    }

    public function getOldStatus()
    {
        return $this->getData('old_status');
    }

    public function setOldStatus($status)
    {
        return $this->setData('old_status', $status);
    }

    public function getNewStatus()
    {
        return $this->getData('new_status');
    }

    public function setNewStatus($status)
    {
        return $this->setData('new_status', $status);
    }

    public function getTimestamp()
    {
        return $this->getData('timestamp');
    }

    public function setTimestamp($timestamp)
    {
        return $this->setData('timestamp', $timestamp);
    }
}
