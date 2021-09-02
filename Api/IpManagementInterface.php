<?php
/**
 *
 * @description Geo IP management interface
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Api;

interface IpManagementInterface
{
    /**
     *
     * Check related store ID
     *
     * @return int
     *
     */
    public function checkRelatedStoreId();
}