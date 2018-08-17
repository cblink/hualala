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

        $encrypted = openssl_encrypt($json, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $key);

        return base64_encode($encrypted);
    }

    public function concater($data) {
        $t = '';
        foreach ($data as $key => $value) {
            if (is_bool($value) || is_float($value)) {
                $value = var_export($value, true);
            }

            $t .= $key.$value;
        }
        return $t;
    }

    protected function filterAndSort($data, $f= true)
    {
        $data = array_filter($data, function ($item) {
            return ! is_null($item);
        });

        ksort($data, SORT_STRING | SORT_FLAG_CASE);
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->filterAndSort($value);
            }
        }
        return $data;
    }

    protected function resetArray($array)
    {
        $reset = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $reset = array_merge($reset, $this->resetArray($value));
            } else {
                $reset[$key] = $value;
            }
        }
        return $reset;
    }

    /**
     * @param array $attributes
     *
     * @return array
     */
    public function signature(array $attributes)
    {
        $maker = function ($data) {
            $data = $this->resetArray($data);
            $data = $this->filterAndSort($data);
            $separated = $this->concater($data);

            $str = 'key'.$separated.'secret';
            return strtoupper(sha1($str));
        };

        $base = [
            'timestamp' => (string) (time() * 1000),
            'devID' => (int) $this->config[HualalaOptions::DEVELOPER_ID],
            'merchantsID' => (int) $this->config[HualalaOptions::MERCHANT_ID],
            'groupID' => (int) $this->config[HualalaOptions::GROUP_ID],
            'version' => 1.0,
            'devPwd' => $this->config[HualalaOptions::DEVELOPER_PASSWORD],
        ];

        if ($this->config->has(HualalaOptions::SHOP_ID)) {
            $base['shopID'] = (int) $this->config[HualalaOptions::SHOP_ID];
        }

        $base = array_merge($base, $attributes);
        $base['signature'] = $maker($base);

        return $base;
    }
}
