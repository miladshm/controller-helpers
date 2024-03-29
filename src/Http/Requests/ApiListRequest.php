<?php

namespace Miladshm\ControllerHelpers\Http\Requests;

use Illuminate\Validation\Rule;

class ApiListRequest extends ApiFormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string'],
            'searchable' => ['nullable', 'array'],
            'searchable.*' => ['nullable','required_with:searchable','string'],
            'sort' => ['nullable', 'array'],
            'sort.column' => ['nullable', 'required_with:sort', 'string'],
            'sort.dir' => ['nullable', Rule::in(['asc','desc'])],
            'page' => ['nullable', 'integer'],
            'pageLength' => ['nullable', 'integer'],
        ];
    }
}
