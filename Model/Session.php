<?php
/**
 *
 * @description Geo IP session
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Model;

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
use Magento\Config\Model\ResourceModel\Config\Data\Collection;
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
     * Get user store from location
     *
     * @return int
     *
     */
    public function getUserStoreFromLocation()
    {
        /**
         *
         * @note Validate if the user location was already checked
         *
         */
        if (!$this->getIsLocationChecked()) {
            /**
             *
             * @note Get IP details
             *
             */
            $ipDetails = $this->_getIpDetails();

            /**
             *
             * @note Check country
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
                     * @note Return store ID
                     * @note All config values are at store level, so the scope ID is related to a store ID
                     *
                     */
                    return $item->getData('scope_id');
                }
            }
        }

        /**
         *
         * @note Return no store
         *
         */
        return 0;
    }

    /**
     *
     * Get is location checked flag
     *
     * @return bool
     *
     */
    public function getIsLocationChecked()
    {
        return $this->storage->getData(self::IS_LOCATION_CHECKED);
    }

    /**
     *
     * Set is location checked flag
     *
     * @param bool $isLocationChecked
     *
     * @return void
     *
     */
    public function setIsLocationChecked($isLocationChecked)
    {
        $this->storage->setData(self::IS_LOCATION_CHECKED, $isLocationChecked);
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
        /** @var Collection $collection */
        $collection = $this->_collectionFactory->create();

        /**
         *
         * @note Get config data related to this default country
         * @note Because the country code values are saved using the ISO 2 format, it is possible to filter using this like condition (all country codes have 3 characters so it is not possible to have more than one coincidence)
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