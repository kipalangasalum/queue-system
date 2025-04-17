<?php

namespace App\Http\Controllers;

use App\Imports\EmployeesImport;
use App\Jobs\SendEmployeeEmail;
use App\Models\Employee;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        try {
            $import = new EmployeesImport();
            Excel::import($import, $request->file('excel_file'));
            $importedEmployees = Employee::where('created_at', '>=', now()->subSeconds(5))->get();
            foreach ($importedEmployees as $index => $employee) {
                SendEmployeeEmail::dispatch($employee)
                    ->delay(now()->addSeconds($index + 1));
            }
            return redirect()->route('employees.index')
                ->with('success', "Successfully imported {$importedEmployees->count()} employees and queued emails.");
        } catch (ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }
            return redirect()->route('employees.create')
                ->with('error', 'Import failed: ' . implode(' | ', $errorMessages));
        } catch (\Exception $e) {
            return redirect()->route('employees.create')
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email,' . $employee->id,
            'message' => 'required|string',
        ]);

        $employee->update([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
