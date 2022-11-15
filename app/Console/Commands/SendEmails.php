<?php

namespace App\Console\Commands;
use App\Models\User;
use App\Models\Task;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Mail\Mailer;
use App\Mail\sendemail;

class SendEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send task reminder email daily to all users ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tasks = Task::all();
        foreach ($tasks as $task) {
            if($task->status!= "Completed")
            {
                $user = User::findorfail($task->assigned_to);

                Mail::to($user->email)
                    ->send(new sendemail($task));
            }
        }
        $this->info('Reminder Email sent to All Users');
    }
}
