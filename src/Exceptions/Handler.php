<?php

namespace Miladshm\ControllerHelpers\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\App;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (NotFoundHttpException|ModelNotFoundException $e, $request) {
            if ($request->is("api/*")) {
                return ResponderFacade::respondNotFound();
            }
        });

        $this->renderable(function (HttpException $e, $request) {
            if ($request->is("api/*")) {
                return ResponderFacade::setHttpCode($e->getStatusCode())->setMessage($e->getMessage())->respond();
            }
        });

        $this->renderable(function (\Exception $e, $request) {
            if (App::environment('production'))
                return ResponderFacade::setMessage('خطای سرور رخ داده لطفا با پشتیبانی تماس بگیرید')->respondError();
            else
                return ResponderFacade::setMessage($e->getMessage())->setData($e->getTrace())->respondError();
        });
    }

}
