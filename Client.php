<?php namespace Spoob\Unotis;

/**
 *
 */
class Client
{
    /**
     * @var int
     */
    private int $version;

    /**
     * @var string
     */
    private string $token;

    /**
     * @param string $token
     * @param int $version
     */
    public function __construct(string $token, int $version = 1)
    {
        $this->token = $token;
        $this->version = $version;
    }

    /**
     * Create message in service.
     *
     * @param string $subject
     * @param string $text
     * @param string|null $url
     *
     * @return string
     */
    public function createMessage(string $subject, string $text, string $url = null): string
    {
        return $this->request('message', compact('subject', 'text', 'url'));
    }

    /**
     * Create message and send e-mail.
     *
     * @param string $addressee
     * @param string $subject
     * @param string $text
     * @param string|null $url
     *
     * @return string
     */
    public function sendEmail(string $addressee, string $subject, string $text, string $url = null): string
    {
        return $this->request('email', compact('addressee', 'subject', 'text', 'url'));
    }

    /**
     * Create message and send to telegram messenger.
     *
     * @param string $subject
     * @param string $text
     * @param string|null $url
     *
     * @return string
     */
    public function writeToTelegram(string $subject, string $text, string $url = null): string
    {
        return $this->request('telegram', compact('subject', 'text', 'url'));
    }

    /**
     * Raw request.
     * @param string $type
     * @param array $data
     *
     * @return string
     */
    private function request(string $type, array $data): string
    {
        $data['token'] = $this->token;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_URL => 'https://unotis.ru/api/' . $type . '/v' . $this->version . '/send',
            CURLOPT_USERAGENT => 'UNOTIS/1.0 (+https://unotis.ru/documentation))',
        ]);

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }
}