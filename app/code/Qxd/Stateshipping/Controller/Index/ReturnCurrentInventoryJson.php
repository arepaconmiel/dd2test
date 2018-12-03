<?php
 
namespace Qxd\Stateshipping\Controller\Index;

 
class ReturnCurrentInventoryJson extends \Magento\Framework\App\Action\Action
{
    public function __construct(
            \Magento\Framework\App\Action\Context $context,
            \Qxd\Memcached\Helper\Data $memcached,
            \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
            \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
        )
    {
        $this->_memcached = $memcached;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }
 
    public function execute()
    {     
        $_memcached = $this->_memcached->initMemcached();
        $result = $this->_resultJsonFactory->create();
        
        if(!$_memcached || !$_memcached->get('_InventoryJason')){

            $availableStates = array(
                "1"=>"Alabama",
                "2"=>"Alaska",
                "3"=>"American Samoa",
                "4"=>"Arizona",
                "5"=>"Arkansas",
                "6"=>"Armed Forces Africa",
                "7"=>"Armed Forces Americas",
                "8"=>"Armed Forces Canada",
                "9"=>"Armed Forces Europe",
                "10"=>"Armed Forces Middle East",
                "11"=>"Armed Forces Pacific",
                "12"=>"California",
                "13"=>"Colorado",
                "14"=>"Connecticut",
                "15"=>"Delaware",
                "16"=>"District Of Columbia",
                "17"=>"Federated States Of Micronesia",
                "18"=>"Florida",
                "19"=>"Georgia",
                "20"=>"Guam",
                "21"=>"Hawaii",
                "22"=>"Idaho",
                "23"=>"Illinois",
                "24"=>"Indiana",
                "25"=>"Iowa",
                "26"=>"Kansas",
                "27"=>"Kentucky",
                "28"=>"Louisiana",
                "29"=>"Maine",
                "30"=>"Marshall Islands",
                "31"=>"Maryland",
                "32"=>"Massachusetts",
                "33"=>"Michigan",
                "34"=>"Minnesota",
                "35"=>"Mississippi",
                "36"=>"Missouri",
                "37"=>"Montana",
                "38"=>"Nebraska",
                "39"=>"Nevada",
                "40"=>"New Hampshire",
                "41"=>"New Jersey",
                "42"=>"New Mexico",
                "43"=>"New York",
                "44"=>"North Carolina",
                "45"=>"North Dakota",
                "46"=>"Northern Mariana Islands",
                "47"=>"Ohio",
                "48"=>"Oklahoma",
                "49"=>"Oregon",
                "50"=>"Palau",
                "51"=>"Pennsylvania",
                "52"=>"Puerto Rico",
                "53"=>"Rhode Island",
                "54"=>"South Carolina",
                "55"=>"South Dakota",
                "56"=>"Tennessee",
                "57"=>"Texas",
                "58"=>"Utah",
                "59"=>"Vermont",
                "60"=>"Virgin Islands",
                "61"=>"Virginia",
                "62"=>"Washington",
                "63"=>"West Virginia",
                "64"=>"Wisconsin",
                "65"=>"Wyoming",
                "66"=>"out");

            foreach($availableStates as $state)
            {
                $stateParsed=str_replace(" ","_",strtolower($state));
                $states_parsed[$state] = $this->_scopeConfig->getValue(
                    'stateshipping_options/configure_states/stateshipping_' . $stateParsed,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
            }
            $result = json_encode($states_parsed);

            if($_memcached){ $_memcached->set('_InventoryJason',$result); }
        }else{ 
            $result=$_memcached->get('_InventoryJason'); 
        }

        $this->getResponse()->setBody($result);
    }
} 
