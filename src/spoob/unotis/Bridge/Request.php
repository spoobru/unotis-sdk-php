<?php namespace Spoob\Unotis\Bridge;

use Spoob\Unotis\Interfaces\Request as iRequest;


class Request implements iRequest
{
    const USER_AGENT = 'UNOTIS/1.0 (+https://unotis.ru/documentation))';

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
            ? $this->asCurl($url, $data)
            : $this->asStreamContext($url, $data);    }

    /**
     * @param string $url
     * @param array $data
     *
     * @return string
     */
    private function asStreamContext(string $url, array $data): string
    {
        $context = stream_context_create(['http' =>
            [
                'method'  => 'POST',
                'header'  => 'Content-type: application/form-data' . "\r\n"
                    . 'User-Agent: ' . self::USER_AGENT . "\r\n",
                'content' => http_build_query($data),
            ]
        ]);

        return file_get_contents($url, false, $context);
    }

    /**
     * @param string $url
     * @param array $data
     *
     * @return string
     */
    private function asCurl(string $url, array $data): string
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => self::USER_AGENT,
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}