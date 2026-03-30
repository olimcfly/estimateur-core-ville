<?php

namespace Core;

class Request
{
    private array $params = [];
    private array $body   = [];
    private array $query  = [];
    private array $headers = [];

    public function __construct()
    {
        $this->body    = $this->parseBody();
        $this->query   = $_GET ?? [];
        $this->headers = $this->parseHeaders();
    }

    /*
    |--------------------------------------------------------------------------
    | Méthode HTTP
    |--------------------------------------------------------------------------
    */
    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    /*
    |--------------------------------------------------------------------------
    | URI
    |--------------------------------------------------------------------------
    */
    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $pos = strpos($uri, '?');
        return $pos !== false ? substr($uri, 0, $pos) : $uri;
    }

    /*
    |--------------------------------------------------------------------------
    | Params (route)
    |--------------------------------------------------------------------------
    */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    /*
    |--------------------------------------------------------------------------
    | Body
    |--------------------------------------------------------------------------
    */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->body;
    }

    /*
    |--------------------------------------------------------------------------
    | Query string
    |--------------------------------------------------------------------------
    */
    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    /*
    |--------------------------------------------------------------------------
    | Headers
    |--------------------------------------------------------------------------
    */
    public function header(string $key, mixed $default = null): mixed
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    public function bearerToken(): ?string
    {
        $auth = $this->header('authorization', '');
        if (str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }
        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers privés
    |--------------------------------------------------------------------------
    */
    private function parseBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            return json_decode($raw, true) ?? [];
        }

        return $_POST ?? [];
    }

    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = $value;
            }
        }
        return $headers;
    }
}
