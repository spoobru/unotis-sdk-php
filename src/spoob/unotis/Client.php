<?php namespace Spoob\Unotis;

use Spoob\Unotis\Bridge\Request;
use Spoob\Unotis\Interfaces\Client as iClient;

/**
 * Unotis client
 *
 * @author SPOOB <info@spoob.ru>
 * @package Unotis
 * @version 1.0
 */
class Client implements iClient
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
     * @var bool
     */
    private bool $use_curl;

    /**
     * @param string $token
     * @param int $version
     * @param bool $use_curl
     */
    public function __construct(string $token, int $version = 1, bool $use_curl = true)
    {
        $this->token = $token;
        $this->version = $version;
        $this->use_curl = $use_curl;
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
     * @param string $type
     *
     * @return string
     */
    private function getApiUrl(string $type): string
    {
        return 'https://unotis.ru/api/' . $type . '/v' . $this->version . '/send';
    }

    /**
     * Raw request.
     *
     * @param string $type
     * @param array $data
     *
     * @return string
     */
    private function request(string $type, array $data): string
    {
        $request = new Request($this->use_curl);
        $data['token'] = $this->token;
        $url = $this->getApiUrl($type);

        return $request->post($url, $data);
    }
}