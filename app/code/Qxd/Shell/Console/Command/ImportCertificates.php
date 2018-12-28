<?php

namespace Qxd\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//ini_set('memory_limit', '-1');

class ImportCertificates extends Command
{   

    public function __construct(
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->_resource = $resource;
        $this->_directoryList = $directoryList;
        $this->_storeManager     = $storeManager;
        $this->_customerFactory  = $customerFactory;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('qxd:importCertificates')->setDescription("Import certificates");
    }

    /**
     * {@inheritdoc}
     */
     protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $output->writeln("Start import certificates");

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_ImportCertificates-Error.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try{
            $this->print_mem($output);

            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            $websiteId  = $this->_storeManager->getWebsite()->getWebsiteId();
            $store = $this->_storeManager->getStore();

            $fromPath=$this->_directoryList->getRoot()."/hidden_feeds/";
            $toPath=$this->_directoryList->getRoot()."/QXD_import/Certificates_Processed/";

            $arrayFileParsed=array();
            $emails=array();

            if(file_exists($fromPath."dr_gc.csv"))
            {
                $file = fopen($fromPath."dr_gc.csv","r");
                fgetcsv($file);

                while(!feof($file))
                {
                    $rowAUX=fgetcsv($file);
                    if(!empty($rowAUX[5]) /*&& ($rowAUX[0] == 'JKSTOTT586@GMAIL.COM')*/)
                    {
                        $status=2;

                        $date1 = new \DateTime("now");
                        $date2 = new \DateTime($rowAUX[5]);
                        if($date1 > $date2){ $status=5; }

                        $newExpirationDate=\DateTime::createFromFormat('m/d/Y', $rowAUX[5]);

                        $arrayFileParsed["'".$rowAUX[1]."'"]=array(
                            'gift_code'=>$rowAUX[1],
                            'amount'=>$rowAUX[2],
                            'balance'=>$rowAUX[3],
                            'expired_at'=>$newExpirationDate->format('Y-m-d'),
                            'customer_email'=>$rowAUX[0],
                            'customer_id'=>0,
                            'is_certificate'=>"1",
                            'status'=>$status
                        );

                        $emails[]="'".$rowAUX[0]."'";
                    }
                }
                fclose($file);
            }

            if(!empty($arrayFileParsed))
            {
                $giftCardUpdated=array();
                $giftCardCreated=array();
                $customersCreated=array();

                $customers=$this->verifyThatCustomersExist($emails,$connection);
                $codes=array_keys($arrayFileParsed);

                $giftCards=$this->getGiftVouchersData($connection,$codes);
                $countGiftCards=count($giftCards);
                $index=0;

                $giftVouchersToProcess=array();
                $initialAmounts=array();

                foreach($giftCards as $row)
                {
                    $giftCode="'".$row['gift_code']."'";

                    if(isset($arrayFileParsed[$giftCode]))
                    {
                        $data=$arrayFileParsed[$giftCode];
                        $data['giftvoucher_id']=$row['giftvoucher_id'];
                        $data['gift_code']=$giftCode;

                        $customerVerification=$this->customerExists($data['customer_email'],$customers,$websiteId,$store);
                        if($customerVerification['new'])
                        {
                            $customersCreated[]=$data['customer_email'];
                            $customers[strtolower($data['customer_email'])]=$customerVerification['id'];
                        }

                        /*$data['customer_id'] = $customerVerification['id'];
                        $giftVouchersToProcess[]= $data;*/

                        $giftVouchersToProcess[]="(".implode(',',array($data['giftvoucher_id'].",". $data['gift_code'].",".$data['balance'],"'".$data['expired_at']."'","'".$data['customer_email']."'",$customerVerification['id'],$data['is_certificate'],$data['status'])).")";

                        $initialAmounts[$data['gift_code']]=$data['amount'];

                        unset($arrayFileParsed[$data['gift_code']]);
                    }

                }
    
                //$logger->info(print_r($giftVouchersToProcess,true)); 

                if(!empty($giftVouchersToProcess))
                {
                    $this->processGiftVoucher('update',$giftVouchersToProcess,$initialAmounts,$connection, $logger);
                    $giftCardUpdated=array_keys($initialAmounts);
                }



                $countGiftCards=count($arrayFileParsed);
                $index=0;

                $giftVouchersToProcess=array();
                $initialAmounts=array();

                foreach($arrayFileParsed as $data)
                {
                    $data['gift_code']="'".$data['gift_code']."'";
                    $customerVerification=$this->customerExists($data['customer_email'],$customers,$websiteId,$store);
                    if($customerVerification['new'])
                    {
                        $customersCreated[]=$data['customer_email'];
                        $customers[strtolower($data['customer_email'])]=$customerVerification['id'];
                    }

                    $giftVouchersToProcess[]="(".implode(',',array($data['gift_code'].",".$data['balance'],"'".$data['expired_at']."'","'".$data['customer_email']."'",$customerVerification['id'],$data['is_certificate'],$data['status'],"'".'USD'."'",1)).")";
                    $initialAmounts[$data['gift_code']]=$data['amount'];

                }

                //$logger->info(print_r($giftVouchersToProcess,true));
                if(!empty($giftVouchersToProcess))
                {
                    $this->processGiftVoucher('insert',$giftVouchersToProcess,$initialAmounts,$connection, $logger);
                    $giftCardCreated=array_keys($initialAmounts);
                }

                rename($fromPath."dr_gc.csv", $toPath.date('m-d-Y h:i:s')."_dr_gc.csv");

                $emailBody="Certificates Updated ".count($giftCardUpdated)."\n\n";
                $emailBody=$emailBody."\n\n";
                
                $emailBody=$emailBody."Certificates Created ".count($giftCardCreated)."\n\n";
                $emailBody=$emailBody."\n\n";

                $emailBody=$emailBody."Customers Created ".count($customersCreated)."\n\n";
                $emailBody=$emailBody."\n\n";

                $output->writeln("Done");

                $this->sendEmail($emailBody);
            }else{
                $emailBody="No certificates found at ".date('m-d-Y h:i:s')."\n\n";
                $this->sendEmail($emailBody);
            }

            $this->print_mem($output);

        }catch (Exception $e){ 
            $logger->info($e->getMessage().' '.$e->getLine()); 
        }
    }

    public function verifyThatCustomersExist($emails,$readConnection)
    {
        $result=array();

        $query = "SELECT entity_id,email FROM customer_entity WHERE email IN (".implode(',',$emails).")" ;
        $resultQuery = $readConnection->fetchAll($query);
        foreach($resultQuery as $data){ $result[strtolower($data['email'])]=$data['entity_id']; }

        return $result;
    }

    public function customerExists($email,$customers,$websiteId,$store)
    {
        $result=array('id'=>'','new'=>false);

        if(isset($customers[strtolower($email)])){ $result['id']=$customers[strtolower($email)]; }
        else
        {
            $createdCustomerID=$this->createCustomer($email,$websiteId,$store,$customers);
            $result['id']=$createdCustomerID;
            $result['new']=true;
        }

        return $result;
    }

    public function createCustomer($email,$websiteId,$store,$customers)
    {
        $result='';

        try{
            $customer = $this->_customerFactory->create();
            $customer->setWebsiteId($websiteId)
            ->setStore($store)
            ->setFirstname('Guest')
            ->setLastname('Customer')
            ->setEmail($email)
            ->setPassword(md5(uniqid(rand(), true)));
            $customer->save();
            //$customer->sendNewAccountEmail();

            $result=$customer->getId();
        }
        catch (Exception $e) { 
            print_r($customers); print_r($email."\n"); Zend_Debug::dump($e->getMessage()); 
        }

        return $result;
    }

    public function getGiftVouchersData($readConnection,$codes)
    {
        $result=array();

        $querySelect="SELECT giftvoucher_id,gift_code,customer_id,customer_email,status,balance FROM giftvoucher WHERE gift_code IN (".implode(',',$codes).")";
        $result=$readConnection->fetchAll($querySelect);

        return $result;
    }

    public function print_mem($output)
    {
        /* Currently used memory */
        $mem_usage = memory_get_usage();

        /* Currently used memory including inactive pages */
        $mem_full_usage = memory_get_usage(TRUE);

        /* Peak memory consumption */
        $mem_peak = memory_get_peak_usage();

        $output->writeln("This scrip now uses " . round($mem_usage / 1024) . "KB of memory.\n");
        $output->writeln("Real usage: " . round($mem_usage / 1024) . "KB.\n");
        $output->writeln("Peak usage: " . round($mem_peak / 1024) . "KB.\n\n");

    }


    public function processGiftVoucher($type,$data,$initialAmounts,$connection, $logger)
    {   
        $historyAction=2;
        try
        {
            switch($type)
            {
                case 'insert':
                {
                    $giftvoucherQuery="INSERT INTO giftvoucher (gift_code,balance,expired_at,customer_email,customer_id,is_certificate,status,currency,store_id)
                                                                    VALUES ".implode(',',$data);
                    $historyAction=1;

                }break;
                case 'update':
                {

                    $giftvoucherQuery="INSERT INTO giftvoucher (giftvoucher_id, gift_code,balance,expired_at,customer_email,customer_id,is_certificate,status)
                                                                    VALUES ".implode(',',$data)."
                                                                    ON DUPLICATE KEY UPDATE
                                                                    balance = VALUES (balance),
                                                                    expired_at = VALUES (expired_at),
                                                                    customer_email = VALUES (customer_email),
                                                                    customer_id = VALUES (customer_id),
                                                                    is_certificate = VALUES (is_certificate),
                                                                    status = VALUES (status)
                                                                    ";
                }break;
            }

            $connection->query($giftvoucherQuery);
            $codes=array_keys($initialAmounts);
            $codesUpdated=$this->getGiftVouchersData($connection,$codes);

            $historyData=array();
            foreach($codesUpdated as $codeData)
            {
                $historyData[]="(".implode(',',array($codeData['giftvoucher_id'],$historyAction,"'".date("Y-m-d h:i:s")."'",$initialAmounts["'".$codeData['gift_code']."'"],"'".'USD'."'",$codeData['status']
                    ,"'". 'Updated by the script to import certificates'."'","'". 'Updated by Import process'."'",$codeData['balance'],$codeData['customer_id'],"'".$codeData['customer_email']."'")).")";
            }

            if(!empty($historyData))
            {
                $queryUpdateGiftVoucherHistory = "INSERT INTO giftvoucher_history (giftvoucher_id,action,created_at,amount,currency,status, comments,extra_content,balance,customer_id,customer_email) VALUES " . implode(',', $historyData);
                $connection->query($queryUpdateGiftVoucherHistory);
            }

        }catch (Exception $e){ 
            $logger->info($e->getMessage().' '.$e->getLine());
        }

    }


    public function sendEmail($emailBody)
    {
        $emailResult = wordwrap($emailBody, 70);
        $from='magenotify@diversdirect.com';
        $subject='Certificates Import Finished, File: Date:'.date('m-d-Y h:i:s');
        mail("magemodules@diversdirect.com",$subject,$emailResult,"From: $from\n");
        //mail("jvillalobos@qxdev.com",$subject,$emailResult,"From: $from\n");
    }
}

