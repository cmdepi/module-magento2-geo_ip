<?php
/**
 *
 * @description System config interface
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Api;

interface SystemConfigInterface
{
    /**
     *
     * @const GEO_IP_COUNTRIES
     *
     */
    const GEO_IP_COUNTRIES = 'general/country/geo_ip';

    /**
     *
     * Get store ID by country code (in IS0 2 format)
     *
     * @param string $countryCode
     *
     * @return int|null
     *
     */
    public function getStoreIdByCountryCode($countryCode);
}
