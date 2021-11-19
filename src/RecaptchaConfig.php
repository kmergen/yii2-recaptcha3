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
 * @author Klaus Mergen <klausmergen1@gmail.com>
 */
class RecaptchaConfig
{
    const API_URL = 'https://www.google.com/recaptcha/api.js';
    const JS_API_URL_ALTERNATIVE = '//www.recaptcha.net/recaptcha/api.js';

    const VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    const SITE_VERIFY_URL_ALTERNATIVE = 'https://www.recaptcha.net/recaptcha/api/siteverify';

    /** @var string Your sitekey for reCAPTCHA v3. */
    public $siteKey;

    /** @var string Your secret for reCAPTCHA v3. */
    public $secret;

       /** @var string Use [[JS_API_URL_ALTERNATIVE]] when [[JS_API_URL_DEFAULT]] is not accessible. */
    public $apiUrl;

    /** @var string Use [[SITE_VERIFY_URL_ALTERNATIVE]] when [[SITE_VERIFY_URL_DEFAULT]] is not accessible. */
    public $verifyUrl;

    /** @var boolean Check host name. */
    public $checkHostName;

    /** @var \yii\httpclient\Request */
    public $httpClientRequest;

}
