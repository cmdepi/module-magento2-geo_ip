<?php
/**
 *
 * @description Geo IP info interface
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Api;

interface IpInfoInterface
{
    /**
     *
     * Get IP details
     *
     * @param string|null $ip
     *
     * @return mixed
     *
     */
    public function getDetails($ip = null);
}