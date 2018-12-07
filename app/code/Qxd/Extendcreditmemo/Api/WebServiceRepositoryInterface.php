<?php
namespace Qxd\Extendcreditmemo\Api;
/**
 * Interface WebServiceRepositoryInterface
 * @package Qxd\Sku\Api
 */
interface WebServiceRepositoryInterface
{
    
    /**
    * @param string $sessionId
    * @param string $creditmemoId
    * 
    * @return mixed
    */
    public function updateExportStatus($sessionId, $creditmemoId);

}