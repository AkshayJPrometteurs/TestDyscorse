<?php

namespace App\Http\Requests\MobileApp;

use Illuminate\Foundation\Http\FormRequest;

class CustomTaskCreate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required',
            'date' => '',
            'assign_task_type' => 'required',
            // 'day' => 'required',
            'wake_up_time' => 'required',
            'sleep_time' => 'required',
            'task_name' => 'required',
            'task_status' => '',
        ];
    }
}
