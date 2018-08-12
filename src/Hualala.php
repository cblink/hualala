<?php

namespace Cblink\Hualala;

use GuzzleHttp\Client;
use Hanson\Foundation\Foundation;

class Hualala extends Foundation
{
    protected $providers = [
        EncrypterServiceProvider::class,
    ];

    /**
     * 发起 HTTP 请求
     *
     * @param string $endpoint
     * @param array  $payload
     *
     * @return array
     */
    public function request($endpoint, $payload = [])
    {
        $client = new Client([
            'base_uri' => 'https://dohko-open-api.hualala.com',
        ]);

        $response = $client->post($endpoint, [
            'form_params' => array_merge($this['encrypter']->signature($payload), [
                'requestBody' => $this['encrypter']->encrypt($payload),
            ]),
        ]);

        return json_decode((string) $response->getBody(), JSON_OBJECT_AS_ARRAY);
    }
}
