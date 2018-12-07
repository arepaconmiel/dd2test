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
    * @param string $customerId
    * @param int $points
    * 
    * @return mixed
    */
    public function createShipmentTracking($sessionId, $customerId, $points);

    /**
    * @param string $sessionId
    * @param string $customerId
    * @param int $points
    * 
    * @return mixed
    */
    public function createInvoiceCapture($sessionId, $customerId, $points);

}