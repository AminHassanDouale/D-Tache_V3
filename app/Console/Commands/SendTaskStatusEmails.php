<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Mail\TaskStatus;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTaskStatusEmails extends Command
{
    protected $signature = 'schedule:send-task-emails';

    protected $description = 'Send task status emails to assigned users.';

    public function handle()
    {
        $tasks = Task::where('status_id', '!=', 5)->get()->groupBy('assigned_id');

        foreach ($tasks as $assignedId => $userTasks) {
            $user = User::find($assignedId);
            if ($user) {
                Mail::to($user->email)->send(new TaskStatus($userTasks));
            }
        }
        
        $this->info('Task status emails sent successfully.');
    }
}