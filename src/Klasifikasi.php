<?php


namespace klasifikasi;

use klasifikasi\util\Request;

class Klasifikasi {

  private static ?string $baseUrl = 'https://api.klasifikasi.com';

  private static ?Klasifikasi $instance = null;

  private ?Request $clientRequest;

  private array $modelMapping;

  public static function build(array $buildParams): Klasifikasi {
    if (static::$instance === null) {
      static::$instance = new self($buildParams);
    }
    return static::$instance;
  }

  public function __construct(array $buildParams) {

    $this->clientRequest = new Request();

    $this->modelMapping = array();

    foreach ($buildParams as $clientData) {

      if (!array_key_exists('clientId', $clientData) || !array_key_exists('clientSecret', $clientData)) {
        throw new \Exception('clientId & clientSecret is required !');
      }

      $clientId = $clientData['clientId'];
      $clientSecret = $clientData['clientSecret'];

      [$responseBody, $responseCode] = $this->clientRequest->request('post', '/api/v1/auth/token', [], [], [
          'clientId' => $clientId,
          'clientSecret' => $clientSecret
      ]);
      if ($responseCode != 200) {
        throw new \Exception($responseBody['error']);
      }
      $token = $responseBody['auth']['token'];
      $expiredAfter = $responseBody['auth']['expiredAfter'];


      [$responseBody, $responseCode] = $this->clientRequest->request('get', '/api/v1/auth/activeClient', [
          'Authorization' => "Bearer {$responseBody['auth']['token']}"
      ], [], []);
      if ($responseCode != 200) {
        throw new \Exception($responseBody['error']);
      } else if (!array_key_exists('model', $responseBody)) {
        throw new \Exception("ClientId & ClientSecret didnt have any model !");
      }

      $model = $responseBody['model'];

      $this->modelMapping[$model['publicId']] = new KlasifikasiModel($clientId, $clientSecret,
          $token, $expiredAfter, $model['name'],$model['publicId'], $model['tags']);

    }
  }

  public function getModels(): array {
    return $this->modelMapping;
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
