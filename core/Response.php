<?php

namespace Core;

class Response
{
    private mixed $data;
    private int   $status;
    private array $headers;

    public function __construct(mixed $data = null, int $status = 200, array $headers = [])
    {
        $this->data    = $data;
        $this->status  = $status;
        $this->headers = array_merge([
            'Content-Type' => 'application/json',
        ], $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | Envoi de la réponse
    |--------------------------------------------------------------------------
    */
    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        if ($this->data !== null) {
            echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Factories statiques
    |--------------------------------------------------------------------------
    */
    public static function json(mixed $data, int $status = 200): self
    {
        return new self($data, $status);
    }

    public static function success(mixed $data = null, string $message = 'OK'): self
    {
        return new self([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], 200);
    }

    public static function error(string $message, int $status = 400, mixed $errors = null): self
    {
        $body = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return new self($body, $status);
    }

    public static function notFound(string $message = 'Ressource non trouvée'): self
    {
        return self::error($message, 404);
    }

    public static function unauthorized(string $message = 'Non autorisé'): self
    {
        return self::error($message, 401);
    }

    public static function forbidden(string $message = 'Accès refusé'): self
    {
        return self::error($message, 403);
    }

    /*
    |--------------------------------------------------------------------------
    | Getters
    |--------------------------------------------------------------------------
    */
    public function getStatus(): int
    {
        return $this->status;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
