<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResouceRequest extends FormRequest
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
    public function rules()
    {
        return [
            'sort'          => 'string|nullable|in:ASC,DESC',
            'number_item'   => 'numeric|nullable|min:1',
            'start_date'    => 'date|nullable|required_with:end_date|before_or_equal:end_date',
            'end_date'      => 'date|nullable|required_with:start_date|after_or_equal:start_date',
        ];
    }
}
