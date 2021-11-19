<?php
/**
 * KM Websolutions Projects
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2010 KM Websolutions
 * @license http://www.yiiframework.com/license/
 */

namespace kmergen\recaptcha3;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\widgets\InputWidget;

/**
 * Yii2 Google reCAPTCHA v3 widget.
 *
 * For example:
 *
 *```php
 * <?= $form->field($model, 'reCaptcha')->widget(
 *  ReCaptcha3::className(),
 *  [
 *   'siteKey' => 'your siteKey', // unnecessary is reCaptcha component was set up
 *   'threshold' => 0.5,
 *   'action' => 'homepage',
 *  ]
 * ) ?>
 *```
 *
 * or
 *
 *```php
 * <?= ReCaptcha3::widget([
 *  'name' => 'reCaptcha',
 *  'siteKey' => 'your siteKey', // unnecessary is reCaptcha component was set up
 *  'threshold' => 0.5,
 *  'action' => 'homepage',
 *  'widgetOptions' => ['class' => 'col-sm-offset-3'],
 * ]) ?>
 *```
 *
 * @see https://developers.google.com/recaptcha/docs/v3
 * @author HimikLab
 * @package himiklab\yii2\recaptcha
 */
class ReCaptchaWidget extends InputWidget
{
    /** @var string reCpaptcha v3 siteKey. */
    public $siteKey;

    /** @var string reCaptcha v3 api url  */
    public $apiUrl;

    /** @var string reCaptcha v3 action for this page. */
    public $action;

    /** @var string Your JS callback function that's executed when reCAPTCHA executed. */
    public $jsCallback;

    /** @var string */
    public $configComponentName = 'recaptcha';

    public function init()
    {
        parent::init();
        $recaptchaConfig = Yii::$app->get($this->configComponentName, false);

        if ($recaptchaConfig->siteKey) {
            $this->siteKey = $reCaptchaConfig->siteKey;
        } else {
            throw new InvalidConfigException('Required `siteKey` param isn\'t set.');
        }
        $this->apiUrl = RecaptchaConfig::API_URL;

        if (!$this->action) {
            $this->action = \preg_replace('/[^a-zA-Z\d\/]/', '', \urldecode(Yii::$app->request->url));
        }
    }

    public function run()
    {
        parent::run();
        $view = $this->view;

        $arguments = \http_build_query([
            'render' => $this->siteKey,
        ]);

        $view->registerJsFile(
            $this->apiUrl . '?' . $arguments,
            ['position' => $view::POS_END]
        );
        $view->registerJs(
            <<<JS
"use strict";
document.getElementById('btn-submit').addEventListener('click', function (event) {
    grecaptcha.ready(function() {
    grecaptcha.execute("{$this->siteKey}", {action: "{$this->action}"}).then(function(token) {
        document.getElementById("{$this->getReCaptchaId()}").val = token;
    });
});
});

JS
            , $view::POS_BEGIN);

        $this->customFieldPrepare();
    }

    protected function customFieldPrepare()
    {
        if ($this->hasModel()) {
            $inputName = Html::getInputName($this->model, $this->attribute);
        } else {
            $inputName = $this->name;
        }

        $options = $this->options;
        $options['id'] = $this->getReCaptchaId();

        echo Html::input('hidden', $inputName, null, $options);
    }

    protected function getReCaptchaId()
    {
        if (isset($this->options['id'])) {
            return $this->options['id'];
        }

        if ($this->hasModel()) {
            return Html::getInputId($this->model, $this->attribute);
        }

        return $this->id . '-' . $this->inputNameToId($this->name);
    }

    protected function inputNameToId($name)
    {
        return \str_replace(['[]', '][', '[', ']', ' ', '.'], ['', '-', '-', '', '-', '-'], \strtolower($name));
    }

 }
