<?php

namespace Miladshm\ControllerHelpers\Libraries\Responder;

use ArrayAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Response;

class ResponseBuilder
{
    private ?string $message = null;
    private ?string $exception_message = null;
    private ?int $http_code = null;
    private int $code = 0;
    private ArrayAccess|iterable $data;

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
     * @param ArrayAccess|iterable $data
     * @return ResponseBuilder
     */
    public function setData(ArrayAccess|iterable $data): ResponseBuilder
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
     * @return ArrayAccess|iterable
     */
    public function getData(): ArrayAccess|iterable
    {
        return $this->data;
    }

    public function respond(): JsonResponse
    {
        $response = [
            getConfigNames('response.field_names.code') => $this->http_code ?? 200,
            getConfigNames('response.field_names.message') => $this->message ?? Lang::get('responder::messages.success_status.status')
        ];
        if (isset($this->data)) {
            $response[getConfigNames('response.field_names.data')] = $this->data;
        }


        return Response::json($response, $this->http_code ?? 200);
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
        return $this->setMessage($this->message ?? Lang::get('responder::messages.not_found_status.status'))
            ->setHttpCode($this->http_code ?? 404)
            ->respond();
    }

    public function respondNoAccess(): JsonResponse
    {
        return $this->setMessage($this->message ?? Lang::get('responder::messages.no_access_status.status'))
            ->setHttpCode($this->http_code ?? 403)
            ->respond();
    }

    public function respondUnavailable(): JsonResponse
    {
        return $this->setMessage($this->message ?? Lang::get('responder::messages.service_unavailable.status'))
            ->setHttpCode($this->http_code ?? 499)
            ->respond();
    }

    public function respondInvalid(): JsonResponse
    {
        return $this->setMessage($this->message ?? Lang::get('responder::messages.invalid_input.status'))
            ->setHttpCode($this->http_code ?? 422)
            ->respond();
    }

    public function respondError(): JsonResponse
    {
        $this->message = $this->message ?? (env('APP_DEBUG') ? $this->exception_message : Lang::get('responder::messages.error_status.status'));
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

    public function getCode(): int
    {
        return $this->code;
    }

}
