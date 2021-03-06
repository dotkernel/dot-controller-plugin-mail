<?php
/**
 * @see https://github.com/dotkernel/dot-controller-plugin-mail/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller-plugin-mail/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Controller\Plugin\Mail;

use Dot\Controller\Plugin\PluginInterface;
use Dot\Mail\Result\ResultInterface;
use Dot\Mail\Service\MailServiceInterface;

/**
 * Class MailPlugin
 * @package Dot\Controller\Plugin\Mail
 */
class MailPlugin implements PluginInterface
{
    /** @var MailServiceInterface */
    protected $mailService;

    /**
     * The list of possible arguments in the order they should be provided
     * @var array
     */
    private $argumentsMapping = [
        0 => 'body',
        1 => 'subject',
        2 => 'to',
        3 => 'from',
        4 => 'cc',
        5 => 'bcc',
        6 => 'attachments'
    ];

    /**
     * MailPlugin constructor.
     * @param MailServiceInterface $mailService
     */
    public function __construct(MailServiceInterface $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * If no arguments are provided, the mail service is returned.
     * If any argument is provided, they will be used to configure the MailService and send an email.
     * The result object will be returned in that case
     *
     * @param null|string|array $bodyOrConfig
     * @param null|string $subject
     * @param null|array $to
     * @param null|string|array $from
     * @param null|array $cc
     * @param null|array $bcc
     * @param null|array $attachments
     * @return MailServiceInterface|ResultInterface
     */
    public function __invoke(
        $bodyOrConfig = null,
        $subject = null,
        $to = null,
        $from = null,
        $cc = null,
        $bcc = null,
        $attachments = null
    ) {
        $args = func_get_args();
        if (empty($args)) {
            return $this->mailService;
        }
        $args = $this->normalizeMailArgs($args);
        $this->applyArgsToMailService($args);
        return $this->mailService->send();
    }

    /**
     * Normalizes the arguments passed when invoking this plugin so that they can be treated in a consistent way
     *
     * @param array $args
     * @return array
     */
    protected function normalizeMailArgs(array $args): array
    {
        // If the first argument is an array, use it as the mail configuration
        if (is_array($args[0])) {
            return $args[0];
        }
        $result = [];
        $length = count($args);
        // FIXME This is a weak way to handle the arguments, since a change in the order will break it
        for ($i = 0; $i < $length; $i++) {
            $result[$this->argumentsMapping[$i]] = $args[$i];
        }
        return $result;
    }

    /**
     * Applies the arguments provided while invoking this plugin to the MailService,
     * discarding any previous configuration
     *
     * @param array $args
     */
    protected function applyArgsToMailService(array $args)
    {
        if (isset($args['body'])) {
            $body = $args['body'];
            if (is_array($body)) {
                $charset = $body['charset'] ?? MailServiceInterface::DEFAULT_CHARSET;
                if (isset($body['content']) && is_string($body['content'])) {
                    $this->mailService->setBody($body['content'], $charset);
                } elseif (isset($body['template']) && is_array($body['template'])) {
                    $name = $body['template']['name'] ?? '';
                    $params = $body['template']['params'] ?? [];
                    if (!empty($name)) {
                        $this->mailService->setTemplate($name, $params, $charset);
                    }
                }
            } else {
                $this->mailService->setBody($body);
            }
        }

        if (isset($args['subject'])) {
            $this->mailService->setSubject($args['subject']);
        }

        if (isset($args['to'])) {
            $this->mailService->getMessage()->setTo($args['to']);
        }

        if (isset($args['from'])) {
            $from = $args['from'];
            if (is_array($from)) {
                $fromAddress = array_keys($from);
                $fromName = array_values($from);
                $this->mailService->getMessage()->setFrom($fromAddress[0], $fromName[0]);
            } else {
                $this->mailService->getMessage()->setFrom($from);
            }
        }

        if (isset($args['cc'])) {
            $this->mailService->getMessage()->setCc($args['cc']);
        }

        if (isset($args['bcc'])) {
            $this->mailService->getMessage()->setBcc($args['bcc']);
        }

        if (isset($args['attachments'])) {
            $this->mailService->setAttachments($args['attachments']);
        }
    }

    /**
     * @return MailServiceInterface
     */
    public function getMailService(): MailServiceInterface
    {
        return $this->mailService;
    }

    /**
     * @param MailServiceInterface $mailService
     */
    public function setMailService(MailServiceInterface $mailService)
    {
        $this->mailService = $mailService;
    }
}
