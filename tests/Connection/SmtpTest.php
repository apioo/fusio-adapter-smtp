<?php
/*
 * Fusio is an open source API management platform which helps to create innovative API solutions.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Fusio\Adapter\Smtp\Tests\Action;

use Fusio\Adapter\Smtp\Connection\Smtp;
use Fusio\Adapter\Smtp\Tests\SmtpTestCase;
use Fusio\Engine\Form\Builder;
use Fusio\Engine\Form\Container;
use Fusio\Engine\Form\Element\Input;
use Fusio\Engine\Parameters;
use Symfony\Component\Mailer\Mailer;

/**
 * SmtpTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class SmtpTest extends SmtpTestCase
{
    public function testGetConnection()
    {
        $connection = $this->getConnectionFactory()->factory(Smtp::class);

        $config = new Parameters([
            'dsn' => 'native://default',
        ]);

        $smtp = $connection->getConnection($config);

        $this->assertInstanceOf(Mailer::class, $smtp);
    }

    public function testConfigure()
    {
        $connection = $this->getConnectionFactory()->factory(Smtp::class);
        $builder    = new Builder();
        $factory    = $this->getFormElementFactory();

        $connection->configure($builder, $factory);

        $this->assertInstanceOf(Container::class, $builder->getForm());

        $elements = $builder->getForm()->getElements();
        $this->assertEquals(1, count($elements));
        $this->assertInstanceOf(Input::class, $elements[0]);
    }
}
