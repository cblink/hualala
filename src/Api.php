<?php


namespace Cblink\Hualala;


class Api
{

    public function __construct()
    {

    }

    public function request($params)
    {
        $data = array_merge($params, [
            'devID' => $this->devId,
            'merchantsID' => $this->merchantId,
            'groupID' => $this->groupId,
            'shopID' => $this->shopId,
            'version' => 1.0,
            'timestamp' => time(),
        ]);

        $this->signature($data);

        openssl_encrypt(json_encode($params), 'AES-128-CBC', $this->merchantSecret);

    }

    public function signature($params)
    {
        $params['devPwd'] = $this->devSecret;

        ksort($params);

        $str = '';
        foreach ($params as $key => $param) {
            if (!$param) {
                continue;
            }
            $str .= $key.$param;
        }

        return sha1($str);
    }

}