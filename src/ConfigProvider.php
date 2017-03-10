<?php
/**
 * @see https://github.com/dotkernel/dot-controller-plugin-mail/ for the canonical source repository
 * @copyright Copyright (c) 2017 Apidemia (https://www.apidemia.com)
 * @license https://github.com/dotkernel/dot-controller-plugin-mail/blob/master/LICENSE.md MIT License
 */

declare(strict_types = 1);

namespace Dot\Controller\Plugin\Mail;

use Dot\Controller\Plugin\Mail\Factory\MailPluginAbstractFactory;

/**
 * Class ConfigProvider
 * @package Dot\Controller\Plugin\Mail
 */
class ConfigProvider
{
    /**
     * @return array
     */
    public function __invoke(): array
    {
        return [
            'dot_controller' => [
                'plugin_manager' => [
                    'abstract_factories' => [
                        MailPluginAbstractFactory::class,
                    ],
                ],
            ],
        ];
    }
}
