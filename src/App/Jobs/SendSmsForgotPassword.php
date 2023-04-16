<?php

namespace Armanbroker\Auth\App\Jobs;

use App\Models\User;
use ArmanTadbir\Magfa\SMSNH1;
use ArmanTadbir\ShortUrl\Models\ShortUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Password;
use Jenssegers\Optimus\Optimus;
use ArmanTadbir\Magfa\SMS;


class SendSmsForgotPassword implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attribute;
    protected $email_mobile_national_code;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($attribute,$email_mobile_national_code)
    {
        $this->attribute = $attribute;
        $this->email_mobile_national_code = $email_mobile_national_code;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = User::where($this->attribute,$this->email_mobile_national_code)->first();
        if($user == null ) return false;
        $token = Password::createToken($user);
        $user_id = app(Optimus::class)->encode($user->id);
        $url = 'https://auth.armanbroker.ir/reset-password?token='.$token."&refer=".$user_id;

//        $url = url(route('password.reset', [
//            'token' => $token,
//            'email' => $user->email,
//        ], false));
        $slug = time().$user_id;
        ShortUrl::create(["slug"=>$slug,'title'=>'فراموشی رمز عبور '.$user->getFullName(),  'url'=>$url]);
        $text = " تنظیم مجدد  گذرواژه "."\n\r";
        $text .= " با کیلیک بر روی لینک موقت زیر گذر واژه خود را بازیابی کنید: "."\n\r";
        $text .= " لینک :  ". ShortUrl::SHORT_URL.$slug."\n\r";
//        $text .= " لینک :  ". $url."\n\r";
        $text .= config('app.name');
        if (env("SMSPANEL") == "magfa") {
            $sms = new SMS();
            $result = $sms->enqueueSample([$user->mobile], $text);
        } elseif (env("SMSPANEL") == "nh1") {
            $sms = new SMSNH1();
            $result = $sms->enqueueSample($user->mobile, $text);
        }
    }
}
