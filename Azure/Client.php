<?php

namespace Progrupa\AzureBundle\Azure;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class Client
{
    /** @var  \GuzzleHttp\Client */
    private $http;
    /** @var  string */
    private $azureAccount;
    /** @var  string */
    private $azureKey;
    /** @var string  */
    private $apiVersion = '2016-07-01.3.1';

    /**
     * Client constructor.
     * @param \GuzzleHttp\Client $http
     * @param string $azureAccount
     * @param string $azureKey
     */
    public function __construct(\GuzzleHttp\Client $http, $azureAccount, $azureKey)
    {
        $this->http = $http;
        $this->azureAccount = $azureAccount;
        $this->azureKey = $azureKey;
    }

    public function test()
    {
        $req = new Request('get', 'pools');

        return $this->send($req);
    }

    protected function send(Request $request, array $parameters = [])
    {
        $parameters['api-version'] = $this->apiVersion;
        /** @var Request $request */
        $request = $request->withAddedHeader('Content-Type', 'application/json');
        $request = $request->withAddedHeader('ocp-date', gmdate('D, d M Y H:i:s T'));

        $canonicalizedHeaders = $this->prepareCanonicalizedHeaders($request);
        $canonicalizedResource = $this->prepareCanonicalizedResource($request, $parameters);

        $signature = sprintf(
            "%s\n%s\n%s\n%s\n%s\n%s\n%s\n%s\n%s\n%s\n%s\n%s\n%s\n%s",
            strtoupper($request->getMethod()),
            implode(',', $request->getHeader('Content-Encoding')),
            implode(',', $request->getHeader('Content-Language')),
            implode(',', $request->getHeader('Content-Length')),
            implode(',', $request->getHeader('Content-MD5')),
            implode(',', $request->getHeader('Content-Type')),
            implode(',', $request->getHeader('Date')),
            implode(',', $request->getHeader('If-Modified-Since')),
            implode(',', $request->getHeader('If-Match')),
            implode(',', $request->getHeader('If-None-Match')),
            implode(',', $request->getHeader('If-Unmodified-Since')),
            implode(',', $request->getHeader('Range')),
            trim($canonicalizedHeaders),
            trim($canonicalizedResource)
        );

        $hmac = hash_hmac('sha256', $signature, base64_decode($this->azureKey), true);
        $request = $request->withAddedHeader(
            'Authorization',
            sprintf("SharedKey %s:%s", $this->azureAccount, base64_encode($hmac))
        );

        try {
            $response = $this->http->send(
                $request,
                [
                    'query' => $parameters
                ]
            );
            return new Response($response->getBody()->getContents());

        } catch (RequestException $exception) {
            $response = $exception->getResponse();
            if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 500) {
                $error = new Error($response->getStatusCode(), $response->getBody()->getContents());
                return $error;
            } else {
                throw $exception;
            }
        }
    }

    /**
     * @param Request $request
     * @return array|string
     */
    protected function prepareCanonicalizedHeaders(Request $request)
    {
        $canonicalizedHeaders = '';
        $headers = $request->getHeaders();
        $preppedHeaders = [];
        foreach ($headers as $header => $value) {
            $preppedHeaders[strtolower($header)] = $value;
        }
        ksort($preppedHeaders);

        foreach ($preppedHeaders as $header => $value) {
            if (0 === strpos($header, 'ocp-')) {
                $canonicalizedHeaders .= sprintf("%s:%s\n", trim($header), trim(implode(',', $value)));
            }
        }

        return trim($canonicalizedHeaders);
    }

    protected function prepareCanonicalizedResource(Request $request, array $parameters = [])
    {
        $uri = $request->getUri();

        $path = ltrim($uri->getPath(), '/');

        $preppedParameters = '';
        if (count($parameters)) {
            foreach ($parameters as $name => $value) {
                $queryParameters[strtolower($name)] = trim(urldecode(is_array($value) ? implode(',', $value): $value));
            }
            ksort($queryParameters);

            foreach ($queryParameters as $header => $value) {
                $preppedParameters .= sprintf("%s:%s\n", $header, is_array($value) ? implode(',', $value) : $value);
            }
        }

        return sprintf(
            "/%s/%s\n%s",
            strtolower($this->azureAccount),
            $path,
            trim($preppedParameters)
        );
    }
}
