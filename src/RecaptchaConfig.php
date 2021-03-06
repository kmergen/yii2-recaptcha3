<?php
/**
 * KM Websolutions Projects
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2010 KM Websolutions
 * @license http://www.yiiframework.com/license/
 */

namespace kmergen\recaptcha3;

/**
 * RecaptchaConfig
 * Global configuration
 *
 * @see https://developers.google.com/recaptcha/docs/v3
 * @author Klaus Mergen <klausmergen1@gmail.com>
 * @package kmergen\yii2-recaptcha3
 */
class RecaptchaConfig
{
    const API_URL = 'https://www.google.com/recaptcha/api.js';
    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';

    /** @var string Your sitekey for reCAPTCHA v3. */
    public $siteKey;

    /** @var string Your secret for reCAPTCHA v3. */
    public $secret;

       /** @var string */
    public $apiUrl;

    /** @var string */
    public $verifyUrl;

    /** @var boolean Check host name. */
    public $checkHostName = false;

    /** @var \yii\httpclient\Request */
    public $httpClientRequest;

}
