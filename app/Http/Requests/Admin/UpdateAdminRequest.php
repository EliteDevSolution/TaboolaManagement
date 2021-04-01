<?php

namespace DLW\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        if($this->route('admin')->id === 1)
        {
            return [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:admins,email,'.$this->route('admin')->id,
                'view_id' => 'required|min:3',
                'avatar' => '|image|mimes:jpeg,png,jpg,gif|max:10000|dimensions:max_width=2000,max_height=2000'
            ];
        } else {
            return [
                'name' => 'required|max:255',
                'email' => 'required|email|unique:admins,email,'.$this->route('admin')->id,
                'avatar' => '|image|mimes:jpeg,png,jpg,gif|max:10000|dimensions:max_width=2000,max_height=2000'
            ];
        }

    }
}
