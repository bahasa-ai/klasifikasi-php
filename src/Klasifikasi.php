<?php


namespace klasifikasi;

use klasifikasi\util\Request;

class Klasifikasi {

  private static $baseUrl = 'https://api.klasifikasi.com';

  private static $instance = null;

  private $clientRequest;

  private $modelMapping;

  public static function build(array $buildParams): Klasifikasi {
    if (static::$instance === null) {
      static::$instance = new Klasifikasi($buildParams);
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

  public function classify(string $publicId, string $query): array {
    if (!array_key_exists($publicId, $this->modelMapping)) {
      throw new \Exception("Model not found !");
    }
    $model = $this->modelMapping[$publicId];

    $requestBody = [ "query" => $query ];
    $headers = [ "Authorization" => "Bearer {$model->getToken()}" ];
    $path = "/api/v1/classify/{$publicId}";
    [$responseBody, $responseCode] = $this->clientRequest->request(
        'post', $path, $headers, [], $requestBody
    );

    if ($responseCode != 200) {
      throw new \Exception($responseBody['error']);
    }

    return $responseBody;

  }

  public function logs(string $publicId, array $params): array {
    if (!array_key_exists($publicId, $this->modelMapping)) {
      throw new \Exception("Model not found !");
    }
    $model = $this->modelMapping[$publicId];

    if (!array_key_exists('startedAt', $params) || !array_key_exists('endedAt', $params)) {
      throw new \Exception('startedAt & endedAt is required !');
    } else if (!($params['startedAt'] instanceof \DateTime) || !($params['endedAt'] instanceof \DateTime)) {
      throw new \Exception('starteddAt & endedAt must be instance of DateTime');
    }

    $queryParams = [
        'startedAt' => $params['startedAt']->format(\DateTime::ISO8601),
        'endedAt' => $params['endedAt']->format(\DateTime::ISO8601)
    ];
    if (array_key_exists('skip', $params)) {
      $queryParams['skip'] = $params['skip'];
    }
    if (array_key_exists('take', $params)) {
      $queryParams['take'] = $params['take'];
    }
    $path = "/api/v1/history/{$publicId}";
    $headers = [ "Authorization" => "Bearer {$model->getToken()}" ];

    [$responseBody, $responseCode] = $this->clientRequest->request('get', $path, $headers, $queryParams, []);
    if ($responseCode != 200) {
      throw new \Exception($responseBody['error']);
    }
    return $responseBody;

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
