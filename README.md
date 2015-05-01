# Contact Form Plugin for PhileCMS #

Add a simple contact-form onto a page which sends the form data to an email address.

[Project Home](https://github.com/Schlaefer/phileContactForm)

### 1.1 Installation (composer) ###

```json
"require": {
	"siezi/phile-contact-form": "*"
}
```

### 1.2 Installation (Download)

* download this plugin into `plugins/siezi/phileContactForm`

### 2. Activation

After you have installed the plugin you need to activate it. Add the following line to your `/config.php` file:

```php
$config['plugins']['siezi\\phileContactForm'] = ['active' => true];
```

### 3. Start ###

Set the email-address the contact form is send to:

```php
$config['plugins']['siezi\\phileContactForm']['recipient-email'] = 'contact@example.com';
```

Put

```
(contact-form: simple)
```

onto the page where the contact form should be inserted.

### 4. Config ###

See `config.php`.


#### Events ####

##### plugin.siezi.contactForm.send #####

Triggered when the received form data is processed.

By altering the returned `success` parameter allows you to utilize the form data and perform your own action and/or suppress the default (send email).
