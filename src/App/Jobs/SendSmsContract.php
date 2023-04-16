<?php

namespace Armanbroker\Auth\App\Jobs;


use Armanbroker\Auth\Models\ContractVerify;
use ArmanTadbir\Awards\Models\AwardRequestMessage;
use ArmanTadbir\Magfa\SMS;
use ArmanTadbir\Magfa\SMSNH1;
use ArmanTadbir\Notification\Models\Notification;
use ArmanTadbir\ShortUrl\Models\ShortUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;

class SendSmsContract implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $message;
    protected $user_id;
    protected $request_id;
    protected $send_by;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::find($this->user_id);
        if ($user == null) return false;
        $old_send = ContractVerify::where("user_id", $user->id)->first();
        $code = $old_send && $old_send->verify == false ? $old_send->code : rand(100000, 999999);

        $messages = " مشتری گرامی کد ارسالی جهت امضا قرارداد" . "\r\n";
        $messages .= $user->getFullName() . "\r\n";
        $messages .= "می باشد" . "\r\n";
        $messages .= "کد ارسال شده را وارد نمایید" . "\r\n";
        $messages .= "کد: " . $code . "\r\n";
        $messages .= "کارگزاری آرمان تدبیر نقش جهان";
        $messages .= "\r\n";
        $result = [];
        if (env("SMSPANEL") == "magfa") {
            $sms = new SMS();
            $result = $sms->enqueueSample([$user->mobile], $messages);
        } elseif (env("SMSPANEL") == "nh1") {
            $sms = new SMSNH1();
            $result = $sms->enqueueSample($user->mobile, $messages);
        }

        if($old_send && $old_send->verify ){
            $old_send->code = $code;
            $old_send->update();
        }elseif ($old_send == null)
            ContractVerify::create([
                'user_id' => $user->id,
                'verify' => false,
                'code' => $code
            ]);


    }
}
