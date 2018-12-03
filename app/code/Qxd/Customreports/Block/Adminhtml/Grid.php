<?php

namespace Qxd\Customreports\Block\Adminhtml;

use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\Store;

class Grid extends \Magento\Backend\Block\Template
{   

    
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\Framework\App\ResourceConnection $resource
    )
    {
        parent::__construct($context);
        $this->_resource = $resource;
    }

    public function getDatesArray()
    {
        $currentDate=date("Y-m-d H:i:s");
        $newDT=new \DateTime($currentDate);
        $newDT->setTimezone(new \DateTimeZone('America/New_York'));
        $currentDate=$newDT->format('Y-m-d H:i:s');

        $result=array("so_far_today"=>date("Y-m-d H:i:s", strtotime($currentDate)),
            "so_far_today_last_year"=>date("Y-m-d H:i:s", strtotime('-52 week',strtotime($currentDate))),
            "whole_day_last_year"=>date("Y-m-d H:i:s", strtotime('-52 week',strtotime($currentDate))),
            "yesterday"=>date("Y-m-d", strtotime('-1 day',strtotime($currentDate))),
            "yesterday_last_year"=>date("Y-m-d", strtotime('-52 week',strtotime('-1 day',strtotime($currentDate)))),
            "last_week"=>date("Y-m-d", strtotime('-4 week',strtotime($currentDate))),
            "last_week_last_year"=>date("Y-m-d", strtotime('-52 week',strtotime('-4 week',strtotime($currentDate)))));

        return $result;
    }

    public function returnTotals()
    {
        $result=array();

        try{

            $currentDateNotProcessed=date("Y-m-d H:i:s");
            $newDT=new \DateTime($currentDateNotProcessed);
            $newDT->setTimezone(new \DateTimeZone('America/New_York'));
            $currentDate=$newDT->format('Y-m-d H:i:s');

            $curentDateMidnight=date("Y-m-d H:i:s",strtotime($currentDate.' midnight'));
            $currentDateLastYear=date("Y-m-d H:i:s", strtotime('-52 week',strtotime($currentDate)));
            $currentDateMidnightLastYear=date("Y-m-d H:i:s",strtotime($currentDateLastYear.' midnight'));

            $yesterday=date("Y-m-d", strtotime('-1 day',strtotime($currentDate)));
            $yesterdayMidnight=date("Y-m-d H:i:s",strtotime($yesterday.' midnight'));
            $yesterdayLastYear=date("Y-m-d", strtotime('-52 week',strtotime('-1 days')));
            $yesterdayMidnightLastYear=date("Y-m-d H:i:s", strtotime('-52 week',strtotime($yesterday.' midnight')));

            $tomorrow=date("Y-m-d", strtotime('+1 day',strtotime($currentDate)));
            $tomorrowMidnight=date("Y-m-d H:i:s",strtotime($tomorrow.' midnight'));
            $tomorrowLastYear=date("Y-m-d", strtotime('-52 week',strtotime('+1 days')));
            $tomorrowMidnightLastYear=date("Y-m-d H:i:s", strtotime('-52 week',strtotime($tomorrow.' midnight')));

            $last4weeksBegin=date("Y-m-d H:i:s", strtotime('-4 week',strtotime($currentDate)));
            $last4weeksLastYearBegin=date("Y-m-d H:i:s", strtotime('-52 week',strtotime('-4 week',strtotime($currentDate))));

            $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
            // la calve esta en este query, hacerlo bien
            $query = "SELECT created_at,base_grand_total,increment_id FROM `sales_order` WHERE (`store_id` NOT IN(28, 29, 30, 31, 32, 33, 34, 35)) AND (`increment_id` NOT LIKE '%INV%') AND (`increment_id` NOT LIKE '%CS%') ORDER BY created_at DESC ";
            $ordersCollection = $connection->fetchAll($query);


            $result['so_far_today']=array('qty'=>0,'revenue'=>0,'over'=>0,'under'=>0);
            $result['so_far_today_last_year']=array('qty'=>0,'revenue'=>0,'over'=>0,'under'=>0);
            $result['whole_day_last_year']=array('qty'=>0,'revenue'=>0,'over'=>0,'under'=>0);
            $result['yesterday']=array('qty'=>0,'revenue'=>0,'over'=>0,'under'=>0);
            $result['yesterday_last_year']=array('qty'=>0,'revenue'=>0,'over'=>0,'under'=>0);
            $result['last_week']=array('qty'=>0,'revenue'=>0,'over'=>0,'under'=>0);
            $result['last_week_last_year']=array('qty'=>0,'revenue'=>0,'over'=>0,'under'=>0);


            foreach($ordersCollection as $order)
            {
                $orderTotal=$order['base_grand_total'];

                $created_at=$order['created_at'];
                $newDT= new \DateTime($created_at);
                $newDT->setTimezone(new \DateTimeZone('America/New_York'));
                $order_date=$newDT->format('Y-m-d H:i:s');

                if($order_date >= $curentDateMidnight && $order_date <= $currentDate)
                {
                    $type='so_far_today';
                    $result[$type]['qty']=$result[$type]['qty']+1;
                    $result[$type]['revenue']=$result[$type]['revenue']+$orderTotal;
                }
                if($order_date >= $currentDateMidnightLastYear && $order_date <= $currentDateLastYear)
                {
                    $type='so_far_today_last_year';
                    $result[$type]['qty']=$result[$type]['qty']+1;
                    $result[$type]['revenue']=$result[$type]['revenue']+$orderTotal;
                }
                if($order_date >= $currentDateMidnightLastYear && $order_date < $tomorrowMidnightLastYear)
                {
                    $type='whole_day_last_year';
                    $result[$type]['qty']=$result[$type]['qty']+1;
                    $result[$type]['revenue']=$result[$type]['revenue']+$orderTotal;
                }
                if($order_date >= $yesterdayMidnight && $order_date < $curentDateMidnight)
                {
                    $type='yesterday';
                    $result[$type]['qty']=$result[$type]['qty']+1;
                    $result[$type]['revenue']=$result[$type]['revenue']+$orderTotal;
                }
                if($order_date >= $yesterdayMidnightLastYear && $order_date < $currentDateMidnightLastYear)
                {
                    $type='yesterday_last_year';
                    $result[$type]['qty']=$result[$type]['qty']+1;
                    $result[$type]['revenue']=$result[$type]['revenue']+$orderTotal;
                }
                if($order_date >= $last4weeksBegin && $order_date < $curentDateMidnight)
                {
                    $type='last_week';
                    $result[$type]['qty']=$result[$type]['qty']+1;
                    $result[$type]['revenue']=$result[$type]['revenue']+$orderTotal;
                }
                if($order_date >= $last4weeksLastYearBegin && $order_date < $currentDateMidnightLastYear)
                {
                    $type='last_week_last_year';
                    $result[$type]['qty']=$result[$type]['qty']+1;
                    $result[$type]['revenue']=$result[$type]['revenue']+$orderTotal;
                }
            }

            if($result['so_far_today_last_year']['qty'] > 0){
                $overToday=($result['so_far_today']['qty']-$result['so_far_today_last_year']['qty'])/$result['so_far_today_last_year']['qty'];
                $result['so_far_today']['over']=number_format((float)$overToday*100, 2, '.', '');

                $underToday=($result['so_far_today']['revenue']-$result['so_far_today_last_year']['revenue'])/$result['so_far_today_last_year']['revenue'];
                $result['so_far_today']['under']=number_format((float)$underToday*100, 2, '.', '');
            }

            if($result['yesterday_last_year']['qty'] > 0){
                $overYesterday=($result['yesterday']['qty']-$result['yesterday_last_year']['qty'])/$result['yesterday_last_year']['qty'];
                $result['yesterday']['over']=number_format((float)$overYesterday*100, 2, '.', '');

                $underYesterday=($result['yesterday']['revenue']-$result['yesterday_last_year']['revenue'])/$result['yesterday_last_year']['revenue'];
                $result['yesterday']['under']=number_format((float)$underYesterday*100, 2, '.', '');
            }

            if($result['last_week_last_year']['qty'] > 0){
                $overLastWeek=($result['last_week']['qty']-$result['last_week_last_year']['qty'])/$result['last_week_last_year']['qty'];
                $result['last_week']['over']=number_format((float)$overLastWeek*100, 2, '.', '');

                $underLastWeekLastYear=($result['last_week']['revenue']-$result['last_week_last_year']['revenue'])/$result['last_week_last_year']['revenue'];
                $result['last_week']['under']=number_format((float)$underLastWeekLastYear*100, 2, '.', '');
            }

        }catch (Exception $e){ 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_YOY_ERROR.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }

        return $result;
    }

}
