<?php


namespace klasifikasi\util;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use http\Exception;
use klasifikasi\Klasifikasi;

class Request {

  protected ?Client $http = null;

  public function __construct() {
    $baseUri = strval(Klasifikasi::getBaseUrl());
    $this->http = new Client([
        'base_uri' => $baseUri,
        'verify' => false,
    ]);
  }

  public function request(string $method, string $path, array $headers, array $queryParams, array $requestBody): array {

    $queryString = http_build_query($queryParams);
    $path = "{$path}/?{$queryString}";

    return $this->doRequest($method, $path, $headers, $requestBody);
  }

  private function doRequest(string $method, string $fullUrl, array $headers, array $requestBody): array
  {
    var_dump($fullUrl);

    try {
      $options = [ 'headers' => $headers ];
      if (count($requestBody) > 0) {
        $options = [
            'headers' => $headers,
            RequestOptions::JSON => $requestBody
        ];
      }

      $response = $this->http->request($method, $fullUrl, $options);
    } catch (RequestException $e) {
      $response = $e->getResponse();
      $body = json_decode($response->getBody()->getContents(), true);
      $code = $response->getStatusCode();
      return [$body, $code];
    }

    $body = json_decode($response->getBody()->getContents(), true);
    $code = $response->getStatusCode();
    return [$body, $code];

  }


}