<?php
namespace DarshanDomadiya\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DarshanDomadiya\CustomOrderProcessing\Model\OrderStatusLog as OrderStatusLogModel;
use DarshanDomadiya\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as OrderStatusLogResource;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(OrderStatusLogModel::class, OrderStatusLogResource::class);
    }
}
