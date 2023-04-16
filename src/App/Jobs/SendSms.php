<?php

namespace Apachish\Auth\App\Jobs;

use Apachish\Auth\Models\Code;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kavenegar\KavenegarApi;

class SendSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $receptor;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($receptor)
    {
        $this->receptor = $receptor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $apiKey = env("APIKEY");

            $client = new KavenegarApi($apiKey);
//                env("SENDER"),          // originator
            $message = $this->createCode();
            if($message ==null) return null;
            $result = $client->VerifyLookup(
                $this->receptor,    // recipients
                $message // message
                ,null,null,"Verify","sms"
            );
            if($result){
                foreach($result as $r){
                    logger("messageid = $r->messageid");
                    logger("message = $r->message");
                    logger("status = $r->status");
                    logger("statustext = $r->statustext");
                    logger("sender = $r->sender");
                    logger("receptor = $r->receptor");
                    logger("date = $r->date");
                    logger("cost = $r->cost");
                }
            }


        } catch (\Exception $e) { // http error
            Log::warning($e->getMessage()); // get stringified error
            Log::warning("code:".$e->getCode());
        }
    }

    private function createCode()
    {
        $code = null;
        $verify = Code::where('mobile_email',$this->receptor)->where("verify","0")->first();
        if($verify)
        {
           $code = $verify->code;
        }else{
            $code = rand(100000,999999);
            $verify = Code::updateOrCreate([
                'mobile_email'=>$this->receptor,
            ],[
                'code'=>$code,
                'mobile_email'=>$this->receptor,
                "verify"=>0
            ]);
        }
        return $code;
    }

}
