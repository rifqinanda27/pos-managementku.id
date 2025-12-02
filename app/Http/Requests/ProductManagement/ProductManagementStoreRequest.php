<?php

namespace App\Http\Requests\ProductManagement;

use Illuminate\Foundation\Http\FormRequest;

class ProductManagementStoreRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return in_array($this->user()->role, ['super-admin', 'admin']);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'name' => ['required', 'string', 'max:255'],
			'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
			'starting_stock' => ['nullable', 'integer', 'min:0'],
		];
	}
}
