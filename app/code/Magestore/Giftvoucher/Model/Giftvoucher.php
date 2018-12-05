<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model;

use Magestore\Giftvoucher\Model\Status;

/**
 * Giftvoucher Model
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class Giftvoucher extends \Magento\Rule\Model\AbstractModel implements \Magestore\Giftvoucher\Api\Data\GiftvoucherInterface
{

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    protected $_conditionsInstance;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    protected $_actionsInstance;

    /**
     * @var \Magestore\Giftvoucher\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Email\Model\Template
     */
    protected $_emailTemplate;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_dateTime;

    /**
     * @var array
     */
    protected $_calculators = [];

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    protected $_calculatorFactory;

    /**
     * @var \Magestore\Giftvoucher\Model\HistoryFactory
     */
    protected $_historyFactory;
    
    /**
     * @var \Magestore\Giftvoucher\Api\HistoryRepositoryInterfaceFactory
     */
    protected $_historyRepositoryFactory;
    
    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Construct Giftvoucher
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $conditionsInstance
     * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionsInstance
     * @param \Magestore\Giftvoucher\Helper\Data $helperData
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Email\Model\Template $emailTemplate
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Framework\Math\CalculatorFactory $calculatorFactory
     * @param \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory
     * @param \Magestore\Giftvoucher\Api\HistoryRepositoryInterfaceFactory $historyRepositoryFactory
     * @param \Magento\Directory\Model\CurrencyFactory $_currencyFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @internal param \Magestore\Giftvoucher\Api\HistoryRepositoryInterface $historyRepository
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $conditionsInstance,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $actionsInstance,
        \Magestore\Giftvoucher\Helper\Data $helperData,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Email\Model\Template $emailTemplate,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Framework\Math\CalculatorFactory $calculatorFactory,
        \Magestore\Giftvoucher\Model\HistoryFactory $historyFactory,
        \Magestore\Giftvoucher\Api\HistoryRepositoryInterfaceFactory $historyRepositoryFactory,
        \Magento\Directory\Model\CurrencyFactory $_currencyFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
    
        $this->_conditionsInstance = $conditionsInstance;
        $this->_actionsInstance = $actionsInstance;
        $this->_helperData = $helperData;
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_urlBuilder = $urlBuilder;
        $this->_emailTemplate = $emailTemplate;
        $this->_dateTime = $dateTime;
        $this->_calculatorFactory = $calculatorFactory;
        $this->_historyFactory = $historyFactory;
        $this->_historyRepositoryFactory = $historyRepositoryFactory;
        $this->_currencyFactory = $_currencyFactory;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $localeDate,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magestore\Giftvoucher\Model\ResourceModel\Giftvoucher');
    }

    /**
     * Load Gift Card by gift code
     *
     * @param string $code
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     */
    public function loadByCode($code)
    {
        return $this->load($code, 'gift_code');
    }

    /**
     * @param int $id
     * @param null $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        parent::load($id, $field);

        $timeSite = date(
            "Y-m-d H:i:s",
            $this->_dateTime->timestamp(time())
        );
        if ($this->getIsDeleted()) {
            return $this;
        }

        if ($this->getStatus() == Status::STATUS_ACTIVE
            && $this->getExpiredAt() && $this->getExpiredAt() < $timeSite
        ) {
            $this->setStatus(Status::STATUS_EXPIRED);
        }
        return $this;
    }

    /**
     * Get rule condition combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\CombineFactory
     */
    public function getConditionsInstance()
    {
        return $this->_conditionsInstance->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
     */
    public function getActionsInstance()
    {
        return $this->_actionsInstance->create();
    }

    /**
     * Initialize rule model data from array
     *
     * @param array $rule
     * @return $this
     * @internal param array $data
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions([])->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions([])->loadArray($arr['actions'][1], 'actions');
        }

        return $this;
    }

    /**
     * @param $price
     * @param string $type
     * @param bool $negative
     * @return mixed
     */
    public function roundPrice($price, $type = 'regular', $negative = false)
    {
        if ($price) {
            if (!isset($this->_calculators[$type])) {
                $this->_calculators[$type] = $this->_calculatorFactory->create(['scope' => $this->_storeManager->getStore()]);
            }
            $price = $this->_calculators[$type]->deltaRound($price, $negative);
        }
        return $price;
    }

    /**
     * Get the base balance of gift code
     *
     * @param string $storeId
     * @return float
     */
    public function getBaseBalance($storeId = null)
    {
        if (!$this->hasData('base_balance')) {
            $baseBalance = 0;
            if ($rate = $this->_storeManager->getStore($storeId)
                ->getBaseCurrency()->getRate($this->getData('currency'))
            ) {
                $baseBalance = $this->getBalance() / $rate;
            }
            $this->setData('base_balance', $baseBalance);
        }
        return $this->getData('base_balance');
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function beforeSave()
    {
        parent::beforeSave();

        $timeSite = date(
            "Y-m-d H:i:s",
            $this->_dateTime->timestamp(time())
        );
        if (!$this->getId()) {
            $this->setAction(\Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE);
        }

        if ($this->getStoreId() == null) {
            $this->setStoreId(0);
        }

        if (!$this->getStatus()) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_PENDING);
        }

        if ($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_USED
            && $this->roundPrice($this->getBalance()) > 0
        ) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE);
        }

        if ($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
            && $this->roundPrice($this->getBalance()) == 0
        ) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_USED);
        }

        if (($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_ACTIVE
                || $this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_USED
                || $this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_PENDING)
            && $this->getExpiredAt() && $this->getExpiredAt() < $timeSite
        ) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_EXPIRED);
        }

        if ($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_EXPIRED
            && $this->getExpiredAt() && $this->getExpiredAt() > date('Y-m-d')
        ) {
            $this->setExpiredAt(date('Y-m-d'));
        }

        if ($this->getStatus() == \Magestore\Giftvoucher\Model\Status::STATUS_EXPIRED
            && !$this->getExpiredAt()
        ) {
            $this->setExpiredAt(date('Y-m-d'));
        }

        if ($this->getExpiredAt() && $this->getExpiredAt() < date('Y-m-d')) {
            $this->setStatus(\Magestore\Giftvoucher\Model\Status::STATUS_EXPIRED);
        }

        if (!$this->getGiftCode()) {
            $this->setGiftCode(
                $this->_scopeConfig->getValue('giftvoucher/general/pattern')
            );
        }
        if ($this->_codeIsExpression()) {
            $this->setGiftCode($this->_getGiftCode());
        } else {
            if ($this->getAction() == \Magestore\Giftvoucher\Model\Actions::ACTIONS_CREATE) {
                if ($this->getResource()->giftcodeExist($this->getGiftCode())) {
                    throw new \Exception(__('Gift code is existed!'));
                }
            }
        }

        if (!$this->_registry->registry('giftvoucher_conditions')) {
            $this->_registry->register('giftvoucher_conditions', true);
        } else {
            if (!$this->getGenerateGiftcode()) {
                $data = $this->getData();
                if (isset($data['conditions_serialized'])) {
                    unset($data['conditions_serialized']);
                }
                if (isset($data['actions_serialized'])) {
                    unset($data['actions_serialized']);
                }
                $this->setData($data);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function afterSave()
    {
        parent::afterSave();

        if ($this->getIncludeHistory() && $this->getAction()) {
            $history = $this->_historyFactory->create()
                ->setData($this->getData())
                ->setData(
                    'created_at',
                    (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
                );
            if ($this->getAction() == \Magestore\Giftvoucher\Model\Actions::ACTIONS_UPDATE
                || $this->getAction() == \Magestore\Giftvoucher\Model\Actions::ACTIONS_MASS_UPDATE
            ) {
                $history->setData('customer_id', null)
                    ->setData('customer_email', null)
                    ->setData('amount', $this->getBalance());
            }

            try {
                $this->_historyRepositoryFactory->create()->save($history);
            } catch (\Exception $e) {
            }
        }

        return $this;
    }

    /**
     * @return bool|int
     */
    protected function _codeIsExpression()
    {
        return $this->_helperData->isExpression($this->getGiftCode());
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function _getGiftCode()
    {
        $helper = $this->_helperData;
        $code = $helper->calcCode($this->getGiftCode());
        $times = 10;
        while ($this->getResource()->giftcodeExist($code) && $times) {
            $code = $helper->calcCode($this->getGiftCode());
            $times--;
            if ($times == 0) {
                throw new \Exception(__('Exceeded maximum retries to find available random gift card code!'));
            }
        }
        return $code;
    }

    /**
     * @param null $session
     * @return $this
     */
    public function addToSession($session = null)
    {
        if (is_null($session)) {
            $session = $this->_helperData->getCheckoutSession();
        }
        if ($codes = $session->getGiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesArray[] = $this->getGiftCode();
            $codes = implode(',', array_unique($codesArray));
        } else {
            $codes = $this->getGiftCode();
        }
        $session->setGiftCodes($codes);
        return $this;
    }

    /**
     * @return $this
     */
    public function sendEmail()
    {
        $store = $this->_storeManager->getStore($this->getStoreId());
        $storeId = $store->getStoreId();
        $mailSent = 0;
        if ($this->getCustomerEmail()) {
            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_helperData->getEmailConfig('self', $storeId)
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'store' => $store,
                        'giftvoucher' => $this,
                        'balance' => $this->getGiftcodeBalance(),
                        'status' => $this->getStatusLabel(),
                        'noactive' => ($this->getStatus() == Status::STATUS_ACTIVE)
                            ? 0 : 1,
                        'expiredat' => $this->getExpiredAt() ?
                            $this->_dateTime->date('M d, Y', $this->getExpiredAt()) : '',
                        'message' => $this->getFormatedMessage(),
                        'note' => $this->getEmailNotes(),
                        'description' => $this->getDescription(),
                        'logo' => $this->getPrintLogo(),
                        'url' => $this->getPrintTemplate(),
                        'secure_key' => base64_encode($this->getGiftCode() . '$' . $this->getId()),
                    ]
                )->setFrom(
                    $this->_helperData->getEmailConfig('sender', $storeId)
                )->addTo(
                    $this->getCustomerEmail(),
                    $this->getCustomerName()
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->_logger->critical($ex);
            }
            $mailSent++;
        }
        if ($this->getRecipientEmail()) {
            $mailSent += $this->sendEmailToRecipient();
        }
        if ($this->getRecipientEmail() || $this->getCustomerEmail()) {
            try {
                if ($this->getData('recipient_address')) {
                    $this->setIsSent(2);
                } else {
                    $this->setIsSent(true);
                }
                if (!$this->getNotResave()) {
                    $this->save();
                }
            } catch (\Exception $ex) {
                $this->_logger->critical($ex);
            }
        }

        $this->setEmailSent($mailSent);
        return $this;
    }

    /**
     * Send email to Gift Voucher Receipient
     *
     * @return int The number of email sent
     */
    public function sendEmailToRecipient()
    {
        $allowStatus = explode(',', $this->_helperData->getEmailConfig('only_complete', $this->getStoreId()));
        if (!is_array($allowStatus)) {
            $allowStatus = array();
        }
        if ($this->getRecipientEmail() && !$this->getData('dont_send_email_to_recipient')
            && in_array($this->getStatus(), $allowStatus)
        ) {
            try {
                $store = $this->_storeManager->getStore($this->getStoreId());
                $storeId = $store->getStoreId();

                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_helperData->getEmailConfig('template', $storeId)
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'store' => $store,
                        'giftvoucher' => $this,
                        'balance' => $this->getGiftcodeBalance(),
                        'status' => $this->getStatusLabel(),
                        'noactive' => ($this->getStatus() == Status::STATUS_ACTIVE)
                            ? 0 : 1,
                        'expiredat' => $this->getExpiredAt() ?
                            $this->_dateTime->date('M d, Y', $this->getExpiredAt()) : '',
                        'message' => $this->getFormatedMessage(),
                        'note' => $this->getEmailNotes(),
                        'logo' => $this->getPrintLogo(),
                        'url' => $this->getPrintTemplate(),
                        'addurl' => $store->getBaseUrl() . '/giftvoucher/index/addlist/giftvouchercode/'. $this->getGiftCode(),
                        'secure_key' => base64_encode($this->getGiftCode() . '$' . $this->getId())
                    ]
                )->setFrom(
                    $this->_helperData->getEmailConfig('sender', $storeId)
                )->addTo(
                    $this->getRecipientEmail(),
                    $this->getRecipientName()
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->_logger->critical($ex);
            }

            try {
                if (!$this->getData('recipient_address')) {
                    $this->setIsSent(true);
                } else {
                    $this->setIsSent(2);
                }
                if (!$this->getNotResave()) {
                    $this->save();
                }
            } catch (\Exception $ex) {
                $this->_logger->critical($ex);
            }
            return 1;
        }
        return 0;
    }

    /**
     * Send the success notification email
     *
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     */
    public function sendEmailSuccess()
    {
        if ($this->getCustomerEmail()) {
            $store = $this->_storeManager->getStore($this->getStoreId());
            $storeId = $store->getStoreId();

            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_helperData->getEmailConfig('template_success', $storeId)
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'name' => $this->getCustomerName(),
                    ]
                )->setFrom(
                    $this->_helperData->getEmailConfig('sender', $storeId)
                )->addTo(
                    $this->getCustomerEmail(),
                    $this->getCustomerName()
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->_logger->critical($ex);
            }
        }
        return $this;
    }

    /**
     * Send the refund notification email
     *
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     */
    public function sendEmailRefundToRecipient()
    {
        if ($this->getRecipientEmail() && !$this->getData('dont_send_email_to_recipient')) {
            $store = $this->_storeManager->getStore($this->getStoreId());
            $storeId = $store->getStoreId();
            try {
                $transport = $this->_transportBuilder->setTemplateIdentifier(
                    $this->_helperData->getEmailConfig('template_refund', $storeId)
                )->setTemplateOptions(
                    ['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId]
                )->setTemplateVars(
                    [
                        'store' => $store,
                        'sendername' => $this->getCustomerName(),
                        'receivename' => $this->getRecipientName(),
                        'code' => $this->getGiftCode(),
                        'balance' => $this->getGiftcodeBalance(),
                        'status' => $this->getStatusLabel(),
                        'message' => $this->getFormatedMessage(),
                        'description' => $this->getDescription(),
                        'addurl' => $this->_urlBuilder->getUrl('giftvoucher/index/addlist', array(
                            'giftvouchercode' => $this->getGiftCode()
                        )),
                    ]
                )->setFrom(
                    $this->_helperData->getEmailConfig('sender', $storeId)
                )->addTo(
                    $this->getRecipientEmail(),
                    $this->getRecipientName()
                )->getTransport();
                $transport->sendMessage();
            } catch (\Magento\Framework\Exception\MailException $ex) {
                $this->_logger->critical($ex);
            }
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusLabel()
    {
        $statusArray = \Magestore\Giftvoucher\Model\Status::getOptionArray();
        return $statusArray[$this->getStatus()];
    }

    /**
     * @return mixed
     */
    public function getFormatedMessage()
    {
        return str_replace("\n", "<br/>", $this->getMessage());
    }

    /**
     * Get the email notes
     *
     * @return string
     */
    public function getEmailNotes()
    {
        if (!$this->hasData('email_notes')) {
            $notes = $this->_scopeConfig->getValue(
                'giftvoucher/email/note',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->getStoreId()
            );
            $notes = str_replace(array(
                '{store_url}',
                '{store_name}',
                '{store_address}'

            ), array(
                $this->_storeManager->getStore($this->getStoreId())->getBaseUrl(),
                $this->_storeManager->getStore($this->getStoreId())->getFrontendName(),
                $this->_scopeConfig->getValue(
                    'general/store_information/address',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $this->getStoreId()
                )

            ), $notes);
            $this->setData('email_notes', $notes);
        }
        return $this->getData('email_notes');
    }

    /**
     * Get the print logo
     *
     * @return string|boolean
     */
    public function getPrintLogo()
    {
        $image = $this->_scopeConfig->getValue('giftvoucher/print_voucher/logo', 'store', $this->getStoreId());
        if ($image) {
            $image = $this->_storeManager->getStore($this->getStoreId())->getBaseUrl('media')
                . 'giftvoucher/pdf/logo/' . $image;
            return $image;
        }
        return false;
    }

    /**
     * Returns the formatted balance
     *
     * @return string
     */
    public function getBalanceFormated()
    {
        $currency = $this->_currencyFactory->create()->load($this->getCurrency());
        return $currency->format($this->getBalance());
    }
    
    /**
     * Gift code balance with currency format
     *
     * @return string
     */
    public function getGiftcodeBalance()
    {
        $currency = $this->_currencyFactory->create()->load($this->getCurrency());
        return $currency->format($this->getBalance(), [], false);
    }

    /**
     * Get the print notes
     *
     * @return string
     */
    public function getPrintNotes()
    {
        if (!$this->hasData('print_notes')) {
            $notes = $this->_scopeConfig->getValue('giftvoucher/print_voucher/note', 'store', $this->getStoreId());
            $notes = str_replace(
                array(
                    '{store_url}',
                    '{store_name}',
                    '{store_address}'
                ),
                array(
                    '<span class="print-notes">' . $this->_storeManager->getStore($this->getStoreId())->getBaseUrl()
                    . '</span>',
                    '<span class="print-notes">' . $this->_storeManager->getStore($this->getStoreId())->getFrontendName() .
                    '</span>',
                    '<span class="print-notes">' . $this->_scopeConfig->getValue(
                        'general/store_information/address',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $this->getStoreId()
                    ) . '</span>'
                ),
                $notes
            );
            $this->setData('print_notes', $notes);
        }
        return $this->getData('print_notes');
    }

    /**
     * Get the list customer that used this code
     *
     * @return array
     */
    public function getCustomerIdsUsed()
    {
        $collection = $this->_objectManager->create('Magestore\Giftvoucher\Model\ResourceModel\History\Collection')
            ->addFieldToFilter('main_table.giftvoucher_id', $this->getId())
            ->addFieldToFilter('main_table.action', \Magestore\Giftvoucher\Model\Actions::ACTIONS_SPEND_ORDER);

        $collection->joinSalesOrder();
        $customerIds = array();
        foreach ($collection as $item) {
            $customerIds[] = $item->getData('order_customer_id');
        }
        return $customerIds;
    }

    /**
     * Check gift code is valid in current website
     * 
     * @param null|int $storeId
     * @return boolean
     */
    public function isValidWebsite($storeId = null)
    {
        if ($this->getStoreId()) {
            $currentWebsite = $this->_storeManager->getStore($storeId)->getWebsiteId();
            $giftWebsite = $this->_storeManager->getStore($this->getStoreId())->getWebsiteId();
            return $currentWebsite == $giftWebsite;
        }
        return true;
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getGiftvoucherId()
    {
        return $this->getData(self::GIFTVOUCHER_ID);
    }

    /**
     * Set ID
     *
     * @param int $giftvoucherId
     * @return GiftvoucherInterface
     */
    public function setGiftvoucherId($giftvoucherId)
    {
        return $this->setData(self::GIFTVOUCHER_ID, $giftvoucherId);
    }

    /**
     * Get Gift code
     *
     * @return string|null
     */
    public function getGiftCode()
    {
        return $this->getData(self::GIFT_CODE);
    }

    /**
     * Set Gift code
     *
     * @param string $giftCode
     * @return GiftvoucherInterface
     */
    public function setGiftCode($giftCode)
    {
        return $this->setData(self::GIFT_CODE, $giftCode);
    }

    /**
     * Get Gift code balance
     *
     * @return string|null
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * Set Gift code balance
     *
     * @param string $balance
     * @return GiftvoucherInterface
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }

    /**
     * Get Gift code currency
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->getData(self::CURRENCY);
    }

    /**
     * Set Gift code currency
     *
     * @param string $currency
     * @return GiftvoucherInterface
     */
    public function setCurrency($currency)
    {
        return $this->setData(self::CURRENCY, $currency);
    }

    /**
     * Get Gift code status
     *
     * @return int|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set Gift code status
     *
     * @param int $status
     * @return GiftvoucherInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Gift code status
     *
     * @return string|null
     */
    public function getExpiredAt()
    {
        return $this->getData(self::EXPIRED_AT);
    }

    /**
     * Set Gift code $expiredAt
     *
     * @param string $expiredAt
     * @return GiftvoucherInterface
     */
    public function setExpiredAt($expiredAt)
    {
        return $this->setData(self::EXPIRED_AT, $expiredAt);
    }

    /**
     * Get Gift code $customerId
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * Set Gift code $customerId
     *
     * @param int $customerId
     * @return GiftvoucherInterface
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Get Gift code $customerName
     *
     * @return string|null
     */
    public function getCustomerName()
    {
        return $this->getData(self::CUSTOMER_NAME);
    }

    /**
     * Set Gift code $customerName
     *
     * @param string $customerName
     * @return GiftvoucherInterface
     */
    public function setCustomerName($customerName)
    {
        return $this->setData(self::CUSTOMER_NAME, $customerName);
    }

    /**
     * Get Gift code $customerEmail
     *
     * @return string|null
     */
    public function getCustomerEmail()
    {
        return $this->getData(self::CUSTOMER_EMAIL);
    }

    /**
     * Set Gift code $customerEmail
     *
     * @param $customerEmail
     * @return GiftvoucherInterface
     * @internal param string $customerName
     */
    public function setCustomerEmail($customerEmail)
    {
        return $this->setData(self::CUSTOMER_EMAIL, $customerEmail);
    }

    /**
     * Get Gift code $recipientName
     *
     * @return string|null
     */
    public function getRecipientName()
    {
        return $this->getData(self::RECIPIENT_NAME);
    }
    /**
     * Set Gift code $recipientName
     *
     * @param string $recipientName
     * @return GiftvoucherInterface
     */
    public function setRecipientName($recipientName)
    {
        return $this->setData(self::RECIPIENT_NAME, $recipientName);
    }

    /**
     * Get Gift code $customerEmail
     *
     * @return string|null
     */
    public function getRecipientEmail()
    {
        return $this->getData(self::RECIPIENT_EMAIL);
    }

    /**
     * Set Gift code $recipientEmail
     *
     * @param string $recipientEmail
     * @return GiftvoucherInterface
     */
    public function setRecipientEmail($recipientEmail)
    {
        return $this->setData(self::RECIPIENT_EMAIL, $recipientEmail);
    }

    /**
     * Get Gift code $recipientAddress
     *
     * @return string|null
     */
    public function getRecipientAddress()
    {
        return $this->getData(self::RECIPIENT_ADDRESS);
    }

    /**
     * Set Gift code $recipientAddress
     *
     * @param string $recipientAddress
     * @return GiftvoucherInterface
     */
    public function setRecipientAddress($recipientAddress)
    {
        return $this->setData(self::RECIPIENT_ADDRESS, $recipientAddress);
    }

    /**
     * Get Gift code $message
     *
     * @return string|null
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * Set Gift code $message
     *
     * @param string $message
     * @return GiftvoucherInterface
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * Get Gift code $storeId
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set Gift code $storeId
     *
     * @param int $storeId
     * @return GiftvoucherInterface
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get Gift code $conditionsSerialized
     *
     * @return string|null
     */
    public function getConditionsSerialized()
    {
        return $this->getData(self::CONDITIONS_SERIALIZED);
    }

    /**
     * Set Gift code $conditionsSerialized
     *
     * @param string $conditionsSerialized
     * @return GiftvoucherInterface
     */
    public function setConditionsSerialized($conditionsSerialized)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $conditionsSerialized);
    }

    /**
     * Get Gift code $dayToSend
     *
     * @return string|null
     */
    public function getDayToSend()
    {
        return $this->getData(self::DAY_TO_SEND);
    }

    /**
     * Set Gift code $dayToSend
     *
     * @param string $dayToSend
     * @return GiftvoucherInterface
     */
    public function setDayToSend($dayToSend)
    {
        return $this->setData(self::DAY_TO_SEND, $dayToSend);
    }

    /**
     * Get Gift code $isSent
     *
     * @return string|null
     */
    public function getIsSent()
    {
        return $this->getData(self::IS_SENT);
    }

    /**
     * Set Gift code $isSent
     *
     * @param string $isSent
     * @return GiftvoucherInterface
     */
    public function setIsSent($isSent)
    {
        return $this->setData(self::IS_SENT, $isSent);
    }

    /**
     * Get Gift code $shippedToCustomer
     *
     * @return int|null
     */
    public function getShippedToCustomer()
    {
        return $this->getData(self::SHIPPED_TO_CUSTOMER);
    }

    /**
     * Set Gift code $shippedToCustomer
     *
     * @param int $shippedToCustomer
     * @return GiftvoucherInterface
     */
    public function setShippedToCustomer($shippedToCustomer)
    {
        return $this->setData(self::SHIPPED_TO_CUSTOMER, $shippedToCustomer);
    }

    /**
     * Get Gift code $createdForm
     *
     * @return string|null
     */
    public function getCreatedForm()
    {
        return $this->getData(self::CREATED_FORM);
    }

    /**
     * Set Gift code $createdForm
     *
     * @param string $createdForm
     * @return GiftvoucherInterface
     */
    public function setCreatedForm($createdForm)
    {
        return $this->setData(self::CREATED_FORM, $createdForm);
    }

    /**
     * Get Gift code $templateId
     *
     * @return int|null
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }
    /**
     * Set Gift code $templateId
     *
     * @param int $templateId
     * @return GiftvoucherInterface
     */
    public function setTemplateId($templateId)
    {
        return $this->setData(self::TEMPLATE_ID, $templateId);
    }

    /**
     * Get Gift code $description
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set Gift code $description
     *
     * @param string $description
     * @return GiftvoucherInterface
     */
    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get Gift code $giftvoucherComments
     *
     * @return string|null
     */
    public function getGiftvoucherComments()
    {
        return $this->getData(self::GIFTVOUCHER_CONMENTS);
    }

    /**
     * Set Gift code $giftvoucherComments
     *
     * @param string $giftvoucherComments
     * @return GiftvoucherInterface
     */
    public function setGiftvoucherComments($giftvoucherComments)
    {
        return $this->setData(self::GIFTVOUCHER_CONMENTS, $giftvoucherComments);
    }

    /**
     * Get Gift code $emailSender
     *
     * @return int|null
     */
    public function getEmailSender()
    {
        return $this->getData(self::EMAIL_SENDER);
    }

    /**
     * Set Gift code $emailSender
     *
     * @param int $emailSender
     * @return GiftvoucherInterface
     */
    public function setEmailSender($emailSender)
    {
        return $this->setData(self::EMAIL_SENDER, $emailSender);
    }

    /**
     * Get Gift code $notifySuccess
     *
     * @return int|null
     */
    public function getNotifySuccess()
    {
        return $this->getData(self::NOTIFY_SUCCESS);
    }

    /**
     * Set Gift code $notifySuccess
     *
     * @param int $notifySuccess
     * @return GiftvoucherInterface
     */
    public function setNotifySuccess($notifySuccess)
    {
        return $this->setData(self::NOTIFY_SUCCESS, $notifySuccess);
    }
    /**
     * Get Gift code $giftcardCustomImage
     *
     * @return int|null
     */
    public function getGiftcardCustomImage()
    {
        return $this->getData(self::GIFTCARD_CUSTOM_IMAGE);
    }

    /**
     * Set Gift code $giftcardCustomImage
     *
     * @param int $giftcardCustomImage
     * @return GiftvoucherInterface
     */
    public function setGiftcardCustomImage($giftcardCustomImage)
    {
        return $this->setData(self::GIFTCARD_CUSTOM_IMAGE, $giftcardCustomImage);
    }
    /**
     * Get Gift code $giftcardTemplateId
     *
     * @return int|null
     */
    public function getGiftcardTemplateId()
    {
        return $this->getData(self::GIFTCARD_TEMPLATE_ID);
    }
    /**
     * Set Gift code $giftcardTemplateId
     *
     * @param int $giftcardTemplateId
     * @return GiftvoucherInterface
     */
    public function setGiftcardTemplateId($giftcardTemplateId)
    {
        return $this->setData(self::GIFTCARD_TEMPLATE_ID, $giftcardTemplateId);
    }

    /**
     * Get Gift code $giftcardTemplateImage
     *
     * @return string|null
     */
    public function getGiftcardTemplateImage()
    {
        return $this->getData(self::GIFTCARD_TEMPLATE_IMAGE);
    }

    /**
     * Set Gift code $giftcardTemplateImage
     *
     * @param string $giftcardTemplateImage
     * @return GiftvoucherInterface
     */
    public function setGiftcardTemplateImage($giftcardTemplateImage)
    {
        return $this->setData(self::GIFTCARD_TEMPLATE_IMAGE, $giftcardTemplateImage);
    }

    /**
     * Get Gift code $actionsSerialized
     *
     * @return string|null
     */
    public function getActionsSerialized()
    {
        return $this->getData(self::ACTIONS_SERIALIZED);
    }

    /**
     * Set Gift code $actionsSerialized
     *
     * @param string $actionsSerialized
     * @return GiftvoucherInterface
     */
    public function setActionsSerialized($actionsSerialized)
    {
        return $this->setData(self::ACTIONS_SERIALIZED, $actionsSerialized);
    }

    /**
     * Get Gift code $timezoneToSend
     *
     * @return string|null
     */
    public function getTimezoneToSend()
    {
        return $this->getData(self::TIMEZONE_TO_SEND);
    }

    /**
     * Set Gift code $timezoneToSend
     *
     * @param string $timezoneToSend
     * @return GiftvoucherInterface
     */
    public function setTimezoneToSend($timezoneToSend)
    {
        return $this->setData(self::TIMEZONE_TO_SEND, $timezoneToSend);
    }

    /**
     * Get Gift code $dayStore
     *
     * @return string|null
     */
    public function getDayStore()
    {
        return $this->getData(self::DAY_STORE);
    }

    /**
     * Set Gift code $dayStore
     *
     * @param string $dayStore
     * @return GiftvoucherInterface
     */
    public function setDayStore($dayStore)
    {
        return $this->setData(self::DAY_STORE, $dayStore);
    }

    /**
     * Get Gift code $used
     *
     * @return int|null
     */
    public function getUsed()
    {
        return $this->getData(self::USED);
    }

    /**
     * Set Gift code $used
     *
     * @param int $used
     * @return GiftvoucherInterface
     */
    public function setUsed($used)
    {
        return $this->setData(self::USED, $used);
    }

    /**
     * Get Gift code $setId
     *
     * @return int|null
     */
    public function getSetId()
    {
        return $this->getData(self::SET_ID);
    }

    /**
     * Set Gift code $setId
     *
     * @param int $setId
     * @return GiftvoucherInterface
     */
    public function setSetId($setId)
    {
        return $this->setData(self::SET_ID, $setId);
    }
}
