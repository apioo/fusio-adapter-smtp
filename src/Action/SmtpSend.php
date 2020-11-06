<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2020 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Smtp\Action;

use Fusio\Engine\ActionAbstract;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

/**
 * Action which allows you to create an API endpoint based on any database
 * table
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class SmtpSend extends ActionAbstract
{
    public function getName()
    {
        return 'SMTP-Send';
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context)
    {
        $connection = $this->getConnection($configuration);

        $message = (new \Swift_Message($request->get('subject')));
        $message->setTo($request->get('to'));
        $message->setBody($request->get('body'));

        $from = $request->get('from');
        if (!empty($from)) {
            $message->setFrom($from);
        }

        $cc = $request->get('cc');
        if (!empty($cc)) {
            $message->setCc($cc);
        }

        $bcc = $request->get('bcc');
        if (!empty($bcc)) {
            $message->setBcc($bcc);
        }

        $connection->send($message);

        return $this->response->build(200, [], [
            'success' => true,
            'message' => 'Mail successful send',
        ]);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The SMTP connection which should be used'));
    }

    protected function getConnection(ParametersInterface $configuration): \Swift_Mailer
    {
        $connection = $this->connector->getConnection($configuration->get('connection'));
        if (!$connection instanceof \Swift_Mailer) {
            throw new ConfigurationException('Given connection must be a Swift_Mailer connection');
        }

        return $connection;
    }
}
