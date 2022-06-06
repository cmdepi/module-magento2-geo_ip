<?php
/**
 *
 * @description Handler for custom logger
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Logger\Handler;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Bina\GeoIp\Logger\Logger;

class Base extends BaseHandler
{
    /**
     *
     * @var int
     *
     */
    protected $loggerType = Logger::INFO;

    /**
     *
     * @var string
     *
     */
    protected $fileName = '/var/log/geo.log';
}
