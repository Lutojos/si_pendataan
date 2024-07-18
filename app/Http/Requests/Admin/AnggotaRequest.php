<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AnggotaRequest extends FormRequest
{
    protected $redirectRoute = 'anggota.create';

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
            'name'         => 'required',
            'umur'         => 'required',
            'gender'         => 'required',
            'address'         => 'required',
            'provinsi_id'         => 'required',
            'kota_id'         => 'required',
            'kecamatan_id'         => 'required',
            'desa_id'         => 'required',
            'phone_number'         => 'required',
            'image_path'         => '',
            'latitude'         => 'required',
            'longitude'         => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name'         => 'Nama Harus diisi',
            'umur'         => 'Umur Harus diisi',
            'gender'         => 'Jenis Kelamin Harus diisi',
            'address'         => 'Alamat Harus diisi',
            'provinsi_id'         => 'Provinsi Harus diisi',
            'kota_id'         => 'Kota Harus diisi',
            'kecamatan_id'         => 'Kecamatan Harus diisi',
            'desa_id'         => 'Desa Harus diisi',
            'phone_number'         => 'Nomor Telepon Harus diisi',
            'image_path'         => 'Gambar Harus diisi',
            'latitude'         => 'Latitude Harus diisi',
            'longitude'         => 'Longitude Harus diisi',
        ];
    }
}
