<?php

/**
 * Class OpenSslEncryption
 */
class OpenSslEncryption
{
    /**
     * @param string $key
     * @return string
     */
    public static function encrypt(string $key): string
    {
        return openssl_encrypt(
            $key,
            Yii::$app->params['environment']['sslKeys']['key-cipher'],
            Yii::$app->params['environment']['sslKeys']['encryption-key'],
            0,
            Yii::$app->params['environment']['sslKeys']['encryption-iv']
        );
    }

    /**
     * @param string|null $key
     * @return string
     */
    public static function decrypt(?string $key): string
    {
        return openssl_decrypt(
            $key,
            Yii::$app->params['environment']['sslKeys']['key-cipher'],
            Yii::$app->params['environment']['sslKeys']['encryption-key'],
            0,
            Yii::$app->params['environment']['sslKeys']['encryption-iv']
        );
    }
}
