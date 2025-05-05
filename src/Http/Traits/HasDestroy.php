<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait HasDestroy
{
    use WithModel;


    /**
     * Deletes a record from the database.
     *
     * This function handles both soft-deleted and hard-deleted records.
     * If the record is soft-deleted, it will be permanently deleted using the `forceDelete` method.
     * If the record is not soft-deleted, it will be deleted using the `deleteOrFail` method.
     *
     * @param mixed $id The ID of the record to be deleted.
     * @return JsonResponse|RedirectResponse Returns a JSON response if the request expects JSON,
     *                                  otherwise, it returns a redirect response.
     * @throws \Throwable Throws an exception if the record cannot be deleted.
     */
    public function destroy($id): JsonResponse|RedirectResponse
    {
        // Retrieve the item from the database, even if it is soft-deleted.
        $item = $this->getItem($id, true);

        DB::beginTransaction();
        try {
            // Perform any necessary actions before deleting a record.
            // This method can be overridden in child classes to add custom logic before deleting a record.
            $this->prepareForDestroy($item);// If the record is soft-deleted, it will be permanently deleted using the `forceDelete` method.
            $item = $item->withoutEagerLoads();
            // If the record is not soft-deleted, it will be deleted using the `deleteOrFail` method.
            if ($item->deleted_at)
                $item->forceDelete();
            else
                $item->deleteOrFail();// If the request expects JSON, return a JSON response.
            // Otherwise, return a redirect response.
            DB::commit();
            if (Request::expectsJson())
                return ResponderFacade::setMessage(Lang::get('responder::messages.success_delete.status'))->respond();
            return Redirect::back()->with(Lang::get('responder::messages.success_delete'));
        } catch (HttpException|AuthorizationException|ModelNotFoundException $exception) {
            DB::rollBack();
            return ResponderFacade::setMessage($exception->getMessage())->setHttpCode($exception->getStatusCode())->respondError();
        } catch (\Exception $e) {
            DB::rollBack();
            return ResponderFacade::setMessage($e->getMessage())->respondError();

        }
    }

    /**
     * Performs any necessary actions before deleting a record.
     *
     * This function can be overridden in child classes to add custom logic before deleting a record.
     *
     * @param Model $item The record to be deleted.
     */
    protected function prepareForDestroy(Model $item): void
    {

    }
}
