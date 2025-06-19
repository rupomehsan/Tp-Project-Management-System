<?php

namespace App\Modules\Management\TasksManagement\Tasks\Validations;

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
            'project_id' => 'required',
            'title' => 'required | sometimes',
            'description' => 'required | sometimes',
            'system_loss' => 'required | sometimes',
            'assigned_to' => 'required | sometimes',
            'start_date' => 'required | sometimes',
            'end_date' => 'required | sometimes',
            'task_status'  => ['sometimes', Rule::in(['Pending', 'In Progress', 'Completed'])],
            'priority'     => ['sometimes', Rule::in(['low', 'normal', 'high', 'urgent'])],

            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }
}