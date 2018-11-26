<?php
namespace Qxd\Memcached\Model;
class Connector extends \Magento\Framework\Model\AbstractModel 
{
    private static $instance;
    private static $scopeConfig;
    public $memcachedConn;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        self::$scopeConfig = $scopeConfig;
    }

    /**
     *
     * @return Connector - Singleton
     */
    public static function getInstance(){
        if (self::$instance == null){
            $className = __CLASS__;
            self::$instance = new $className(self::$scopeConfig);
        }

        return self::$instance;
    }

    /**
     *
     * @return \Memcached
     */
    private static function initConnection(){
        $mem = self::getInstance();
        $mem->memcachedConn = new \Memcached;
        $host = self::$scopeConfig->getValue(
            'memcached_options/configure_memcached/memcached_host',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $post = self::$scopeConfig->getValue(
            'memcached_options/configure_memcached/memcached_port',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $mem->memcachedConn->addServer($host, $post);
        return $mem;
    }

    /**
     * @return Memcached connection
     */
    public static function getMemcachedConn() {
        try {
            $memcached = self::initConnection();
            return $memcached->memcachedConn;
        } catch (Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/QXD_Memcached_Error.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('observer before save product');
            return null;
        }
    }
}