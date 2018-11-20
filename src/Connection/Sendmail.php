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
 * Sendmail
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Sendmail implements ConnectionInterface
{
    public function getName()
    {
        return 'Sendmail';
    }

    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Swift_Mailer
     */
    public function getConnection(ParametersInterface $config)
    {
        $command = $config->get('command');
        if (!empty($command)) {
            $transport = \Swift_SendmailTransport::newInstance($command);
        } else {
            $transport = \Swift_SendmailTransport::newInstance();
        }

        return \Swift_Mailer::newInstance($transport);
    }

    /**
     * @param \Fusio\Engine\Form\BuilderInterface $builder
     * @param \Fusio\Engine\Form\ElementFactoryInterface $elementFactory
     */
    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
        $builder->add($elementFactory->newInput('command', 'Command', 'text', 'Optional path to the sendmail binary by default it uses <code>/usr/sbin/sendmail</code>'));
    }
}
