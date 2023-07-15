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

namespace Fusio\Adapter\Smtp\Action;

use Fusio\Engine\ActionAbstract;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use PSX\Http\Environment\HttpResponseInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;

/**
 * SmtpSend
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class SmtpSend extends ActionAbstract
{
    public function getName(): string
    {
        return 'SMTP-Send';
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): HttpResponseInterface
    {
        $connection = $this->getConnection($configuration);

        $message = (new Email())
            ->subject($request->get('subject'))
            ->to($request->get('to'))
            ->html($request->get('body'));

        $from = $request->get('from');
        if (!empty($from)) {
            $message->from($from);
        }

        $cc = $request->get('cc');
        if (!empty($cc)) {
            $message->cc($cc);
        }

        $bcc = $request->get('bcc');
        if (!empty($bcc)) {
            $message->bcc($bcc);
        }

        $connection->send($message);

        return $this->response->build(200, [], [
            'success' => true,
            'message' => 'Mail successful send',
        ]);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The SMTP connection which should be used'));
    }

    protected function getConnection(ParametersInterface $configuration): Mailer
    {
        $connection = $this->connector->getConnection($configuration->get('connection'));
        if (!$connection instanceof Mailer) {
            throw new ConfigurationException('Given connection must be a Mailer connection');
        }

        return $connection;
    }
}
