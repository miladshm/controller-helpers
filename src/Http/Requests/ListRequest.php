<?php

namespace Miladshm\ControllerHelpers\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListRequest extends FormRequest
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
            getConfigNames('params.search') => ['nullable', 'string'],
            getConfigNames('params.searchable_columns') => ['nullable', 'array'],
            getConfigNames('params.sort') . ".*" => ['nullable', "required_with:" . getConfigNames('params.searchable_columns'), 'string'],
            getConfigNames('params.sort') => ['nullable', 'array'],
            getConfigNames('params.sort') . ".column" => ['nullable', "required_with:" . getConfigNames('params.sort'), 'string'],
            getConfigNames('params.sort') . ".dir" => ['nullable', Rule::in(['asc', 'desc'])],
            getConfigNames('page_number') => ['nullable', 'integer'],
            getConfigNames('page_length') => ['nullable', 'integer'],
            'all' => ['boolean']
        ];
    }


}
