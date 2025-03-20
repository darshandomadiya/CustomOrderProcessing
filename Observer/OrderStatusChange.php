<?php
namespace DarshanDomadiya\CustomOrderProcessing\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;
use DarshanDomadiya\CustomOrderProcessing\Model\OrderStatusLogFactory;
use DarshanDomadiya\CustomOrderProcessing\Model\ResourceModel\OrderStatusLog as OrderStatusLogResource;
use Magento\Sales\Model\Order;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class OrderStatusChange implements ObserverInterface
{
    protected $logger;
    protected $orderStatusLogFactory;
    protected $orderStatusLogResource;
    protected $transportBuilder;
    protected $storeManager;

    public function __construct(
        LoggerInterface $logger,
        OrderStatusLogFactory $orderStatusLogFactory,
        OrderStatusLogResource $orderStatusLogResource,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->logger = $logger;
        $this->orderStatusLogFactory = $orderStatusLogFactory;
        $this->orderStatusLogResource = $orderStatusLogResource;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
    }

    public function execute(Observer $observer)
    { 
        try {
            $order = $observer->getEvent()->getOrder();
            $oldStatus = $order->getOrigData('status');
            $newStatus = $order->getStatus();

            if ($oldStatus === $newStatus) {
                return;
            }

            // Create log entry
            $orderStatusLog = $this->orderStatusLogFactory->create();
            $orderStatusLog->setOrderId($order->getId())
                ->setOldStatus($oldStatus)
                ->setNewStatus($newStatus)
                ->setTimestamp((new \DateTime())->format('Y-m-d H:i:s'));

            $this->orderStatusLogResource->logStatusChange($orderStatusLog);

            // Send email if shipped
            if ($newStatus == Order::STATE_COMPLETE) {
                $this->sendShipmentNotification($order);
            }

            $this->logger->info("Order status updated for Order #{$order->getIncrementId()} from {$oldStatus} to {$newStatus}");
        } catch (LocalizedException $e) {
            $this->logger->error('LocalizedException: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->logger->error('Error in OrderStatusChange Observer: ' . $e->getMessage());
        }
    }

    private function sendShipmentNotification($order)
    {
        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier('order_shipped_email_template')
                ->setTemplateOptions([
                    'area' => 'frontend',
                    'store' => $this->storeManager->getStore()->getId(),
                ])
                ->setTemplateVars(['order' => $order])
                ->setFrom('general')
                ->addTo($order->getCustomerEmail())
                ->getTransport();

            $transport->sendMessage();

            $this->logger->info("Shipment email sent for Order #{$order->getIncrementId()}");
        } catch (\Exception $e) {
            $this->logger->error('Failed to send shipment email: ' . $e->getMessage());
        }
    }
}
