<?php
namespace DarshanDomadiya\CustomOrderProcessing\Api;

interface OrderStatusInterface
{
    /**
     * Update order status
     * @param string $orderIncrementId
     * @param string $status
     * @return string
     */
    public function updateOrderStatus($orderIncrementId, $status);
}