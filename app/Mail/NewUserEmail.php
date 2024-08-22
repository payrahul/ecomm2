<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $message;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($message,$subject,$details)
    {
        $this->message = $message;
        $this->subject = $subject;
        $this->details = $details;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.new-user',
            with:[
                'name' => $this->details['name'],
                'email'=>$this->details['email'],
                'phone'=>$this->details['phone'],
                'role'=>$this->details['role'],
                'uuid'=>$this->details['uuid'],
                'subject'=>$this->subject,
                'message'=>$this->message,

                // 'name'=> $user->name,
                // 'email'=>$user->email,
                // 'Phone'=>$user->phone_number,
                // 'role'=>$user->role_id,
                // 'uuid'=>$user->uuid,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
