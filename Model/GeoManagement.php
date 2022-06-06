<?php
/**
 *
 * @description Geo management model
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Model;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http;
use Bina\GeoIp\Api\GeoMetadataInterface;
use Bina\GeoIp\Api\GeoManagementInterface;
use Bina\GeoIp\Api\SystemConfigInterface;
use Bina\GeoIp\Api\SessionInterface;

class GeoManagement implements GeoManagementInterface
{
    /**
     *
     * @var SessionInterface
     *
     */
    protected $_session;

    /**
     *
     * @var SystemConfigInterface
     *
     */
    protected $_config;

    /**
     *
     * Constructor
     *
     * @param SessionInterface      $session
     * @param SystemConfigInterface $config
     *
     */
    public function __construct(SessionInterface $session, SystemConfigInterface $config)
    {
        /**
         *
         * @note Init geo IP session model
         *
         */
        $this->_session = $session;

        /**
         *
         * @note Init system config model
         *
         */
        $this->_config = $config;
    }

    /**
     *
     * Process request and get store related to its geo information
     *
     * @param RequestInterface|Http $request
     *
     * @return int|null
     *
     */
    public function processRequest($request)
    {
        /**
         *
         * @note Check if it is set the geo country header data
         *
         */
        if ($country = $request->getHeader(GeoMetadataInterface::GEO_COUNTRY_HEADER)) {
            /**
             *
             * @note Get store ID by geo country header data
             *
             */
            $storeId = $this->_config->getStoreIdByCountryCode($country);

            /**
             *
             * @note Set store ID in session to avoid determine it again
             *
             */
            $this->_session->setStoreId($storeId);
        }

        /**
         *
         * @note If it is not set the geo country header data, then apply the determination/validation logic
         *
         */
        return $this->_session->getUserStoreIdFromIp();
    }
}
