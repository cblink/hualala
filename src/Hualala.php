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
        $baseUri = $this['config']['debug'] ? 'https://dohko-open-api.hualala.com' : 'https://www-openapi.hualala.com';

        $client = new Client([
            'base_uri' => $baseUri,
        ]);

        $response = $client->post($endpoint, [
            'form_params' => array_merge($this['encrypter']->signature($payload), [
                'requestBody' => $this['encrypter']->encrypt($payload),
            ]),
        ]);

        $result = json_decode((string) $response->getBody(), JSON_OBJECT_AS_ARRAY);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid response.');
        }

        if ($result['code'] == '000') {
            return $result;
        }

        throw new Exception($result['message']);
    }
}
