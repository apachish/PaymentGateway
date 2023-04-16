<?php

namespace Armanbroker\Auth\app\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendVerifyCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $code;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,$code)
    {
        $this->code = $code;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('auth-passport::emails.users.verify-code');
    }
}
