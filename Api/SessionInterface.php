<?php
/**
 *
 * @description Geo IP session interface
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Api;

interface SessionInterface
{
    /**
     *
     * @const IS_LOCATION_CHECKED
     *
     */
    const IS_LOCATION_CHECKED = 'is_location_checked';

    /**
     *
     * Get user store from location
     *
     * @return int
     *
     */
    public function getUserStoreFromLocation();

    /**
     *
     * Get is location checked flag
     *
     * @return bool
     *
     */
    public function getIsLocationChecked();

    /**
     *
     * Set is location checked flag
     *
     * @param bool $isLocationChecked
     *
     * @return void
     *
     */
    public function setIsLocationChecked($isLocationChecked);
}