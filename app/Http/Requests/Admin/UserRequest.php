<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $rule = [];
        if ($this->method() == 'POST') {
            $rule['_token_user']   = 'sometimes';
            $rule['name']          = 'required';
            $rule['email']         = 'required|email|unique:users,email,NULL,id,deleted_at,NULL';
            $rule['password']      = 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/'; // harus strong password';
            $rule['role']          = 'required';
            $rule['ktp']           = 'nullable';
            $rule['jenis_kelamin'] = 'required_if:role,4';
            $rule['tanggal_lahir'] = 'required_if:role,4';
            $rule['tempat_lahir']  = 'required_if:role,4';
            $rule['alamat']        = 'required_if:role,4';
            $rule['nomor_telepon'] = 'required_if:role,4|numeric|digits_between:10,13';
            $rule['ktp']           = 'nullable';
            if ($this->request->get('role') == 4) {
                $rule['ktp'] = 'required|image|mimes:png,jpg,jpeg|max:2048';
            }
            $rule['avatar'] = 'nullable';
            if ($this->request->get('avatar') != 'undifined') {
                $rule['avatar'] = 'required|image|mimes:png,jpg,jpeg|max:2048';
            }
        }

        if ($this->request->get('update')) {
            $rule['name']        = 'required';
            $rule['_token_user'] = 'required';
            $rule['email']       = [
                'required',
                Rule::unique('users')->where(function ($query) {
                    return $query->where(DB::Raw("md5(concat(id, '-', date_format(curdate(), '%Y%m%d')))"), '<>', $this->request->get('_token_user'));
                }),
            ];
            $rule['password'] = 'nullable';
            if ($this->request->get('password') != null) {
                $rule['password'] = 'required|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/'; // harus strong password';
            }
            $rule['role']          = 'required';
            $rule['ktp']           = 'nullable';
            $rule['jenis_kelamin'] = 'required_if:role,4';
            $rule['tanggal_lahir'] = 'required_if:role,4';
            $rule['tempat_lahir']  = 'required_if:role,4';
            $rule['alamat']        = 'required_if:role,4';
            $rule['nomor_telepon'] = 'required_if:role,4|numeric|digits_between:10,13';
            if ($this->request->get('role') == 4) {
                if ($this->request->get('ktp') != 'undifined') {
                    $rule['ktp'] = 'required|image|mimes:png,jpg,jpe|max:2048';
                }
            }
            $rule['avatar'] = 'nullable';
            if ($this->request->get('avatar') != 'undifined') {
                $rule['avatar'] = 'required|image|mimes:png,jpg,jpeg|max:2048';
            }
        }

        return $rule;
    }

    //attributes
    public function attributes()
    {
        return [
            'name'          => 'Nama',
            'email'         => 'Email',
            'password'      => 'Password',
            'role'          => 'Role',
            'ktp'           => 'KTP',
            'avatar'        => 'Avatar',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tanggal_lahir' => 'Tanggal Lahir',
            'tempat_lahir'  => 'Tempat Lahir',
            'alamat'        => 'Alamat',
        ];
    }

    //messages

    public function messages()
    {
        return [
            'name.required'     => 'Name wajib diisi',
            'email.required'    => 'Email wajib diisi',
            'email.email'       => 'Format Email salah',
            'email.unique'      => 'Email is sudah ada',
            'password.required' => 'Password wajib diisi',
            'password.min'      => 'Password paling sedikit 8 karakter',
            'password.regex'    => 'Harus mengandung setidaknya satu angka, satu character dan satu huruf besar dan kecil',
            'role.required'        => 'Role wajib diisi',
            'ktp.required_if'      => 'KTP wajib diisi',
            'required'             => ':attribute harus diisi',
            'required_if'          => ':attribute harus diisi',
            'email'                => ':attribute harus berupa email',
            'unique'               => ':attribute sudah terdaftar',
            'min'                  => ':attribute minimal :min karakter',
            'image'                => ':attribute harus berupa gambar',
            'mimes'                => ' :attribute harus berupa gambar dengan format jpg, png, jpeg',
            'max'                  => ':attribute maksimal 2Mb',
        ];
    }
}
