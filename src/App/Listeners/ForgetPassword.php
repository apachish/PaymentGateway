<?php

namespace Apachish\Auth\App\Listeners;


use App\Models\User;
use Armanbroker\Auth\app\Events\UserForgetPassword;
use Armanbroker\Auth\app\Mail\SendResetEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Armanbroker\Auth\Traits\Helper;

class ForgetPassword
{
    use Helper;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ProductUpdated $event
     * @return void
     */
    public function handle(UserForgetPassword $event)
    {
        $user = User::find($event->user_id);

        if ($event->type == 'email') {
            Mail::to($user)->send(new SendResetEmail($user, $event->token));
        } elseif ($event->type == 'mobile') {
            $this->send_sms($user, $event->token, 'forgetPass');
        }


    }
}
