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
 * Yii2 Google recaptcha v3 widget.
 *
 * For example:
 *
 *```php
 * <?= $form->field($model, 'recaptcha')->widget(
 *  RecaptchaWidget::className(),
 *  [
 *   'siteKey' => 'your siteKey', // unnecessary is recaptcha component was set up
 *   'action' => 'homepage',
 *  ]
 * ) ?>
 *```
 *
 * or
 *
 *```php
 * <?= RecaptchaWidget::widget([
 *  'name' => 'recaptcha',
 *  'siteKey' => 'your siteKey', // unnecessary is reCaptcha component was set up
 *  'action' => 'homepage',
 * ]) ?>
 *```
 *
 * @see https://developers.google.com/recaptcha/docs/v3
 * @author Klaus Mergen <klausmergen1@gmail.com>
 * @package kmergen\yii2-recaptcha3
 */
class RecaptchaWidget extends InputWidget
{
    /** @var string recpaptcha v3 siteKey. */
    public $siteKey;

    /** @var string recaptcha v3 api url */
    public $apiUrl;

    /** @var string recaptcha v3 action for this page. */
    public $action;

    /** @var string Your JS callback function that's executed when recaptcha executed. */
    public $jsCallback;

    /** @var string */
    public $configComponentName = 'recaptcha3';

    public function init()
    {
        parent::init();
        $recaptchaConfig = Yii::$app->get($this->configComponentName, false);

        if ($recaptchaConfig->siteKey) {
            $this->siteKey = $recaptchaConfig->siteKey;
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
 document.addEventListener('DOMContentLoaded', (ev) => {
     var el = document.getElementById("{$this->getReCaptchaId()}");
     var form = el.form;
     form.addEventListener('submit', function (event) {
         event.preventDefault();
         grecaptcha.ready(function() {
             grecaptcha.execute("{$this->siteKey}", {action: "{$this->action}"}).then(function(token) {
                 el.value = token;
                 form.submit();
             });
         });
     });
});

JS
            , $view::POS_END);

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
