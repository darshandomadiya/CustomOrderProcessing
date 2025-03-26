<?php
namespace DarshanDomadiya\CustomOrderProcessing\Model;

use DarshanDomadiya\CustomOrderProcessing\Api\OrderStatusInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\CacheInterface;

class OrderStatus implements OrderStatusInterface
{
    protected $orderRepository;
    protected $logger;
    private $cache;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        OrderFactory $orderFactory,
        CacheInterface $cache,
    )
    {
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        $this->orderFactory = $orderFactory;
        $this->cache = $cache;
    }

    public function updateOrderStatus($orderIncrementId, $status)
    {
        try {
            if (!preg_match('/^\d{9}$/', $orderIncrementId)) {
                throw new LocalizedException(__('Invalid order ID format.'));
            }
            
            if (!in_array($status, $this->allowedStatuses, true)) {
                throw new LocalizedException(__('Invalid status provided.'));
            }
 
            $orderId = $this->getOrderIdByIncrementId($orderIncrementId);
            if (!$orderId) {
                throw new NoSuchEntityException(__('Order not found.'));
            }

            $cacheKey = "order_status_$orderIncrementId";
            $cachedStatus = $this->cache->load($cacheKey);
            if ($cachedStatus === $status) {
                return __('Order status is already set to %1.', $status);
            }

            $order = $this->orderRepository->get($orderId);
            if ($order->getStatus() === $status) {
                return __('Order status is already set to %1.', $status);
            }

            if (!$order->canCancel() && $status === Order::STATE_CANCELED) {
                throw new LocalizedException(__('Cannot cancel this order.'));
            }

            $order->setStatus($status);
            $order->addCommentToStatusHistory('Order status updated via API to %1'); 
            $this->orderRepository->save($order);

            $this->cache->save($status, $cacheKey, ['order_status'], 3600);
            return __('Order status updated successfully.');
        } catch (NoSuchEntityException $e) {
            $this->logger->error($e->getMessage());
            throw new LocalizedException(__('Order not found.'));
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            throw new LocalizedException(__($e->getMessage()));
        }
    }
 
    /**
     * Get order id by increment id
     *
     * @param void
     * @return int $orderId
     */
    public function getOrderIdByIncrementId($incrementId)
     {
        $orderModel = $this->orderFactory->create();
        $order = $orderModel->loadByIncrementId($incrementId);
        $orderId = $order->getId(); 
        return $orderId;
     }
}
