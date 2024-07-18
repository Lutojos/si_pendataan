<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(_400($validator->errors()));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $user_id = "";
        if (Auth::user()) {
            $user_id = ',' . Auth::user()->id;
        }

        return [
            'name'         => 'required|min:3|max:100',
            'email'        => $user_id ? 'required|email|max:255|unique:users,email' . $user_id : 'required|email|max:255|unique:users,email',
            'password'     => 'sometimes|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/',
            'placeofbirth' => 'required|min:3|max:50',
            'dateofbirth'  => 'required|date',
            'address'      => 'required|min:3',
            'gender'       => 'required|integer',
            'phone_number' => 'string|max:15',
            'ktp'          => !Auth::check() ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : 'sometimes|image|mimes:jpeg,png,jpg.webp|max:2048',
            'avatar'       => $this->request->get('avatar') ? 'required|image|mimes:jpeg,png,jpg,webp|max:2048' : 'sometimes|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'password.min'   => 'Password paling sedikit 8 karakter',
            'password.regex' => 'Harus mengandung setidaknya satu angka, satu character dan satu huruf besar dan kecil',
            'ktp.mimes'      => 'File harus bertipe jpeg,png,jpg,webp',
            'ktp.max'        => 'Ukuran file terlalu besar max 2Mb',
            'avatar.mimes'   => 'File harus bertipe jpeg,png,jpg,webp',
            'avatar.max'     => 'Ukuran file terlalu besar max 2Mb',
        ];
    }
}
