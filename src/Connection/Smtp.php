<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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

/**
 * Smtp
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Smtp implements ConnectionInterface
{
    public function getName()
    {
        return 'SMTP';
    }

    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Swift_Mailer
     */
    public function getConnection(ParametersInterface $config)
    {
        $host = $config->get('host');
        if (empty($host)) {
            $host = 'localhost';
        }

        $port = (int) $config->get('port');
        if (empty($port)) {
            $port = 25;
        }

        $transport = \Swift_SmtpTransport::newInstance($host, $port);

        $encryption = $config->get('encryption');
        if (in_array($encryption, ['tls', 'ssl'])) {
            $transport->setEncryption($encryption);
        }

        $username = $config->get('username');
        if (!empty($username)) {
            $transport->setUsername($username);
        }

        $password = $config->get('password');
        if (!empty($password)) {
            $transport->setPassword($password);
        }

        return \Swift_Mailer::newInstance($transport);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
        $builder->add($elementFactory->newInput('host', 'Host', 'text', 'SMTP host'));
        $builder->add($elementFactory->newInput('port', 'Port', 'number', 'SMTP port'));
        $builder->add($elementFactory->newInput('username', 'Username', 'text', 'Optional SMTP username'));
        $builder->add($elementFactory->newInput('password', 'Password', 'text', 'Optional SMTP password'));
        $builder->add($elementFactory->newSelect('encryption', 'Encryption', ['none' => 'None', 'tls' => 'TLS', 'ssl' => 'SSL'], ''));
    }
}
