<?php

namespace Phile\Plugin\Siezi\PhileContactForm;

use Phile\Plugin\AbstractPlugin;

/**
 * Class Plugin
 *
 * Simple Contact Form
 *
 * @author PhileCMS
 * @link https://philecms.com
 * @license http://opensource.org/licenses/MIT
 * @package Phile\Plugin\Phile\ContactForm
 */
class Plugin extends AbstractPlugin
{

    protected $events = [
        'template_engine_registered' => 'onBeforeRender'
    ];

    protected $settings = [
        'css' => null,
        'messages' => [
            'config-error-recipient-email' => '"recipient-email" config invalid.',
            'error-send' => 'Message sending failed.',
            'invalid-name' => 'Name is not valid.',
            'invalid-message' => 'Message is not valid.',
            'invalid-email' => 'Address is not valid.',
            'success-send' => 'Thanks for contacting. Your message was send.'
        ],
        'regex' => '/(<p>)?\(contact-form:\s+(?P<type>\S*?)\)(<\/p>)?/',
        'server-sender' => null,
        'template-path' => null
    ];

    protected function onBeforeRender($eventData)
    {
        // check if contact form on page
        $content = $eventData['data']['content'];
        if (!preg_match($this->settings['regex'], $content, $matches)) {
            return;
        }

        // defaults
        $form = $formReset = ['name' => '', 'email' => '', 'message' => ''];
        $labels = $this->settings['labels'];
        $infoMsg = $errorMsg = $isValid = $isPost = $configError = false;
        $css = $this->settings['css'] ?: '';

        // check config
        if (!filter_var($this->settings['recipient-email'], FILTER_VALIDATE_EMAIL)) {
            $configError = $this->settings['messages']['config-error-recipient-email'];
        }
        if ($configError) {
            $errorMsg = 'Contact form plugin config error: ' . $configError;
        }

        // process email form
        $isPost = !$configError && filter_input(INPUT_POST, 'send', FILTER_VALIDATE_BOOLEAN);
        if ($isPost) {
            // validate input
            $isValid = true;
            $form['message'] = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);
            $form['name'] = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $form['email'] = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            foreach (['message', 'name', 'email'] as $key) {
                if (empty($form[$key])) {
                    $errorMsg = $this->settings['messages']['invalid-' . $key];
                    $isValid = false;
                    break;
                }
            }

            // send email
            if ($isValid) {
                $success = [$this, 'email'];
                $data = [
                    'sender-email' => $form['email'],
                    'sender-name' => $form['name'],
                    'message' => $form['message'],
                    'success' => &$success,
                ];
                \Phile\Core\Event::triggerEvent('plugin.phile.contactForm.send', $data);
                if (is_callable($data['success'])) {
                    $success = call_user_func($data['success'], $data);
                } elseif (is_bool($data['success'])) {
                    $success = $data['success'];
                } else {
                    $success = false;
                }
                if ($success) {
                    $infoMsg = $this->settings['messages']['success-send'];
                    $form = $formReset;
                } else {
                    $errorMsg = $this->settings['messages']['error-send'];
                }
            }
        }

        // output contact form
        $params = compact('css', 'errorMsg', 'infoMsg', 'form', 'labels') ;
        $html = $this->render($eventData['engine'], $params);
        $html = preg_replace($this->settings['regex'], $html, $content);
        $eventData['data']['content'] = $html;
    }

    protected function render($engine, $params)
    {
        $eng = clone $engine;
        if (empty($this->settings['template-path'])) {
            $tplPath = $this->getPluginPath('templates/contact.twig');
        } else {
            $tplPath = $this->settings['template-path'];
        }
        $loader = new \Twig_Loader_Filesystem(dirname($tplPath));
        $eng->setLoader($loader);
        return $eng->render(basename($tplPath), $params);
    }

    protected function email($params)
    {
        $params += [
            'server-sender' => $this->settings['server-sender'],
            'recipient-email' => $this->settings['recipient-email'],
            'subject' => $this->settings['subject'],
        ];
        $headers = '';
        if (!empty($params['server-sender'])) {
            $headers .= 'From: ' . $params['server-sender']['name'] . '<' . $params['server-sender']['email'] . ">\r\n";
        }
        $headers .= 'Reply-To: ' . $params['sender-name'] . '<' . $params['sender-email'] . '>' . "\r\n";
        return mail($params['recipient-email'], $params['subject'], $params['message'], $headers);
    }

}
