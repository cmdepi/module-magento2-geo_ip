<?php
/**
 *
 * @description Geo IP popup block
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Bina\GeoIp\Api\SessionInterface;

class Popup extends Template
{
    /**
     *
     * @var SessionInterface
     *
     */
    protected $_ipSession;

    /**
     *
     * @var string
     *
     */
    protected $_template = 'Bina_GeoIp::popup.phtml';

    /**
     *
     * Constructor
     *
     * @param SessionInterface $ipSession
     * @param Context          $context
     * @param array            $data
     *
     */
    public function __construct(SessionInterface $ipSession, Context $context, array $data = [])
    {
        /**
         *
         * @note Init IP session
         *
         */
        $this->_ipSession = $ipSession;

        /**
         *
         * @note Call parent constructor
         *
         */
        parent::__construct($context, $data);
    }

    /**
     *
     * Check if feature is active
     *
     * @return bool
     *
     */
    public function isActive()
    {
        return !($this->_ipSession->getIsLocationChecked());
    }

    /**
     *
     * Get check IP URL
     *
     * @return string
     *
     */
    public function getCheckIpUrl()
    {
        return $this->_urlBuilder->getUrl('geoip/ip/check');
    }

    /**
     *
     * Get current URL
     *
     * @return string
     *
     */
    public function getCurrentUrl()
    {
        /**
         *
         * @note Get store
         *
         */
        /** @var StoreManagerInterface|Store $store */
        $store = $this->_storeManager->getStore();

        /**
         *
         * @note Return current URL
         *
         */
        return $store->getCurrentUrl(false);
    }
}