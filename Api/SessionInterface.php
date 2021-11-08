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
     * @const STORE_ID
     *
     */
    const STORE_ID = 'store_id';

    /**
     *
     * Get user store from IP
     *
     * @return int
     *
     */
    public function getUserStoreFromIp();

    /**
     *
     * Get store ID
     *
     * @return int
     *
     */
    public function getStoreId();

    /**
     *
     * Set store ID
     *
     * @param int $storeId
     *
     * @return void
     *
     */
    public function setStoreId($storeId);
}
