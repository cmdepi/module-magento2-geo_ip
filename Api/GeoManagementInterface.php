<?php
/**
 *
 * @description Geo management interface
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Api;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\Http;

interface GeoManagementInterface
{
    /**
     *
     * Process request and get store related to its geo information
     *
     * @param RequestInterface|Http $request
     *
     * @return int|null
     *
     */
    public function processRequest($request);
}
