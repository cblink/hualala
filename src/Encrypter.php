<?php

namespace Cblink\Hualala;

class Encrypter
{
    protected $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 加密 requestBody.
     *
     * @param  array
     *
     * @return string
     */
    public function encrypt($attributes)
    {
        $json = json_encode($attributes);
        $key = $this->config[HualalaOptions::AES_KEY];

        if (extension_loaded('mcrypt')) {
            $encrypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $json, MCRYPT_MODE_CBC, $key);
        } else {
            $encrypted = openssl_encrypt($json, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $key);
        }

        return base64_encode($encrypted);
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function signature(array $attributes)
    {
        $separater = function (array $data) {
            $k = '';
            foreach ($data as $key => $value) {
                if (is_bool($value) || is_float($value)) {
                    $value = var_export($value, true);
                }
                $k .= $key.$value;
            }

            return $k;
        };
        $maker = function ($data) use ($separater) {
            $data = array_filter($data);
            ksort($data);
            $separated = $separater($data);
            $str = 'key'.$separated.'secret';

            return strtoupper(sha1($str));
        };

        $base = [
            'timestamp' => time() * 1000,
            'devID' => $this->config[HualalaOptions::DEVELOPER_ID],
            'merchantsID' => $this->config[HualalaOptions::MERCHANT_ID],
            'groupID' => $this->config[HualalaOptions::GROUP_ID],
            'version' => 1.0,
            'devPwd' => $this->config[HualalaOptions::DEVELOPER_PASSWORD],
        ];

        if ($this->config->has(HualalaOptions::SHOP_ID)) {
            $base['shopID'] = $this->config[HualalaOptions::SHOP_ID];
        }

        $base = array_merge($base, $attributes);
        $base['signature'] = $maker($base);

        return $base;
    }
}
