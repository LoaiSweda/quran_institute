<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreInstituteRequest extends FormRequest
{
    public function authorize() { return auth()->user()->role->name === 'super admin'; }

    public function rules()
    {
        return [
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'image'   => 'nullable|image|max:2048',
            'user_id' => 'required|exists:users,id',

        ];
    }
}
