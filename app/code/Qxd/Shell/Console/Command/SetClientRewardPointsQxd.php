<?php

namespace Qxd\Shell\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//ini_set('memory_limit', '-1');

class SetClientRewardPointsQxd extends Command
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
        $this->setName('qxd:setClientRewardPoints_qxd')->setDescription("Set clients' reward points");
    }

    /**
     * {@inheritdoc}
     */
     protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $output->writeln("Start setClientRewardPoints_qxd");

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Reward_Points.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try{

            $fileName="drpointsbalance.csv";
            $fromPath=$this->_directoryList->getRoot()."/feeds/";
            $toPath=$this->_directoryList->getRoot('var')."/QXD_import/RewardPoints_Processed/";
            $updated=array();
            $errors=array();

            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);

            $websiteId  = $this->_storeManager->getWebsite()->getWebsiteId();
            $store = $this->_storeManager->getStore();

            $arrayFileParsed=array();
            if(file_exists($fromPath.$fileName))
            {
                $file = fopen($fromPath.$fileName,"r");

                while(!feof($file))
                {
                    $rowAUX=fgetcsv($file);
                    $arrayFileParsed[]=array('email'=>$rowAUX[0],'firstname'=>$rowAUX[2],'lastname'=>$rowAUX[3],'points'=>$rowAUX[1]);
                }
                fclose($file);
            }

            $countPointsProcessed=count($arrayFileParsed);
            $countPointsIndex= 0;
            if(!empty($arrayFileParsed))
            {
                foreach($arrayFileParsed as $email=>$data)
                {
                    try {

                        if($data['email'] && $data['points'])
                        {
                            if(!$this->customerExists($data['email'],$connection)) { $this->createCustomer($data,$websiteId,$store,$connection,$output); }

                            $points=$data['points'];

                            $query = "UPDATE customer_entity set reward_points=".$points."  WHERE email='".$data['email']."'" ;
                            $connection->query($query);

                            $query = "SELECT entity_id FROM customer_entity WHERE email='".$data['email']."'"." LIMIT 1" ;
                            $customerID = $connection->fetchOne($query);

                            if($customerID)
                            {
                                $query = "UPDATE rewards_customer_index_points set customer_points_usable=".$points." , customer_points_active=".$points."  WHERE customer_id='".$customerID."'" ;
                                $connection->query($query);
                            }

                            $updated[]=$data['email'].' : '.$points;
                        }
                    }
                    catch (Mage_Core_Exception $e) { $errors[]=$data['email'].' : '.$e->getMessage(); }
                    catch(mysqli_sql_exception $e){ print_r($e->getMessage()); }

                    $countPointsIndex++;
                    $output->writeln($countPointsIndex.' of '.$countPointsProcessed);
                }
            }
            rename($fromPath.$fileName, $toPath.date('m-d-Y h:i:s').$fileName);
            /*$emailBody="Points Processed ".count($updated)."\n\n";
            $emailBody=$emailBody.implode("\n",$updated);

            $emailBody="\n\n\n\n";

            $emailBody="Points Not Processed ".count($errors)."\n\n";
            $emailBody=$emailBody.implode("\n",$errors);

            $emailResult = wordwrap($emailBody, 70);
            $from='magenotify@diversdirect.com';
            $subject='Customer Points Import Finish, File: Date:'.date('m-d-Y h:i:s');
            mail("magenotify@diversdirect.com",$subject,$emailResult,"From: $from\n");*/

        }catch(Exception $e){ $logger->info($e->getMessage()); }
        catch(mysqli_sql_exception $e){ $logger->info($e->getMessage()); }

    }

    public function createCustomer($data,$websiteId,$store,$connection,$output)
    {
        $customer = $this->_customerFactory->create();
        $customer->setWebsiteId($websiteId)
            ->setStore($store)
            ->setFirstname($data['firstname'])
            ->setLastname($data['lastname'])
            ->setEmail($data['email'])
            ->setPassword(md5(uniqid(rand(), true)));

        try{
            $customer->save();
            $output->writeln("ID ".$customer->getId());
            $query = "INSERT INTO rewards_customer_index_points (customer_id,customer_points_usable,customer_points_pending_event,customer_points_pending_time,customer_points_pending_approval,customer_points_active)
            VALUES ('".$customer->getId()."',0,0,0,0,0)" ;
            $connection->query($query);
        }
        catch (Exception $e) { 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Reward_Points.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
    }

    public function customerExists($email,$connection)
    {
        $result=false;
        $query = "SELECT email FROM customer_entity WHERE email='".$email."'" ;
        $email = $connection->fetchOne($query);

        if(!empty($email)){ $result=true; }
        return $result;
    }
}

