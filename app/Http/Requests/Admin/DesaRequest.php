<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DesaRequest extends FormRequest
{
    protected $redirectRoute = 'desa.create';

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
            'kota_id'         => 'required',
            'kecamatan_id'         => 'required',
            'desa_name'         => 'required|min:1',
        ];
    }

    public function messages()
    {
        return [
            'provinsi_id.required' => 'Nama Provinsi Harus diisi',
            'kota_id.required' => 'Nama Kota Harus diisi',
            'kecamatan_id.required' => 'Nama Kecamatan Harus diisi',
            'desa_name.required' => 'Nama Desa Harus diisi',
        ];
    }
}
