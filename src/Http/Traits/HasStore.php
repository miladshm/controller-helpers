<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Number;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithValidation;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait HasStore
{
    use WithModel, WithValidation;

    /**
     * Configuration for transaction usage
     */
    protected bool $useTransactionForStore = true;
    protected bool $enablePerformanceMetrics = false;

    /**
     * Handles the creation of a new model instance with optimized performance.
     *
     * @param Request $request The incoming request.
     * @return RedirectResponse|JsonResponse The response to be sent back to the client.
     * @throws ValidationException If the request data fails validation.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $useTransaction = $this->shouldUseTransaction();

        if ($useTransaction) {
            DB::beginTransaction();
        }

        try {
            // Prepare and validate
            $this->prepareForStore($request);
            $data = $this->setRequestClass($this->requestClass())->getValidationData($request);

            // Create the model
            $item = $this->createModel($data);

            // Execute callback
            $this->storeCallback($request, $item);

            if ($useTransaction) {
                DB::commit();
            }

            // Prepare response
            $response = $this->buildStoreResponse($request, $item);

            // Add performance metrics if enabled
            if ($this->enablePerformanceMetrics && config('app.debug')) {
                $this->addPerformanceMetrics($response, $startTime, $startMemory);
            }

            return $response;

        } catch (ValidationException|HttpException|AuthorizationException $exception) {
            if ($useTransaction) {
                DB::rollBack();
            }
            throw $exception;
        } catch (\Exception $exception) {
            if ($useTransaction) {
                DB::rollBack();
            }
            return ResponderFacade::setExceptionMessage($exception->getMessage())
                ->setMessage($exception->getMessage())
                ->respondError();
        }
    }

    /**
     * Determine if transaction should be used based on configuration and context.
     */
    protected function shouldUseTransaction(): bool
    {
        return $this->useTransactionForStore && !DB::transactionLevel();
    }

    /**
     * Create the model with optimized query.
     */
    protected function createModel(array $data): Model
    {
        return $this->model()->query()->create($data);
    }

    /**
     * Build the store response efficiently.
     */
    protected function buildStoreResponse(Request $request, Model $item): RedirectResponse|JsonResponse
    {
        if ($request->expectsJson()) {
            return $this->buildJsonStoreResponse($item);
        }

        return $this->buildRedirectStoreResponse();
    }

    /**
     * Build JSON response for API requests.
     */
    protected function buildJsonStoreResponse(Model $item): JsonResponse
    {
        $relations = $this->getRelations();

        if ($this->getApiResource()) {
            $resource = get_class($this->getApiResource());
            $itemData = (new $resource($item->fresh($relations)))->jsonSerialize();
        } else {
            $itemData = $item->load($relations)->toArray();
        }

        return ResponderFacade::setData($itemData)
            ->setMessage(Lang::get('responder::messages.success_store.status'))
            ->respond();
    }

    /**
     * Build redirect response for web requests.
     */
    protected function buildRedirectStoreResponse(): RedirectResponse
    {
        return Redirect::back()->with(Lang::get('responder::messages.success_status'));
    }

    /**
     * Add performance metrics to the response.
     */
    protected function addPerformanceMetrics($response, float $startTime, int $startMemory): void
    {
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $data['_performance'] = [
                'execution_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
                'memory_used' => Number::fileSize(memory_get_usage(true) - $startMemory),
                'transaction_used' => $this->useTransactionForStore,
            ];
            $response->setData($data);
        }
    }

    /**
     * Enable or disable transaction usage for store operations.
     */
    public function setUseTransaction(bool $useTransaction): self
    {
        $this->useTransactionForStore = $useTransaction;
        return $this;
    }

    /**
     * Enable or disable performance metrics collection.
     */
    public function setPerformanceMetrics(bool $enabled): self
    {
        $this->enablePerformanceMetrics = $enabled;
        return $this;
    }

    /**
     * A callback method that can be overridden to perform additional actions after a new model instance is created.
     *
     * @param Request $request The incoming request.
     * @param Model $item The newly created model instance.
     * @return void
     */
    protected function storeCallback(Request $request, Model $item): void
    {
        // Override in child classes for custom behavior
    }

    /**
     * A method that can be overridden to perform any necessary preparations before a new model instance is created.
     * This method also allows you to modify the request object before it is validated.
     * And you can implement the authorization logic here.
     *
     * @param Request $request The incoming request.
     * @return void
     */
    protected function prepareForStore(Request &$request): void
    {
        // Override in child classes for custom behavior
    }
}
