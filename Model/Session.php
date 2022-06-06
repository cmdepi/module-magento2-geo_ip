<?php
/**
 *
 * @description Geo IP session
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 * @note This session model is intended to work like the checkout session model and its get quote feature
 *
 */
namespace Bina\GeoIp\Model;

use Exception;
use Magento\Framework\DataObject;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\State;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\ValidatorInterface;
use Magento\Framework\Session\StorageInterface;
use Magento\Framework\Session\SessionStartChecker;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Bina\GeoIp\Api\LoggerInterface;
use Bina\GeoIp\Api\SystemConfigInterface;
use Bina\GeoIp\Api\SessionInterface;
use Bina\GeoIp\Api\IpInfoInterfaceFactory;
use Bina\GeoIp\Api\IpInfoInterface;

class Session extends SessionManager implements SessionInterface
{
    /**
     *
     * @var IpInfoInterfaceFactory
     *
     */
    protected $_ipInfoFactory;

    /**
     *
     * @var LoggerInterface
     *
     */
    protected $_logger;

    /**
     *
     * @var CollectionFactory
     *
     */
    protected $_collectionFactory;

    /**
     *
     * @var RemoteAddress
     *
     */
    protected $_remoteAddress;

    /**
     *
     * @var IpInfoInterface|null
     *
     */
    private $_ipInfo = null;

    /**
     *
     * Constructor
     *
     * @param IpInfoInterfaceFactory   $ipInfoFactory
     * @param LoggerInterface          $logger
     * @param CollectionFactory        $collectionFactory
     * @param RemoteAddress            $remoteAddress
     * @param Http                     $request
     * @param SidResolverInterface     $sidResolver
     * @param ConfigInterface          $sessionConfig
     * @param SaveHandlerInterface     $saveHandler
     * @param ValidatorInterface       $validator
     * @param StorageInterface         $storage
     * @param CookieManagerInterface   $cookieManager
     * @param CookieMetadataFactory    $cookieMetadataFactory
     * @param State                    $appState
     * @param SessionStartChecker|null $sessionStartChecker
     *
     */
    public function __construct(
        IpInfoInterfaceFactory $ipInfoFactory,
        LoggerInterface        $logger,
        CollectionFactory      $collectionFactory,
        RemoteAddress          $remoteAddress,
        Http                   $request,
        SidResolverInterface   $sidResolver,
        ConfigInterface        $sessionConfig,
        SaveHandlerInterface   $saveHandler,
        ValidatorInterface     $validator,
        StorageInterface       $storage,
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory  $cookieMetadataFactory,
        State                  $appState,
        SessionStartChecker    $sessionStartChecker = null
    ) {
        /**
         *
         * @note Init IP info factory
         *
         */
        $this->_ipInfoFactory = $ipInfoFactory;

        /**
         *
         * @note Init logger
         *
         */
        $this->_logger = $logger;

        /**
         *
         * @note Init collection factory
         *
         */
        $this->_collectionFactory = $collectionFactory;

        /**
         *
         * @note Init remote address
         *
         */
        $this->_remoteAddress = $remoteAddress;

        /**
         *
         * @note Call parent constructor
         *
         */
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $sessionStartChecker
        );
    }

    /**
     *
     * Get user store ID from IP
     *
     * @return int
     *
     * @note It returns 0 as store ID when it is not possible to determine a store for user IP
     *
     */
    public function getUserStoreIdFromIp()
    {
        /**
         *
         * @note Check if store ID was already determined
         *
         */
        if (!is_null($this->getStoreId())) {
            /**
             *
             * @note Return store ID
             *
             */
            return $this->getStoreId();
        }

        /**
         *
         * @note Init store ID as no store
         *
         */
        $storeId = 0;

        /**
         *
         * @note Try to get IP details
         *
         */
        try {
            /**
             *
             * @note Get user IP details
             *
             */
            $ipDetails = $this->_getIpDetails();

            /**
             *
             * @note Check if country exists for user IP
             *
             */
            if (isset($ipDetails->country)) {
                /**
                 *
                 * @note Get country related to IP
                 *
                 */
                $country = $ipDetails->country;

                /**
                 *
                 * @note Get config item
                 *
                 */
                $item = $this->_getConfigItemRelatedToCountry($country);

                /**
                 *
                 * @note Check item
                 *
                 */
                if ($item) {
                    /**
                     *
                     * @note Get store ID
                     * @note All config values are at store level, so the scope ID is related to a store ID
                     *
                     */
                    $storeId = $item->getData('scope_id');
                }
            }
        }
        catch (Exception $e) {
            /**
             *
             * @note Log exception
             *
             */
            $this->_logger->info('[' . $this->_getUserIp() . ']' . ' ' . $e->getMessage());
        }

        /**
         *
         * @note Set store ID in session to avoid determine it again
         *
         */
        $this->setStoreId($storeId);

        /**
         *
         * @note Return store ID
         *
         */
        return $storeId;
    }

    /**
     *
     * Get store ID
     *
     * @return int|null
     *
     */
    public function getStoreId()
    {
        return $this->storage->getData(self::STORE_ID);
    }

    /**
     *
     * Set store ID
     *
     * @param int $storeId
     *
     * @return void
     *
     */
    public function setStoreId($storeId)
    {
        $this->storage->setData(self::STORE_ID, $storeId);
    }

    /**
     *
     * Get IP details
     *
     * @return mixed
     *
     */
    protected function _getIpDetails()
    {
        return $this->_getIpInfo()->getDetails($this->_getUserIp());
    }

    /**
     *
     * Get IP info
     *
     * @return IpInfoInterface
     *
     */
    protected function _getIpInfo()
    {
        /**
         *
         * @note Check IP info
         *
         */
        if (is_null($this->_ipInfo)) {
            /**
             *
             * @note Create IP info
             *
             */
            $this->_ipInfo = $this->_ipInfoFactory->create();
        }

        /**
         *
         * @note Return IP info
         *
         */
        return $this->_ipInfo;
    }

    /**
     *
     * Get config item related to country
     *
     * @param string $countryCode
     *
     * @return DataObject
     *
     */
    private function _getConfigItemRelatedToCountry($countryCode)
    {
        /**
         *
         * @note Create collection
         *
         */
        $collection = $this->_collectionFactory->create();

        /**
         *
         * @note Get config data related to this default country
         * @note Because the country code values are saved using the ISO 2 format, it is possible to filter using this like condition (all country codes have 2 characters and are saved using a ',' as separator, so it is not possible to have more than one coincidence)
         *
         */
        $collection->addFieldToFilter('path', array('eq' => SystemConfigInterface::GEO_IP_COUNTRIES));
        $collection->addFieldToFilter('value', array('like' => '%' . $countryCode . '%'));

        /**
         *
         * @note Get first item
         * @note We are going to assume that there is only one store related to this country
         *
         */
        return $collection->getFirstItem();
    }

    /**
     *
     * Get user IP
     *
     * @return string
     *
     */
    private function _getUserIp()
    {
        return $this->_remoteAddress->getRemoteAddress();
    }
}
