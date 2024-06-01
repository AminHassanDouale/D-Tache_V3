<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): \Illuminate\Mail\Mailables\Envelope
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: 'Task Created Mail',
        );
    }

    public function build()
    {
        return $this->view('emails.task_created')
                    ->with([
                        'task' => $this->data['task'],
                        'assigned' => $this->data['assigned'],
                        'creator' => $this->data['creator'],
                        'project' => $this->data['project'],
                    ])
                    ->subject('New Task Created: '. $this->data['task']->name);
    }
}
