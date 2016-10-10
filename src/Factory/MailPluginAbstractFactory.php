<?php
/**
 * @copyright: DotKernel
 * @library: dotkernel/dot-controller-plugin-mail
 * @author: n3vrax
 * Date: 9/6/2016
 * Time: 7:49 PM
 */

namespace Dot\Controller\Plugin\Mail\Factory;


use Dot\Controller\Plugin\Mail\MailPlugin;
use Dot\Mail\Factory\AbstractMailFactory;
use Dot\Mail\Factory\MailServiceAbstractFactory;
use Dot\Mail\Service\MailServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\Stdlib\StringUtils;

/**
 * Class MailPluginAbstractFactory
 * @package Dot\Controller\Plugin\Mail\Factory
 */
class MailPluginAbstractFactory extends AbstractMailFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        if (strpos($requestedName, 'sendMail') !== 0) {
            return false;
        }

        if ($requestedName === 'sendMail') {
            return true;
        }

        $specificServiceName = $this->getSpecificServiceName($requestedName);
        return array_key_exists($specificServiceName, $this->getConfig($container));
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return MailPlugin
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $specificServiceName = $this->getSpecificServiceName($requestedName);

        /** @var MailServiceInterface $mailService */
        $mailService = $container->get(sprintf(
            '%s.%s.%s', self::DOT_MAIL_PART, MailServiceAbstractFactory::SPECIFIC_PART, $specificServiceName
        ));

        return new MailPlugin($mailService);
    }

    /**
     * @param $requestedName
     * @return string
     */
    protected function getSpecificServiceName($requestedName)
    {
        $parts = explode('_', $this->camelCaseToUnderscore($requestedName));

        if (count($parts) === 2) {
            return 'default';
        }

        //discard the sendMail part
        $parts = array_slice($parts, 2);
        $specificServiceName = '';
        foreach ($parts as $part) {
            $specificServiceName .= $part;
        }

        //convert from camecase to underscores and set to lower
        return strtolower($specificServiceName);
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function camelCaseToUnderscore($value)
    {
        if (!is_scalar($value) && !is_array($value)) {
            return $value;
        }

        if (StringUtils::hasPcreUnicodeSupport()) {
            $pattern = ['#(?<=(?:\p{Lu}))(\p{Lu}\p{Ll})#', '#(?<=(?:\p{Ll}|\p{Nd}))(\p{Lu})#'];
            $replacement = ['_\1', '_\1'];
        } else {
            $pattern = ['#(?<=(?:[A-Z]))([A-Z]+)([A-Z][a-z])#', '#(?<=(?:[a-z0-9]))([A-Z])#'];
            $replacement = ['\1_\2', '_\1'];
        }

        return preg_replace($pattern, $replacement, $value);
    }
}