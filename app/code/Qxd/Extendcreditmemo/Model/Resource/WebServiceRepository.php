<?php
namespace Qxd\Extendcreditmemo\Model\Resource;

use Qxd\Extendcreditmemo\Api\WebServiceRepositoryInterface;

/**
 * Class WebServiceRepository
 * @package MagePlaza\WebService\Model
 */
class WebServiceRepository implements WebServiceRepositoryInterface
{
	public function __construct(
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemo
    )
    {
        $this->_creditmemo = $creditmemo;
    }

    /*
    * Function used for the api
    * Url /V1/qxd/webservice/updateExportStatus
    */

    public function updateExportStatus($sessionId, $creditmemoId)
    {   
        $result=false;
        try{

            $creditmemo = $this->_creditmemo->get($creditmemoId);
            if (!$creditmemo->getId()) { 
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested creditmemo does not exist.'));
            }
            
            $creditmemo->setData("export_status",1);
            $creditmemo->save();
            $result=true;

        }catch (Exception $e)
        {	
        	throw new \Magento\Framework\Webapi\Exception(__('Error while saving creditmemo.'));
        }

        return $result;
    
    }


}