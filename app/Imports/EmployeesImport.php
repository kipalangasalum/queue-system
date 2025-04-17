<?php

// app/Imports/EmployeesImport.php
namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class EmployeesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new Employee([
            'name' => $row['name'],
            'email' => $row['email'],
            'message' => $row['message'],
            'sent' => false,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('employees', 'email')],
            'message' => ['required', 'string'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'The name field is required in the Excel file.',
            'email.required' => 'The email field is required in the Excel file.',
            'email.email' => 'The email field must be a valid email address.',
            'email.unique' => 'The email :input already exists in the database.',
            'message.required' => 'The message field is required in the Excel file.',
        ];
    }
}
