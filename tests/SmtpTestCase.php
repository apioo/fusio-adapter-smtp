<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Smtp\Tests;

use Fusio\Adapter\OpenStack\Connection\BlockStorage;
use Fusio\Adapter\OpenStack\Connection\Compute;
use Fusio\Adapter\OpenStack\Connection\Identity;
use Fusio\Adapter\OpenStack\Connection\Images;
use Fusio\Adapter\OpenStack\Connection\Networking;
use Fusio\Adapter\OpenStack\Connection\ObjectStore;
use Fusio\Adapter\Php\Action\PhpProcessor;
use Fusio\Adapter\Php\Action\PhpSandbox;
use Fusio\Adapter\Redis\Action\RedisHashDelete;
use Fusio\Adapter\Redis\Action\RedisHashGet;
use Fusio\Adapter\Redis\Action\RedisHashGetAll;
use Fusio\Adapter\Redis\Action\RedisHashSet;
use Fusio\Adapter\Redis\Connection\Redis;
use Fusio\Adapter\Redis\Generator\RedisHash;
use Fusio\Adapter\Smtp\Action\SmtpSend;
use Fusio\Adapter\Smtp\Connection\Smtp;
use Fusio\Engine\Action\Runtime;
use Fusio\Engine\Test\EngineTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * SmtpTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
abstract class SmtpTestCase extends TestCase
{
    use EngineTestCaseTrait;

    protected function configure(Runtime $runtime, Container $container): void
    {
        $container->set(Smtp::class, new Smtp());
        $container->set(SmtpSend::class, new SmtpSend($runtime));
    }
}