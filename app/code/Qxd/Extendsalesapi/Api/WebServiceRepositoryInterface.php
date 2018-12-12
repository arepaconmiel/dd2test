<?php
namespace Qxd\Extendsalesapi\Api;
/**
 * Interface WebServiceRepositoryInterface
 * @package Qxd\Sku\Api
 */
interface WebServiceRepositoryInterface
{
    
    /**
    * @param string $sessionId
    * @param string $orderIncrementId
    * @param mixed $itemsQty
    * @param string $comment
    * @param int $email
    * @param boolean $includeComment
    * @param mixed $tracking
    * 
    * @return mixed
    */
    public function createShipmentTracking($sessionId, $orderIncrementId, $itemsQty = array(), $comment = null, $email = false, $includeComment = false,$tracking = array());

    /**
    * @param string $sessionId
    * @param string $invoiceIncrementId
    * @param mixed $itemsQty
    * @param string $comment
    * @param int $email
    * @param boolean $includeComment
    * 
    * @return mixed
    */
    public function createInvoiceCapture($sessionId, $invoiceIncrementId, $itemsQty, $comment = null, $email = false, $includeComment = false);

}