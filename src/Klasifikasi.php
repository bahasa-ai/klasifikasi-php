<?php


namespace klasifikasi;

use klasifikasi\util\Request;

class Klasifikasi {

  private static ?string $baseUrl = 'https://api.klasifikasi.com';

  private static ?Klasifikasi $instance = null;

  private ?Request $clientRequest;

  public static function build(array $buildParams): Klasifikasi {
    if (static::$instance === null) {
      static::$instance = new static($buildParams);
    }
    return static::$instance;
  }

  public function __construct(array $buildParams) {

    $this->clientRequest = new Request();

    foreach ($buildParams as $clientData) {

      $clientId = $clientData['clientId'];
      $clientSecret = $clientData['clientSecret'];

      [$responseBody, $responseCode] = $this->clientRequest->request('post', '/api/v1/auth/token', [], [], [
          'clientId' => $clientId,
          'clientSecret' => $clientSecret
      ]);

      if ($responseCode != 200) {
        throw new \Exception($responseBody['error']);
      }

      [$responseBody, $responseCode] = $this->clientRequest->request('get', '/api/v1/auth/activeClient', [
          'Authorization' => "Bearer {$responseBody['auth']['token']}"
      ], [], []);



    }
  }

  public static function getBaseUrl(): ?string
  {
    return self::$baseUrl;
  }

  public static function setBaseUrl(?string $baseUrl): void
  {
    self::$baseUrl = $baseUrl;
  }



}
