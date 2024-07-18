<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class KotaRequest extends FormRequest
{
    protected $redirectRoute = 'kota.create';

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
            'provinsi_id'         => 'required',
            'kota_name'         => 'required|min:1',
        ];
    }

    public function messages()
    {
        return [
            'provinsi_id.required' => 'Nama Provinsi Harus diisi',
            'kota_name.required' => 'Nama Kota Harus diisi',
        ];
    }
}
