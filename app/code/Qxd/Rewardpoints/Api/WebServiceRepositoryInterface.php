<?php
namespace Qxd\Rewardpoints\Api;
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
    public function updateRewardPoints($sessionId, $customerId, $points);

}