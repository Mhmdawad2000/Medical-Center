<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterDoctorRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [

            'fname' => ['required', 'string'],
            'lname' => ['required', 'string'],
            'birthday' => ['required', 'string'],
            'phone' => ['required', 'numeric'],
            'gendere' => ['required', 'string'],
            'address' => ['required', 'string'],
            'nationality' => ['required', 'string'],
            'password' => ['required', 'string'],
            'national_id' => ['required', 'numeric', 'unique:users,national_id'],
            'email' => ['required', 'email', 'unique:users,email'],
            'notes' => ['nullable', 'string'],
            'hire_date' => ['required', 'string'],
            'specialization_id' => ['required', 'exists:specializations,id'],
            'degree' => ['required', 'string'],
            'state' => ['required', 'string'],

        ];
    }
}
