<?php

use Pyz\Yves\ShopApplication\YvesBootstrap;
use Spryker\Shared\Config\Application\Environment;
use Spryker\Shared\ErrorHandler\ErrorHandlerEnvironment;
use SebastianBergmann\CodeCoverage\CodeCoverage;

require __DIR__ . '/maintenance/maintenance.php';

define('APPLICATION', 'YVES');
defined('APPLICATION_ROOT_DIR') || define('APPLICATION_ROOT_DIR', realpath(__DIR__ . '/../..'));

require_once APPLICATION_ROOT_DIR . '/vendor/autoload.php';

$coverage = new CodeCoverage();
$dir = __DIR__ . "/../../src/Pyz";
$coverage->filter()->addDirectoryToWhitelist($dir);
$token = uniqid();
$coverage->start($token);

Environment::initialize();

$errorHandlerEnvironment = new ErrorHandlerEnvironment();
$errorHandlerEnvironment->initialize();

$bootstrap = new YvesBootstrap();
$bootstrap
    ->boot()
    ->run();

$coverage->stop();

$s = serialize($coverage);
file_put_contents('/tmp/cov/' . $token, $s);
