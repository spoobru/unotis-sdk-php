<?php namespace Spoob\Unotis\Bridge;

use Spoob\Unotis\Interfaces\Request as iRequest;


class Request implements iRequest
{
    const USER_AGENT = 'UNOTIS-SDK/1.1 (+https://unotis.ru/documentation))';

    /**
     * @var bool
     */
    private bool $use_curl;

    /**
     * @param bool $use_curl
     */
    public function __construct(bool $use_curl = true)
    {
        $this->use_curl = $use_curl;
    }

    /**
     * @param string $url
     * @param array $data
     *
     * @return string
     */
    public function post(string $url, array $data): string
    {
        return $this->use_curl && function_exists('curl_version')
            ? $this->asCurl('post', $url, $data)
            : $this->asStreamContext('post', $url, $data);
    }

    /**
     * @param string $url
     * @param array $data
     *
     * @return string
     */
    public function get(string $url, array $data): string
    {
        return $this->use_curl && function_exists('curl_version')
            ? $this->asCurl('get', $url, $data)
            : $this->asStreamContext('get', $url, $data);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $data
     *
     * @return string
     */
    private function asStreamContext(string $method, string $url, array $data = []): string
    {
        $http = [
            'method'  => strtoupper($method),
            'header'  => 'User-Agent: ' . self::USER_AGENT . "\r\n",
        ];

        if ('get' !== $method) {
            $http['content'] = http_build_query($data);
            $http['header'] .= 'Content-type: application/form-data' . "\r\n";
        }

        $context = stream_context_create(['http' => $http]);

        return file_get_contents($url, false, $context);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array $data
     *
     * @return string
     */
    private function asCurl(string $method, string $url, array $data = []): string
    {
        $curl = curl_init();

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => self::USER_AGENT,
        ];

        if ('get' !== $method) {
            if ('post' === $method) {
                $options[CURLOPT_POST] = true;
            } else if (in_array($method, ['delete', 'put', 'patch'])) {
                $options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);
            }

            $options[CURLOPT_POSTFIELDS] = http_build_query($data);
        }

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}