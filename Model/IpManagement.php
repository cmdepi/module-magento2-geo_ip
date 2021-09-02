<?php
/**
 *
 * @description Geo IP management
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Model;

use Bina\GeoIp\Api\SessionInterface;
use Bina\GeoIp\Api\IpManagementInterface;

class IpManagement implements IpManagementInterface
{
    /**
     *
     * @var SessionInterface
     *
     */
    protected $_session;

    /**
     *
     * Constructor
     *
     * @param SessionInterface $session
     *
     */
    public function __construct(SessionInterface $session)
    {
        /**
         *
         * @note Init session
         *
         */
        $this->_session = $session;
    }

    /**
     *
     * Check related store ID
     *
     * @return int
     *
     */
    public function checkRelatedStoreId()
    {
        /**
         *
         * @note Get store ID
         *
         */
        $storeId = $this->_session->getUserStoreFromLocation();

        /**
         *
         * @note Set flag as checked (in that way, we disable the possibility to the this check again)
         *
         */
        $this->_session->setIsLocationChecked(true);

        /**
         *
         * @note Return store ID
         *
         */
        return $storeId;
    }
}