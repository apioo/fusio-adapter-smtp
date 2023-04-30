<?php

use Fusio\Adapter\Smtp\Action\SmtpSend;
use Fusio\Adapter\Smtp\Connection\Smtp;
use Fusio\Engine\Adapter\ServiceBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = ServiceBuilder::build($container);
    $services->set(Smtp::class);
    $services->set(SmtpSend::class);
};
