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
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        $this->_customerFactory = $customerFactory;
        $this->_resource = $resource;
    }

    public function createShipmentTracking($sessionId, $customerId, $points)
    {
       
        return true;
    
    }

    public function createInvoiceCapture($sessionId, $customerId, $points)
    {
       
        return true;
    
    }


}