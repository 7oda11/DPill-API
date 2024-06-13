<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class ImageInteractionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'img1' => 'required|image|mimes:jpeg,png,jpg',
            'img2' => 'required|image|mimes:jpeg,png,jpg',
        ];
    }


    // protected function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json([
    //         'errors' => $validator->errors(),
    //     ], 422));
    // }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->toArray();
        $firstErrorMessage = reset($errors)[0];

        $customError = [
            'errorMessage' => $firstErrorMessage,
            "statusCode" => 422,
        ];

        throw new HttpResponseException(response()->json($customError, 422));
    }
}