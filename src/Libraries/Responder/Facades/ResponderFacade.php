<?php

namespace Miladshm\ControllerHelpers\Libraries\Responder\Facades;

use App\Libraries\Responder\ResponseBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static JsonResponse respond()
 * @method static JsonResponse respondError()
 * @method static JsonResponse respondNotFound()
 * @method static JsonResponse respondNoAccess()
 * @method static JsonResponse respondUnavailable()
 * @method static JsonResponse respondInvalid()
 * @method static ResponseBuilder setMessage(string $message)
 * @method static ResponseBuilder setExceptionMessage(string $message)
 * @method static ResponseBuilder setData(iterable $data)
 * @method static ResponseBuilder setStatus(int $status)
 * @method static ResponseBuilder setHttpCode(int $http_code)
 */
class ResponderFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ResponseBuilder::class;
    }
}
