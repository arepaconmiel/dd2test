<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Helper;

use Magento\User\Api\Data\UserInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class System
 * @package Magestore\Giftvoucher\Helper
 */
class System extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    /**
     * @var DateTime
     */
    protected $dateTime;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    
    /**
     * @var \Magento\User\Model\UserFactory 
     */
    protected $userFactory;


    /**
     * System constructor.
     * @param DateTime $dateTime
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        DateTime $dateTime,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\User\Model\UserFactory $userFactory
    ) {
    
        $this->dateTime = $dateTime;
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
    }
    
    /**
     * Get current timestamp
     *
     * @return string
     */
    public function getCurTime()
    {
        return $this->dateTime->gmtDate();
    }
    
    /**
     * Get current admin user
     *
     * @return UserInterface
     */
    public function getCurUser()
    {
        $user = $this->authSession->getUser();
        if(!$user || !$user->getId()) {
            /* request from API */
            $user = $this->userFactory->create();
            $user->setId(0)->setUserName('API');
        }
        return $user;
    }
    
    /**
     * Get current unix time stamp
     *
     * @return int
     */
    public function getUnixTime()
    {
        return $this->dateTime->timestamp();
    }
    
    /**
     *
     * @return boolean
     */
    public function isAdminArea()
    {
        if ($this->authSession->isLoggedIn()) {
            return true;
        }
        return false;
    }
}
