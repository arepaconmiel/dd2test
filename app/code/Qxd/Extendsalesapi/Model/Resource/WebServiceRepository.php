<?php
namespace Qxd\Extendsalesapi\Model\Resource;

use Qxd\Extendsalesapi\Api\WebServiceRepositoryInterface;

/**
 * Class WebServiceRepository
 * @package MagePlaza\WebService\Model
 */
class WebServiceRepository implements WebServiceRepositoryInterface
{
	public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\DB\Transaction $transactionFactory,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService

    )
    {
        $this->_order = $order;
        $this->_convertOrder = $convertOrder;
        $this->_shipmentNotifier = $shipmentNotifier;
        $this->_transactionFactory = $transactionFactory;
        $this->_trackFactory = $trackFactory;
        $this->_shippingConfig = $shippingConfig;
        $this->_invoiceSender = $invoiceSender;
        $this->_invoiceService = $invoiceService;
    }

    public function createShipmentTracking($sessionId, $orderIncrementId, $itemsQty = array(), $comment = null, $email = false, $includeComment = false,$tracking = array())
    {   
        $orderItems = [];
        $shipmentItems = [];
        try{

            //BEGIN CREATE
            $order = $this->_order->loadByIncrementId($orderIncrementId);
            $itemsQty = $this->_prepareItemQtyData($itemsQty);

            if (!$order->getId()) { 
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested order not exists.'));
            }
            if (!$order->canShip()) { 
                throw new \Magento\Framework\Webapi\Exception(__('Cannot do shipment for order.')); 
            }

            $shipment = $this->_convertOrder->toShipment($order);

            if ($shipment)
            {   
                foreach ($order->getAllItems() AS $orderItem) {

                    $orderItems[] = $orderItem;
   
                }

                if(!empty($itemsQty)){
                    foreach ($orderItems as $item) {
                        if(isset($itemsQty[$item->getId()])){
                            $shipmentItem = $this->_convertOrder->itemToShipmentItem($item)->setQty($itemsQty[$item->getId()]);
                            $shipment->addItem($shipmentItem);
                        }
                    }
                }else{
                    throw new \Magento\Framework\Webapi\Exception(__('No items to add.')); 
                }

                $shipment->register();
                $shipment->addComment($comment, $email && $includeComment);
                $shipment->getOrder()->setIsInProcess(true);

                $shipment->save();
                $shipment->getOrder()->save();
                
                try
                {
                    $transactionSave = $this->_transactionFactory->addObject($shipment)->addObject($shipment->getOrder())->save();
                } catch (Mage_Core_Exception $e)
                {
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_API.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info($e->getMessage());
                    $logger->info($e->getTraceAsString());
                    throw new \Magento\Framework\Webapi\Exception(__($e->getMessage())); 
                }

                //BEGIN ADD TRACKING
                $trackId=$this->addTrack($shipment,$tracking);
                if ($email) { 
                    $this->_shipmentNotifier->notify($shipment);
                    $shipment->save(); 
                }
                return $shipment->getIncrementId();
                //END ADD TRACKING
              
            }
            //END CREATE

        }catch (Exception $e)
        {   
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_API.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            $logger->info($e->getTraceAsString());
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage())); 
        }

        return null;
    
    }

    public function createInvoiceCapture($sessionId, $invoiceIncrementId, $itemsQty, $comment = null, $email = false, $includeComment = false)
    {
        try{
            //BEGIN CREATE
            $order = $this->_order->loadByIncrementId($invoiceIncrementId);
            $itemsQty = $this->_prepareInvoiceItemQtyData($itemsQty);

            if (!$order->getId()) { 
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested order not exists.'));
            }

            if (!$order->canInvoice()) { 
                throw new \Magento\Framework\Webapi\Exception(__('Cannot do invoice for order.')); 
            }

            $invoice = $this->_invoiceService->prepareInvoice($order, $itemsQty);
            $invoice->register();
            if ($comment !== null) { $invoice->addComment($comment, $email); }
            if ($email) { $invoice->setEmailSent(true); }
            $invoice->getOrder()->setIsInProcess(true);
        }catch (Exception $e)
        {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_API.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            $logger->info($e->getTraceAsString());
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage() . ' line' . $e->getLine())); 
        }

        try {
            $transactionSave = $this->_transactionFactory->addObject($invoice)->addObject($invoice->getOrder())->save();

            //$invoice->sendEmail($email, ($includeComment ? $comment : ''));
            $this->_invoiceSender->send($invoice);
        } catch (Mage_Core_Exception $e)
        {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_API.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            $logger->info($e->getTraceAsString());
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage() . ' line' . $e->getLine())); 
        }

        try{
            //BEGIN CAPTURE

            $this->capture($invoice);
            $InvInc=$invoice->getIncrementId();

            return $InvInc;
            //END CAPTURE

            //END CREATE
        }catch (Exception $e)
        {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_API.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            $logger->info($e->getTraceAsString());
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage() . ' line' . $e->getLine()));
        }
        return null;
    } 

    public function capture($invoice)
    {   
        if (!$invoice->getId()) { 
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested invoice does not exist.'));
        }

        if (!$invoice->canCapture()) { 
            throw new \Magento\Framework\Webapi\Exception(__('Invoice cannot be captured.'));
        }

        try {
            $invoice->capture();

            $invoice->getOrder()->setIsInProcess(true);

            $transactionSave = $this->_transactionFactory->addObject($invoice)->addObject($invoice->getOrder())->save();
        }
        catch (Mage_Core_Exception $e) {  
            throw new \Magento\Framework\Webapi\Exception(__($e->getMessage() . ' line ' . $e->getLine()));
        }
        catch (Exception $e) {  
            throw new \Magento\Framework\Webapi\Exception(__('Invoice capturing problem.'));
        }

        return true;
    }

    protected function _prepareInvoiceItemQtyData($data)
    {
        $quantity = array();
        foreach ($data as $item) { if (isset($item['order_item_id']) && isset($item['qty'])) { $quantity[$item['order_item_id']] = $item['qty']; } }
        return $quantity;
    }

    protected function _prepareItemQtyData($data)
    {   
        $_data = array();
        foreach ($data as $item) { 
            if (isset($item['order_item_id']) && isset($item['qty'])) { 
                $_data[$item['order_item_id']] = $item['qty']; 
            } 
        }
        return $_data;
    }

    protected function _prepareTrackingData($data)
    {
        $_data = array();
        foreach ($data as $item)
        {
            if (isset($item['carrier']) && isset($item['title']) && isset($item['trackNumber']))
            { $_data[] = array('carrier'=>$item['carrier'],'title'=>$item['title'],'trackNumber'=>$item['trackNumber']); }
        }
        return $_data;
    }

    protected function _getCarriers($object)
    {
        $carriers = array();
        $carrierInstances = $this->_shippingConfig->getActiveCarriers($object->getStoreId());
        $carriers['custom'] = __('Custom Value');

        foreach ($carrierInstances as $code => $carrier) { if ($carrier->isTrackingAvailable()) { $carriers[$code] = $carrier->getConfigData('title'); } }

        return $carriers;
    }

    public function addTrack($shipment, $tracking)
    {
        $result=false;
        $trackingInfo = $this->_prepareTrackingData($tracking);
        $carriers = $this->_getCarriers($shipment);

        if (!$shipment->getId()) { 
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested entry does not exist.'));
        }

        foreach($trackingInfo as $data)
        {
            $carrier=$data['carrier'];
            $title=$data['title'];
            $trackNumber=$data['trackNumber'];

            if (!isset($carriers[$carrier])) { 
                throw new \Magento\Framework\Webapi\Exception(__('Invalid carrier specified.'));
            }

            $track = $this->_trackFactory->create();
            $track->setCarrierCode($carrier);
            $track->setTitle($title);
            $track->setTrackNumber($trackNumber);
            $shipment->addTrack($track);
           

            try {
                $shipment->save();
                $track->save();
                $result=true;
            } catch (Mage_Core_Exception $e)
            {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_API.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($e->getMessage());
                $logger->info($e->getTraceAsString());
                throw new \Magento\Framework\Webapi\Exception(__($e->getMessage())); 
            }
        }

        return $result;
    }

}