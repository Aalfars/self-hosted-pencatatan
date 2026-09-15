<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sesuaikan jika sudah ada sistem auth/permission
    }

    public function rules(): array
    {
        return [
            'type'        => ['required', 'in:pemasukan,pengeluaran'],
            'category'    => ['required', 'string', 'max:100'],
            'title'       => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'date'        => ['required', 'date'],
            'image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Kategori wajib diisi (pilih dari daftar atau ketik baru).',
            'amount.min'         => 'Jumlah tidak boleh negatif.',
            'image.max'          => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
