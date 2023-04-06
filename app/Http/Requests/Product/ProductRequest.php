<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'number' => 'required',
            'stock' => 'required',
            'production_date' => 'required',
            'expired_date' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama tidak boleh kosong',
            'number.required' => 'Nomor tidak boleh kosong',
            'stock.required' => 'Stok tidak boleh kosong',
            'production_date.required' => 'Tanggal produksi tidak boleh kosong',
            'expired_date.required' => 'Tanggal kadaluarsa tidak boleh kosong',
        ];
    }
}
