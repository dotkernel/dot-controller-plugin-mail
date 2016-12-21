# dot-controller-plugin-mail

Mail controller plugin for easy access to dot-mail service from any controller.

## Installation

Run the  following command in your project's root directory
```bash
$ composer require dotkernel/dot-controller-plugin-mail
```

This will also install packages `dotkernel/dot-controller` and `'dotkernel/dot-mail` as dependencies.
Next, make sure you merge the `ConfigProvider` to your application's configuration.

## Usage

Because multiple mail services can be defined in the dot-mail module, there can also be requested as controller plugins by following the convention

```php
$this->sendMail(); //will return the default mail service

$this->sendMailYourMailService(); //will return the mail service named your_mail_service
```

Controller mail plugins are invokable. To send a mail you can use the following 2 methods
 
 * `$this->sendMail(array $mailConfig)` - will send a mail through a mail service configured based on the $mailConfig param.
 The array parameter valid keys are body, subject, to, from, cc, bcc, attachments. The body parameter can be specified as an array too in which case the $body[0] should be the template name to use as mail template and $body[1] should be optionally the template parameters.
  
 * `$this->sendMail($body, $subject, $to, $from, $cc, $bcc, $attachments)` - the parameters have the same meaning as in the previous method
