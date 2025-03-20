<?php
namespace DarshanDomadiya\CustomOrderProcessing\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Exception\LocalizedException;

class OrderStatusLog extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('order_status_log', 'id'); // Table name & primary key
    }

    /**
     * Log order status change
     *
     * @param \DarshanDomadiya\CustomOrderProcessing\Model\OrderStatusLog $log
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function logStatusChange(\DarshanDomadiya\CustomOrderProcessing\Model\OrderStatusLog $log)
    {
        try {
            $this->save($log); // Uses Magento’s ORM for safe data insertion
        } catch (\Exception $e) {
            throw new LocalizedException(__('Error while logging order status: %1', $e->getMessage()));
        }
    }
}
