<?php

namespace DLW\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateFaqRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'question' => 'required',
            'category' => 'required',
            'answer' => 'required',
        ];
    }
}
