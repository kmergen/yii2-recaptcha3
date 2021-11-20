<h1>Yii2 ReCaptcha 3</h1>

Yii2 ReCaptcha3 provide a validator and a widget for Google ReCaptcha 3.

It needs no jQuery.

The preferred way to install this extension is through [composer](https://getcomposer.org/).

Either run

```bash
composer require "kmergen/yii2-recaptcha3: "*"
```

or add

```
"kmergen/yii2-recaptcha3": "*",
```

to the `require` section of your `composer.json` file.


### 2. Configuration
In your configuration file set the following:
```php
'components' => [
    ...
    'recaptcha3' => [
        'class'      => 'kmergen\recaptcha3\RecaptchaConfig',
        'siteKey'   => 'google_recaptcha_v3_site key',
        'secret' => 'google_recaptcha_v3_secret key',
    ],
    ...
]
```
### 2. Set Validator in Model

```php
public $recaptcha;
 
public function rules()
{
 	return [
 		...
 		 [['recaptcha'], \kmergen\recaptcha3\RecaptchaValidator::class, 'threshold' => 0.5]
 	];
}
```
and then in form view
```php
<?= $form->field($model, 'recaptcha')->widget(
         \kmergen\recaptcha3\RecaptchaWidget::class,
         ['action' => 'homepage', // optional]
    ) ?>
```
or

```php
<?= \kmergen\recaptcha3\RecaptchaWidget::widget([
         'name' => 'FormName[recaptcha]',
         'action' => 'homepage', // optional
     ]) ?>
```
