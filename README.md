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

> Note: You can react on the response error-code 'duplicate-or-timeout'. Then the returned error message is 'duplicate-or-timeout'
> This error appears if a user refresh a page with an already submitted form, so you can do the following in your controller:

```php
        if ($model->load($post)) {
            if ($model->validate()) {
                if ($model->sendEmail()) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Thank you for contacting us. We will respond to you as soon as possible.'));
                } else {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'There was an error while sending your message.'));
                }
            } else {
                if ($model->hasErrors('recaptcha')) {
                    $errors = $model->getErrors('recaptcha');
                    if (strcmp($errors[0] , 'timeout-or-duplicate') !== 0) {
                        Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'flashmessage.recaptcha3.failed'));
                    }
                }
            }
        }
        return $this->render('contact', ['model' => $model]);
    
```
