<?php
/**
 *
 * @description Custom logger
 *
 * @author Bina Commerce      <https://www.binacommerce.com>
 * @author C. M. de Picciotto <cmdepicciotto@binacommerce.com>
 *
 */
namespace Bina\GeoIp\Logger;

use Magento\Framework\Logger\Monolog;
use Bina\GeoIp\Api\LoggerInterface;

class Logger extends Monolog implements LoggerInterface
{}
