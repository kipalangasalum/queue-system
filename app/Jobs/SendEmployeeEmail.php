<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\EmployeeMessage;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;

class SendEmployeeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function handle()
    {
        try {
            Mail::to($this->employee->email)->send(new EmployeeMessage($this->employee));
            $this->employee->update(['sent' => true]);
        } catch (\Exception $e) {
            \Log::error("Failed to send email to {$this->employee->email}: {$e->getMessage()}");
        }
    }
}
