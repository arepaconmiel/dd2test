<?php
namespace Qxd\Emailsreport\Model;
use Magento\Framework\App\Filesystem\DirectoryList;

class Reports extends \Magento\Framework\Model\AbstractModel 
{
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Ebizmarts\Mandrill\Model\MessageFactory $messageFactory,
        \Magento\Framework\View\LayoutInterface $layout,
        \Ebizmarts\Mandrill\Model\Api\Mandrill $api
    ) {
        $this->_resource = $resource;
        $this->_scopeConfig = $scopeConfig;
        $this->_messageFactory = $messageFactory;
        $this->_layout = $layout;
        $this->_api = $api;
    }

    public function updateStatusToPending()
    {
        try
        {   
            $writeConnection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            $query = "UPDATE customer_entity SET sent_status=0";
            $writeConnection->query($query);

        }catch (Exception $e){ 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_ERROR.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }

    }
    public function getSender(){ 
        return $this->_scopeConfig->getValue(
            'emailsreport_options/configure_emailsreport/emailsreport_sender',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getEmailAccounts(){ 
        return $this->_scopeConfig->getValue(
            'emailsreport_options/configure_emailsreport/emailsreport_emaillist',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    // Not used
    public function getFolderPath(){ 
        return Mage::getBaseDir('var')."/QXD_export/Rewards_Report/"; 
    }
    public function generateReports()
    {
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

        $reportData=$this->generatePointsToNextRewardReport($connection);

        $this->generateEmail($reportData,$connection);

    }
    public function generatePointsToNextRewardReport($readConnection)
    {
        $result=array();

        try {
            $query = "
                        SELECT TAUX1.customer_id,TAUX1.email,TAUX1.firstname,TAUX1.lastname,TAUX1.customer_points_usable AS points,(250 - TAUX1.auxPoints) AS next_points
                        FROM (SELECT T1.customer_id,T2.email,T2.firstname,T2.lastname,T1.customer_points_usable,IF(T1.customer_points_usable > 250,
                        T1.customer_points_usable % 250,T1.customer_points_usable ) as auxPoints
                        FROM rewards_customer_index_points as T1
                        INNER JOIN ( 
                            SELECT `e`.entity_id,`e`.email, `e`.`firstname`, `e`.`lastname`
                            FROM `customer_entity` AS `e`
                            WHERE `e`.sent_status=0 AND `e`.reward_subscription_status=1
                        ) as T2 ON T1.customer_id=T2.entity_id) AS TAUX1
                     " ;

            $result['next_reward'] = $readConnection->fetchAll($query);

            $certificatesQuery="SELECT T1.gift_code,T1.customer_id,T1.giftvoucher_id,
                    CASE
                        WHEN T1.status = 1 THEN 'Pending'
                        WHEN T1.status = 2 THEN 'Active'
                        WHEN T1.status = 3 THEN 'Disabled'
                        WHEN T1.status = 4 THEN 'Used'
                        WHEN T1.status = 5 THEN 'Expired'
                        ELSE ''
                    END as status,
                    T2.created_at,T1.expired_at,T2.amount,T1.balance,T2.customer_email,T2.comments,T2.order_increment_id,T2.order_amount
                    FROM  giftvoucher AS T1
                    INNER JOIN giftvoucher_history AS T2 ON T1.giftvoucher_id=T2.giftvoucher_id
                    WHERE T1.is_certificate = 1 AND T1.balance > 0
                    GROUP BY T1.gift_code
                    ORDER BY T1.giftvoucher_id";

            $result['certificates'] = $readConnection->fetchAll($certificatesQuery);

        }catch (Exception $e){ 
            print_r($e->getMessage()); 
        }

        return $result;
    }
    public function generateEmail($reportData,$writeConnection)
    {
        $nextPoints=$reportData['next_reward'];
        $Certificates=$reportData['certificates'];

        $scheduleDate=date('Y-m-d');
        $scheduleDate=$scheduleDate."16:00:00";

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Reward_Statement.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $mail = $this->_messageFactory->create();
        $mandrillApiInstance = $this->getMandrillApiInstance();

        $headers = $mail->getHeaders();

        $index=0;
        $countNP=count($nextPoints);

        foreach($nextPoints as $data)
        {
            if( !empty($data['email']) && strpos($data['email'], 'diversdirect.com') === false &&
                strpos($data['email'], 'oceandivers.com') === false &&
                strpos($data['email'], 'marketplace.amazon.com') === false &&
                strpos($data['email'], 'emoceansports.com') === false )
            {
                $customerCertificates=array();
                $availableAmountCertificates=0;

                foreach($Certificates as $k=>$certificate)
                {
                    if($certificate['customer_id'] == $data['customer_id'] && $certificate['balance'] > 0)
                    {
                        $newED=new DateTime($certificate['expired_at']);
                        $expire_date=$newED->format('m/d/Y');

                        $newID=new DateTime($certificate['created_at']);
                        $issued_date=$newID->format('m/d/Y');

                        $balance=number_format((float)$certificate['balance'], 2, '.', '');
                        if($balance < 1){ $balance="$00.00"; }else{ $balance="$".$balance; }

                        $amount=number_format((float)$certificate['amount'], 2, '.', '');
                        if($amount < 1){ $amount="$00.00"; }else{ $amount="$".$amount; }

                        $customerCertificates[]=array('code'=>$certificate['gift_code'],'balance'=>$balance,'expiration'=>$expire_date,'issued'=>$issued_date,'amount'=>$amount);
                        $availableAmountCertificates=$availableAmountCertificates+$certificate['balance'];

                        unset($Certificates[$k]);
                    }
                }
                if($data['points'] > 0 || $availableAmountCertificates > 0)
                {
                //    $data['email']='BILL@CASTSTONE.COM';

                    $email = array('subject' => "Divers Direct Rewards Statement", 'to' => array());

                    $email['to'][] = array('email' => $data['email'],'name' => 'Divers Direct Rewards Report');

                    $email['from_name'] = 'Divers Direct';
                    $email['from_email'] = $this->getSender();

                    $email['headers'] = $headers;
                    $email['text'] = "Report for date ".date('m-d-Y');
                    $email['merge_language']='handlebars';
                    $email['merge_vars']=array( array('rcpt'=>$data['email'],'vars'=>array(array('name'=>'customer_name','content'=>$data['firstname'].' '.$data['lastname']),
                        array('name'=>'statement_date','content'=>date('m/d/Y')),array('name'=>'points','content'=>$data['points']),array('name'=>'next_points','content'=>$data['next_points']),
                        array('name'=>'certificates_amount','content'=>$availableAmountCertificates),
                        array('name'=>'certificates','content'=>$customerCertificates),
                        array('name'=>'custom_banner','content'=>$this->_layout->createBlock('Magento\Cms\Block\Block')->setBlockId('reward_statement_banner')->toHtml()),
                    ) ) );


                    try
                    {
                        $result = $mandrillApiInstance->messages->sendTemplate("Reward Statement", array(array()), $email, $async = false, $ip_pool = null, $send_at = null); //$scheduleDate

                        $logger->info(print_r($result, true));

                        if(isset($result[0]['status']) &&
                            ($result[0]['status']=='scheduled' ||  $result[0]['status']=='queued' ||  $result[0]['status']=='sent'))
                        {
                            $query = "UPDATE customer_entity SET sent_status=1 WHERE email='".$data['email']."'";
                            $writeConnection->query($query);
                        }
                        else{
                            if($result[0]['status'] == 'rejected' &&
                                ($result[0]['reject_reason'] == 'unsub' ||
                                    $result[0]['reject_reason'] == 'hard-bounce' ||
                                    $result[0]['reject_reason'] == 'spam' ||
                                    $result[0]['reject_reason'] == 'soft-bounce') )
                            {
                    //            $data['email']="asalgado@qxdev.com";
                                $query = "UPDATE customer_entity SET reward_subscription_status=0 WHERE email='".$data['email']."'";
                                $writeConnection->query($query);
                            }
                            $logger->info("EMAIL NOT SENT");
                        }

                    } catch(Mandrill_Error $e)
                    {   

                        $writer_e = new \Zend\Log\Writer\Stream(BP . '/var/log/Reward_Statement.log');
                        $logger_e = new \Zend\Log\Logger();
                        $logger_e->addWriter($writer_e);
                        $logger_e->info('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage());
                    }
                }
            }
             $logger->info(++$index.' of '.$countNP);
        }
    }
    public function testTemplate()
    {   
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Reward_Statement.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $mail = $this->_messageFactory->create();
        $mandrillApiInstance = $this->getMandrillApiInstance();

        if ($mandrillApiInstance === null) {
            return false;
        }

        $email = array('subject' => "Divers Direct Rewards Statement", 'to' => array());

        $email['to'][] = array('email' => 'jvillalobos@qxdev.com','name' => 'Divers Direct Rewards Report');

        $email['from_name'] = 'Divers Direct Experts';
        $email['from_email'] = $this->getSender();

        $headers = $mail->getHeaders();
        //$headers[] = Mage::helper('mailchimp/mandrill')->getUserAgent();
        $email['headers'] = $headers;
        $email['text'] = "Report for date ".date('m-d-Y');
        $email['merge_language']='handlebars';
        $email['merge_vars']=array( array('rcpt'=>'jvillalobos@qxdev.com','vars'=>array(array('name'=>'customer_name','content'=>'Andres Salgado'),
            array('name'=>'statement_date','content'=>date('m/d/Y')),array('name'=>'points','content'=>100),array('name'=>'next_points','content'=>150),
            array('name'=>'certificates_amount','content'=>10),
            array('name'=>'certificates','content'=>array(array('code'=>'TEST1','balance'=>0,'expiration'=>date('m-d-Y'),'issued'=>date('m-d-Y'),'amount'=>10),array('code'=>'TEST2','balance'=>10,'expiration'=>date('m-d-Y'),'issued'=>date('m-d-Y'),'amount'=>20))),
            array('name'=>'custom_banner','content'=>$this->_layout->createBlock('Magento\Cms\Block\Block')->setBlockId('reward_statement_banner')->toHtml()),
        ) ) );

        $result = $mandrillApiInstance->messages->sendTemplate("Reward Statement", array(array()),
            $email, $async = false, $ip_pool = null, $send_at = null);
        // print_r($email);
        //print_r($result);
        $logger->info(print_r($result,true));
        if(isset($result[0]['status']) && ($result[0]['status']=='queued' || $result[0]['status']=='sent') ) { 
            $logger->info("EMAIL SENT"); 
        }
        else{  
            $logger->info("EMAIL NOT SENT");
        }

        return true;

    }
    public function getScheduledData()
    {
        $mail = new Mandrill_Message(Mage::getStoreConfig(Ebizmarts_MailChimp_Model_Config::MANDRILL_APIKEY, 1));
        $result = $mail->messages->listScheduled();

        print_r($result);
    }
    public function ReeScheduledData()
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $currentDate=date("Y-m-d H:i:s");
        $lastMonth=date("Y-m-d H:i:s",strtotime(date("Y-m-d", strtotime('-4 week',strtotime($currentDate))).' midnight'));

        $reportData=$this->generatePointsToNextRewardReport($readConnection,$lastMonth);

        $nextPoints=$reportData['next_reward'];
        $Certificates=$reportData['certificates'];

        $scheduleDate=date('Y-m-d');
        $scheduleDate=$scheduleDate."1:00:00";

        $mail = new Mandrill_Message(Mage::getStoreConfig(Ebizmarts_MailChimp_Model_Config::MANDRILL_APIKEY, 1));
        $headers = $mail->getHeaders();
        $headers[] = Mage::helper('mailchimp/mandrill')->getUserAgent();

        foreach($nextPoints as $data)
        {
            if( !empty($data['email']) && strpos($data['email'], 'diversdirect.com') === false && strpos($data['email'], 'oceandivers.com') === false && strpos($data['email'], 'emoceansports.com') === false )
            {
                $availableAmountCertificates=0;

                foreach($Certificates as $k=>$certificate)
                {
                    if($certificate['customer_id'] == $data['customer_id'] && $certificate['balance'] > 0)
                    {
                        $availableAmountCertificates=$availableAmountCertificates+$certificate['balance'];
                        unset($Certificates[$k]);
                    }
                }
                if($data['points'] > 0 || $availableAmountCertificates > 0)
                {
                    $scheduledResults = $mail->messages->listScheduled($data['email']);
                    //     $scheduledResults = $mail->messages->listScheduled('nsire@msn.com');
                    if(!empty($scheduledResults))
                    {
                        $scheduledID=$scheduledResults[0]['_id'];

                        try {

                            $result = $mail->messages->reschedule($scheduledID, $scheduleDate);
                            print_r($result);

                        } catch(Mandrill_Error $e) {

                            Mage::log('A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage(),null,'MANDRILLERROR,log');
                            throw $e;
                        }
                    }
                }
            }
        }
    }


    /**
     * @return \Mandrill
     */
    private function getMandrillApiInstance()
    {
        return $this->_api->getApi();
    }
}