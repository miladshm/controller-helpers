<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithValidation;

trait HasExists
{
    use WithModel, WithValidation;

    /**
     * Checks if a record exists in the database based on the provided column and value.
     *
     * This method is used to check if a record exists in the database based on the provided column and value.
     * It uses the 'filters' method to apply any filters to the query, and then checks if a record with the given
     * column and value exists.
     *
     * @param Request $request The incoming request containing the column and value to check.
     *
     * @return \Illuminate\Http\JsonResponse The response containing the existence status of the record.
     */
    public function exists(Request $request)
    {
        // Get the validation rules for the 'value' and 'column' parameters.
        // The 'value' parameter should be required, and the 'column' parameter should be a string.
        $this->setRules($this->rules())->getValidationData($request);

        // Apply any filters to the query using the 'filters' method.
        // Then, check if a record with the given column and value exists.
        $exists = $this->query()
            ->where($request->string('column')->value(), $request->input('value', 'id'))
            ->exists();

        // Return a JSON response containing the existence status of the record.
        return ResponderFacade::setData(compact('exists'))->respond();
    }

    /**
     * Returns the validation rules for the 'exists' method.
     *
     * @return array The validation rules for the 'value' and 'column' parameters.
     */
    protected function rules(): array
    {
        return [
            'value' => ['required'],
            'column' => ['nullable', 'string']
        ];
    }
}
