<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Smtp\Connection;

use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

/**
 * Smtp
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class Smtp implements ConnectionInterface
{
    public function getName(): string
    {
        return 'SMTP';
    }

    public function getConnection(ParametersInterface $config): Mailer
    {
        return new Mailer(Transport::fromDsn($config->get('dsn')));
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newInput('dsn', 'DSN', 'text', 'The Mailer DSN. CLick <a href="https://symfony.com/doc/current/mailer.html#using-built-in-transports">here</a> for more information.'));
    }
}
