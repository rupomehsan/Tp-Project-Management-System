<?php

namespace App\Modules\Management\ProjectManagement\UpcommingProject\Validations;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DataStoreValidation extends FormRequest
{
    /**
     * 
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
            'project_group_id' => 'required',
            'name' => 'required | sometimes',
            'description' => 'required | sometimes',
            'start_date' => 'required | sometimes',
            'end_date' => 'required | sometimes',
            'project_link' => 'required | sometimes',
            'project_agrement_file' => 'required | sometimes',
            'project_document' => 'required | sometimes',
            'project_progress' => 'required | sometimes',
            'project_status' => [
                'sometimes',
                Rule::in(['Not Started', 'In Progress', 'Completed', 'On Hold']),
            ],
            'delivery_status' => [
                'sometimes',
                Rule::in(['Yet to meeting', 'Testing Phase', 'Successfully Delivered', 'Delivery process not started']),
            ],
            'client_rating' => 'required | sometimes',

            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}
