<?php
namespace DarshanDomadiya\CustomOrderProcessing\Model;

use DarshanDomadiya\CustomOrderProcessing\Api\OrderStatusInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;

class OrderStatus implements OrderStatusInterface
{
    protected $orderRepository;
    protected $logger;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
    )
    {
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    public function updateOrderStatus($orderIncrementId, $status)
    {
        try {
            $order = $this->orderRepository->get($orderIncrementId);
            
            if (!$order->canCancel() && $status === Order::STATE_CANCELED) {
                throw new LocalizedException(__('Cannot cancel this order.'));
            }

            $order->setStatus($status);
            $order->addCommentToStatusHistory('Order status updated via API to %1'); 
            $this->orderRepository->save($order);

            return __('Order status updated successfully.');
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
            throw new LocalizedException(__('Order not found.'));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
