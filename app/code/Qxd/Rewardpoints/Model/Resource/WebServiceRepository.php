<?php
namespace Qxd\Rewardpoints\Model\Resource;

use Qxd\Rewardpoints\Api\WebServiceRepositoryInterface;

/**
 * Class WebServiceRepository
 * @package MagePlaza\WebService\Model
 */
class WebServiceRepository implements WebServiceRepositoryInterface
{
	public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->_customerFactory = $customerFactory;
        $this->_resource = $resource;
    }

    /*
    * Function used for the api
    * Url /V1/qxd/webservice/updateRewardPoints
    */

    public function updateRewardPoints($sessionId, $customerId, $points)
    {
        try{


            $customer = $this->_customerFactory->create()->load($customerId);
            if($customer->getData())
            {	
            	$connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            	// sql debido a problemas con el metodo usado anteriormente
            	$connection->query('update customer_entity set reward_points ='.$points. ' where entity_id = '. $customerId);

                return true;
            }
            else{
            	throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested customer not exist'));
            }
        }catch (Exception $e)
        {	
        	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_API.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
            $logger->info($e->getTraceAsString());
            /*Mage::log($e->getMessage(),null,'QXD_API.log');
            Mage::log($e->getTraceAsString(),null,'QXD_API.log');*/
        }

        return false;
    
    }


}