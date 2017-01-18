<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller-plugin-mail
 * @author: n3vrax
 * Date: 9/6/2016
 * Time: 7:49 PM
 */

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
    public function __invoke()
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
