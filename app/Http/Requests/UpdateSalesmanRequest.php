<?php

namespace App\Http\Requests;

use App\Models\Salesman;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesmanRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() {

        return [
            'first_name'      => 'string|max:255',
            'last_name'       => 'string|max:255',
            'titles_before'   => 'array|min:0|max:10',
            'titles_before.*' => 'string|distinct|in:' . Salesman::getTitlesBeforeList(),
            'titles_after'    => 'array|min:0|max:10',
            'titles_after.*'  => 'string|distinct|in:' . Salesman::getTitlesAfterList(),
            'prosight_id'     => 'integer|digits:5',
            'email'           => 'email',
            'phone'           => 'string',
            'gender'          => 'string|in:' . Salesman::getGendersList(),
            'marital_status'  => 'string|in:' . Salesman::getMaritalStatusesList()
        ];
    }

    /**
     * @param $validator
     * @return void
     */
    protected function failedValidation($validator) {
        //raw input, to pass posted value to error message
        $input = $this->all();
        \App\Helpers\ValidationRequestHelper::generateFailedValidationResponse($input, $validator->failed(), $this->expectsJson());
    }

}
