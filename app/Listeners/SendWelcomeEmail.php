<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserEmail;
class SendWelcomeEmail
{
    /**
     * Create the event listener.
     */
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegistered $event)
    {
        // Mail::to('rahulvermat006@gmail.com')->send();
        $user = $event->user['user'];

        // return $user->name;
        // Mail::raw('123', function ($message) {
        //     $message->to('rahulvermat006@gmail.com')
        //             ->subject('Your Subject');
        // });

        // Mail::send('emails.simple', ['message' => '123'], function ($message) {
            //     $message->to('rahulvermat006@gmail.com')
            //             ->subject('Your Subject');
            // });

        try {
            // Mail::raw($event->name, function ($message) {
            //     $message->to('rahulvermat006@gmail.com')
            //             ->subject('Test');
            // });
            $message = $user->name.' registration done check details';
            $subject = $user->name.' Registration';
            // return gettype($message).gettype($subject);
            $details = [
                'name'=> $user->name,
                'email'=>$user->email,
                'phone'=>$user->phone_number,
                'role'=>$user->role_id,
                'uuid'=>$user->uuid,
            ];

            Mail::to('rahulvermat006@gmail.com')->send(new NewUserEmail($message,$subject,$details));
          
          } catch (\Exception $e) {
          
              return $e->getMessage();
          }

        
    }
}
