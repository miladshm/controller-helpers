<?php

namespace App\Libraries\Responder;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ResponseBuilder
{
    private ?string $message = null;
    private ?string $exception_message = null;
    private ?int $http_code = null;
    private int $code = 0;
    private iterable $data = [];

    /**
     * @param string $message
     * @return ResponseBuilder
     */
    public function setMessage(string $message): ResponseBuilder
    {
        $this->message = $message;
        return $this;
    }

    /**
     * @param int $http_code
     * @return ResponseBuilder
     */
    public function setHttpCode(int $http_code): ResponseBuilder
    {
        $this->http_code = $http_code;
        return $this;
    }

    /**
     * @param iterable $data
     * @return ResponseBuilder
     */
    public function setData(iterable $data): ResponseBuilder
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->http_code;
    }

    /**
     * @return iterable
     */
    public function getData(): iterable
    {
        return $this->data;
    }

    public function respond(): JsonResponse
    {
        $response = [
            'Status' => $this->code,
            'Message' => $this->message ?? trans('messages.success_status.status')
        ];
        if (isset($this->data)) {
            $response['Data'] = $this->data;
        }

        return response()
            ->json($response, $this->http_code ?? 200);
    }

    /**
     * @param int $code
     * @return ResponseBuilder
     */
    public function setCode(int $code): ResponseBuilder
    {
        $this->code = $code;
        return $this;
    }

    public function respondNotFound(): JsonResponse
    {
        return $this->setMessage($this->message ?? trans('messages.not_found_status.status'))
            ->setHttpCode($this->http_code ?? 404)
            ->respond();
    }

    public function respondNoAccess(): JsonResponse
    {
        return $this->setMessage($this->message ?? trans('messages.no_access_status.status'))
            ->setHttpCode($this->http_code ?? 403)
            ->respond();
    }

    public function respondUnavailable(): JsonResponse
    {
        return $this->setMessage($this->message ?? trans('messages.service_unavailable.status'))
            ->setHttpCode($this->http_code ?? 499)
            ->respond();
    }

    public function respondInvalid(): JsonResponse
    {
        return $this->setMessage($this->message ?? trans('messages.invalid_input.status'))
            ->setHttpCode($this->http_code ?? 422)
            ->respond();
    }

    public function respondError(): JsonResponse
    {
        $this->message = $this->message ?? (env('APP_DEBUG') ? $this->exception_message : trans('messages.error_status.status'));
        return $this->setMessage($this->message)
            ->setHttpCode($this->http_code ?? 500)
            ->respond();
    }

    /**
     * @param string $exception_message
     * @return ResponseBuilder
     */
    public function setExceptionMessage(string $exception_message): ResponseBuilder
    {
        $this->exception_message = $exception_message;
        return $this;
    }

}
