<?php
namespace DarshanDomadiya\CustomOrderProcessing\Test\Unit\Model;

use PHPUnit\Framework\TestCase;
use DarshanDomadiya\CustomOrderProcessing\Model\OrderStatus;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class OrderStatusTest extends TestCase
{
    private $orderRepositoryMock;
    private $orderMock;
    private $orderStatus;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->orderMock = $this->createMock(Order::class);
        $this->orderStatus = new OrderStatus($this->orderRepositoryMock);
    }

    public function testUpdateOrderStatusSuccess()
    {
        $orderIncrementId = '100000001';
        $newStatus = 'complete';

        $this->orderRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->with($orderIncrementId)
            ->willReturn($this->orderMock);

        $this->orderMock
            ->expects($this->once())
            ->method('setStatus')
            ->with($newStatus);

        $this->orderRepositoryMock
            ->expects($this->once())
            ->method('save')
            ->with($this->orderMock);

        $result = $this->orderStatus->updateOrderStatus($orderIncrementId, $newStatus);
        $this->assertEquals('Order status updated successfully.', $result);
    }

    public function testUpdateOrderStatusThrowsExceptionIfOrderNotFound()
    {
        $this->orderRepositoryMock
            ->expects($this->once())
            ->method('get')
            ->willThrowException(new NoSuchEntityException(__('Order not found.')));

        $this->expectException(LocalizedException::class);
        $this->expectExceptionMessage('Order not found.');

        $this->orderStatus->updateOrderStatus('999999', 'processing');
    }
}
