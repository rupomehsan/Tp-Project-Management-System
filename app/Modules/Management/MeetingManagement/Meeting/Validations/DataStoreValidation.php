<?php

namespace App\Modules\Management\MeetingManagement\Meeting\Validations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DataStoreValidation extends FormRequest
{
    /**
     * Determine if the  is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }
    /**
     * validateError to make this request.
     */
    public function validateError($data)
    {
        $errorPayload =  $data->getMessages();
        return response(['status' => 'validation_error', 'errors' => $errorPayload], 422);
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException($this->validateError($validator->errors()));
        if ($this->wantsJson() || $this->ajax()) {
            throw new HttpResponseException($this->validateError($validator->errors()));
        }
        parent::failedValidation($validator);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'group_id' => 'required | sometimes',
            'title' => 'required | sometimes',
            'date' => 'required | sometimes',
            'meeting_type'  => ['sometimes', Rule::in(['Offline', 'Online', 'Hybrid'])],
            'meeting_link' => [
                'sometimes',
                function ($attribute, $value, $fail) {
                    if ($this->input('meeting_type') === 'Online' && empty($value)) {
                        $fail('The meeting link field is required when meeting type is Online.');
                    }
                }
            ],
            'description' => 'required | sometimes',
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}