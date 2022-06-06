<?php
/**
 *
 * @description Logger interface
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Api;

interface LoggerInterface
{
    /**
     *
     * Add a log record at the INFO level
     *
     * @param string $message The log message
     * @param array  $context The log context
     *
     * @return bool
     *
     */
    public function info($message, array $context = array());
}
