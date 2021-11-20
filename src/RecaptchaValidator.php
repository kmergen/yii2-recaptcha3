<?php
/**
 * KM Websolutions Projects
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2010 KM Websolutions
 * @license http://www.yiiframework.com/license/
 */


namespace kmergen\recaptcha3;

use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\httpclient\Client as HttpClient;
use yii\validators\Validator;
use Yii;

/**
 * RecaptchaValidator
 *
 * @see https://developers.google.com/recaptcha/docs/v3
 * @author Klaus Mergen <klausmergen1@gmail.com>
 * @package kmergen\yii2-recaptcha3
 */
class RecaptchaValidator extends Validator
{
    /** @var boolean Whether to skip this validator if the input is empty. */
    public $skipOnEmpty = false;

    /** @var string The shared key between your site and ReCAPTCHA. */
    public $secret;

    /** @var string Google ReCaptcha v3 verify url. */
    public $verifyUrl;

    /** @var \yii\httpclient\Request */
    public $httpClientRequest;

    /** @var string */
    public $configComponentName = 'recaptcha3';

    /** @var boolean Check host name. Default is false. */
    public $checkHostName;

    /** @var boolean */
    protected $isValid;

    /** @var float|callable */
    public $threshold = 0.5;

    /** @var string|boolean Set to false if you don`t need to check action. */
    public $action;

    public function init()
    {
        parent::init();

        $recaptchaConfig = Yii::$app->get($this->configComponentName, false);

        if ($recaptchaConfig->secret) {
            $this->secret = $recaptchaConfig->secret;
        } else {
            throw new InvalidConfigException('Required `secret` param isn\'t set.');
        }

        if ($this->message === null) {
            $this->message = Yii::t('yii', 'The verification code is incorrect.');
        }

        $this->verifyUrl = RecaptchaConfig::VERIFY_URL;

        $this->httpClientRequest = (new HttpClient())->createRequest();

        $this->checkHostName = $recaptchaConfig->checkHostName;

        if ($this->action === null) {
            $this->action = \preg_replace('/[^a-zA-Z\d\/]/', '', \urldecode(Yii::$app->request->url));
        }
    }

    /**
     * @param string $value
     * @return array
     * @throws Exception
     * @throws \yii\base\InvalidParamException
     */
    protected function getResponse($value)
    {
        $response = $this->httpClientRequest
            ->setMethod('POST')
            ->setUrl($this->verifyUrl)
            ->setData(['secret' => $this->secret, 'response' => $value, 'remoteip' => Yii::$app->request->userIP])
            ->send();
        if (!$response->isOk) {
            throw new Exception('Unable connection to the captcha server. Status code ' . $response->statusCode);
        }

        return $response->data;
    }

    /**
     * @param string|array $value
     * @return array|null
     * @throws Exception
     * @throws \yii\base\InvalidParamException
     */
    protected function validateValue($value)
    {
        if ($this->isValid === null) {
            if (!$value) {
                $this->isValid = false;
            } else {
                $response = $this->getResponse($value);
                if (isset($response['error-codes'])) {
                    $this->isValid = false;
                } else {
                    if (!isset($response['success'], $response['action'], $response['hostname'], $response['score']) ||
                        $response['success'] !== true ||
                        ($this->action !== false && $response['action'] !== $this->action) ||
                        ($this->checkHostName && $response['hostname'] !== Yii::$app->request->hostName)
                    ) {
                        throw new Exception('Invalid recaptcha verify response.');
                    }

                    if (\is_callable($this->threshold)) {
                        $this->isValid = (bool)\call_user_func($this->threshold, $response['score']);
                    } else {
                        $this->isValid = $response['score'] >= $this->threshold;
                    }
                }
            }
        }

        return $this->isValid ? null : [$this->message, []];
    }
}
