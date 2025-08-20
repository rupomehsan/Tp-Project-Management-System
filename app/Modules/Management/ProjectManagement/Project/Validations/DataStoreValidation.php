<?php

namespace App\Modules\Management\ProjectManagement\Project\Validations;

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
            'project_group_id' => 'required | sometimes',
            'name' => 'required | sometimes',
            'description' => 'sometimes',
            'start_date' => 'sometimes',
            'end_date' => 'sometimes',
            'project_link' => 'sometimes',
            'project_agrement_file' => 'sometimes',
            'project_document' => 'sometimes',
            'project_progress' => 'sometimes',
            'project_status' => [
                'sometimes',
                Rule::in(['Not Started', 'In Progress', 'Completed', 'On Hold', 'Upcoming']),
            ],
            'delivery_status' => [
                'sometimes',
                Rule::in(['Yet to meeting', 'Testing Phase', 'Successfully Delivered', 'Delivery process not started']),
            ],
            'client_rating' => 'sometimes',

            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}
