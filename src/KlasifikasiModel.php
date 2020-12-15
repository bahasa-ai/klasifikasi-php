<?php


namespace klasifikasi;


class KlasifikasiModel {

  private string $_clientId;

  private string $_clientSecret;

  private string $_token;

  private int $_expiredAfter;

  private string $name;

  private string $publicId;

  private array  $tags;

  public function __construct(string $_clientId, string $_clientSecret, string $_token, int $_expiredAfter, string $name, string $publicId, array $tags) {
    $this->_clientId = $_clientId;
    $this->_clientSecret = $_clientSecret;
    $this->_token = $_token;
    $this->_expiredAfter = $_expiredAfter;
    $this->name = $name;
    $this->publicId = $publicId;
    $this->tags = $tags;
  }

  public function getClientId(): string {
    return $this->_clientId;
  }

  public function getClientSecret(): string {
    return $this->_clientSecret;
  }

  public function getToken(): string {
    return $this->_token;
  }

  public function getExpiredAfter(): int {
    return $this->_expiredAfter;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getPublicId(): string {
    return $this->publicId;
  }

  public function getTags(): array {
    return $this->tags;
  }

}