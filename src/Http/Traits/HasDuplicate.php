<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithValidation;

/**
 * A trait that provides functionality for duplicating existing records in the database.
 */
trait HasDuplicate
{
    use WithModel, WithValidation;

    /**
     * Duplicates an existing record in the database.
     *
     * This method takes an incoming request object and the unique identifier of the record to be duplicated.
     * It returns a JSON response to be sent back to the client.
     * If any errors occur during the duplication process, it throws a Throwable exception.
     *
     * @param Request $request The incoming request object.
     * @param int $id The unique identifier of the record to be duplicated.
     * @return JsonResponse The response to be sent back to the client.
     * @throws \Throwable
     */
    public function duplicate(Request $request, int $id): JsonResponse
    {
        // Retrieve the record to be duplicated from the database.
        // This is done by calling the getItem method, which is assumed to be implemented elsewhere.
        $original = $this->getItem($id);

        // Get the validated data from the incoming request.
        // This is done by calling the getValidationData method, which is assumed to be implemented elsewhere.
        $data = $this->when($this->duplicateRequestClass(), fn() => $this->setRequestClass($this->duplicateRequestClass()))->getValidationData($request);

        // Begin a database transaction to ensure atomicity.
        // This means that either all changes will be committed, or none will be, in case of an error.
        DB::beginTransaction();

        try {
            // Call the prepareForDuplicate method to perform any necessary actions before duplication.
            // This method is assumed to be implemented elsewhere and can be used to perform custom actions.
            $this->prepareForDuplicate($request, $original);

            // Replicate the original record and fill it with the validated data.
            // This creates a new instance of the original record with the same data.
            $duplicated = $original->replicate()->fill($data);

            // Save the duplicated record to the database.
            // This commits the changes to the database.
            $duplicated->save();

            // Call the duplicateCallback method to perform any additional actions after duplication.
            // This method is assumed to be implemented elsewhere and can be used to perform custom actions.
            $this->duplicateCallback($request, $original, $duplicated);

            // Commit the database transaction if no errors occurred.
            // This confirms that all changes have been successfully committed to the database.
            DB::commit();

            // Return a successful response with the duplicated record.
            // This response is sent back to the client to indicate that the duplication was successful.
            return ResponderFacade::setData($duplicated)->setMessage(trans('responder::messages.success_duplicate.status'))->respond();
        } catch (\Exception $e) {
            // Rollback the database transaction in case of an error.
            // This reverts all changes made during the transaction, ensuring that the database remains in a consistent state.
            DB::rollBack();

            // Return an error response with the exception message.
            // This response is sent back to the client to indicate that an error occurred during the duplication process.
            return ResponderFacade::setExceptionMessage($e->getMessage())->setMessage($e->getMessage())->respondError();
        }
    }

    /**
     * A callback method that can be overridden to perform additional actions after a record has been duplicated.
     *
     * This method is triggered after a record has been successfully duplicated and the database transaction has been committed.
     * It provides an opportunity to perform any necessary actions, such as sending notifications or updating related records.
     *
     * @param Request $request The incoming request object.
     * @param Model $original The original model instance that was duplicated.
     * @param Model $duplicated The duplicated model instance.
     * @return void
     */
    protected function duplicateCallback(Request $request, Model $original, Model $duplicated): void
    {
        // Perform any necessary actions after duplication, such as:
        // - Sending notifications to users or administrators
        // - Updating related records or statistics
        // - Triggering additional workflows or processes
    }

    /**
     * A method that can be overridden to perform any necessary actions before duplicating a record.
     *
     * This method is called before the duplication process begins and can be used to perform custom actions.
     *
     * @param Request $request The incoming request object.
     * @param Model $original The original model instance that is being duplicated.
     * @return void
     */
    protected function prepareForDuplicate(Request &$request, Model $original)
    {
        // Perform any necessary actions before duplication, such as:
        // - Validating the request data
        // - Updating related records or statistics
        // - Triggering additional workflows or processes
    }

    protected function duplicateRequestClass(): ?FormRequest
    {
        // Return the request class for duplicating a record.
        // This is used to validate the incoming request data.
        // If the request class is not set, it returns null.
        return null;
    }
}
