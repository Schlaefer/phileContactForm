<?php

$config = [
    /**
     * where to send emails
     *
     * must be set
     */
    'recipient-email' => '',
    /**
     * message subject
     */
    'subject' => 'New message from the contact form',
    /**
     * server sender address and name
     */
    /*
    'server-sender' => [
        'name' => 'awesome bob',
        'email' => 'bob@my-domain.com'
    ],
     */
    /**
     * style
     */
    'css' => '
        .contactForm-input, .contactForm-message, contactForm-label {
            display: block;
            width: 100%;
            max-width: 30em;
            box-sizing: border-box;
            margin-bottom: 1em;
        }

        .contactForm-message {
            color: white;
            padding: 1em;
            font-weight: bold;
        }

        .contactForm-message-error {
            background: red;
        }

        .contactForm-message-info {
            background: green;
        }
    ',
    /**
     * labels
     */
    'labels' => [
        'name' => 'Your name:',
        'email' => 'Your email:',
        'message' => 'Your message:',
        'submit' => 'Send'
    ],
];

return $config;
