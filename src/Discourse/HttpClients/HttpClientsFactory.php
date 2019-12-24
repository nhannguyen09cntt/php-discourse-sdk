<?php
namespace Discourse\HttpClients;

use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;

class HttpClientsFactory
{
    private function __construct()
    {
        // a factory constructor should never be invoked
    }

    /**
     * HTTP client generation.
     *
     * @param DiscourseHttpClientInterface|Client|string|null $handler
     *
     * @throws Exception                If the cURL extension or the Guzzle client aren't available (if required).
     * @throws InvalidArgumentException If the http client handler isn't "curl", "stream", "guzzle", or an instance of Discourse\HttpClients\DiscourseHttpClientInterface.
     *
     * @return DiscourseHttpClientInterface
     */
    public static function createHttpClient($handler)
    {
        if (!$handler) {
            return self::detectDefaultClient();
        }
        if ($handler instanceof DiscourseHttpClientInterface) {
            return $handler;
        }

        if ('stream' === $handler) {
            return new DiscourseStreamHttpClient();
        }

        if ('guzzle' === $handler && !class_exists('GuzzleHttp\Client')) {
            throw new Exception('The Guzzle HTTP client must be included in order to use the "guzzle" handler.');
        }
        if ($handler instanceof Client) {
            return new DiscourseGuzzleHttpClient($handler);
        }
        if ('guzzle' === $handler) {
            return new DiscourseGuzzleHttpClient();
        }
        throw new InvalidArgumentException('The http client handler must be set to "curl", "stream", "guzzle", be an instance of GuzzleHttp\Client or an instance of Discourse\HttpClients\DiscourseHttpClientInterface');
    }

    /**
     * Detect default HTTP client.
     *
     * @return DiscourseHttpClientInterface
     */
    private static function detectDefaultClient()
    {
        if (class_exists('GuzzleHttp\Client')) {
            return new DiscourseGuzzleHttpClient();
        }
        return new DiscourseStreamHttpClient();
    }
}
