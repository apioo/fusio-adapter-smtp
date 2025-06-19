<?php
/*
 * Fusio - Self-Hosted API Management for Builders.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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
use Fusio\Engine\Request\HttpRequestContext;
use Fusio\Engine\RequestInterface;
use PSX\Http\Environment\HttpResponseInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

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

        $body = $configuration->get('body');
        if (empty($body)) {
            $subject = $request->get('subject');
            $to = $request->get('to');
            $cc = $request->get('cc');
            $bcc = $request->get('bcc');
            $from = $request->get('from');
            $body = $request->get('body');
        } else {
            $subject = $configuration->get('subject');
            $to = $configuration->get('to');
            $cc = $configuration->get('cc');
            $bcc = $configuration->get('bcc');
            $from = $configuration->get('from');
            $body = $this->buildBody($request, $body);

            if (empty($to)) {
                $context->getUser()->getEmail();
            }
        }

        if (empty($subject)) {
            throw new ConfigurationException('No subject configured');
        }

        if (empty($to)) {
            throw new ConfigurationException('No to configured');
        }

        if (empty($body)) {
            throw new ConfigurationException('No body configured');
        }

        $message = (new Email())
            ->subject($subject)
            ->to($to)
            ->html($body);

        if (!empty($from)) {
            $message->from($from);
        }

        if (!empty($cc)) {
            $message->cc($cc);
        }

        if (!empty($bcc)) {
            $message->bcc($bcc);
        }

        $connection->send($message);

        return $this->response->build(200, [], [
            'success' => true,
            'message' => 'Mail successfully send',
        ]);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The SMTP connection which should be used'));
        $builder->add($elementFactory->newInput('to', 'To', 'text', 'The receiver of this mail, if empty we try to send the email to the authenticated user'));
        $builder->add($elementFactory->newInput('cc', 'CC', 'text', 'Optional CC receiver'));
        $builder->add($elementFactory->newInput('bcc', 'BCC', 'text', 'Optional BCC receiver'));
        $builder->add($elementFactory->newInput('from', 'From', 'text', 'Optional the from address'));
        $builder->add($elementFactory->newInput('subject', 'Subject', 'text', 'The subject of this mail'));
        $builder->add($elementFactory->newTextArea('body', 'Body', 'html', 'The HTML body of this mail'));
    }

    protected function getConnection(ParametersInterface $configuration): Mailer
    {
        $connection = $this->connector->getConnection($configuration->get('connection'));
        if (!$connection instanceof Mailer) {
            throw new ConfigurationException('Given connection must be a Mailer connection');
        }

        return $connection;
    }

    private function buildBody(RequestInterface $request, string $body): string
    {
        $templateContext = [
            'payload' => $request->getPayload(),
            'arguments' => $request->getArguments(),
        ];

        $requestContext = $request->getContext();
        if ($requestContext instanceof HttpRequestContext) {
            $templateContext['uriFragments'] = $requestContext->getParameters();
            $templateContext['query'] = $requestContext->getRequest()->getUri()->getParameters();
        }

        $loader = new ArrayLoader(['body' => $body]);
        $twig = new Environment($loader, []);

        return $twig->render('body', $templateContext);
    }
}
