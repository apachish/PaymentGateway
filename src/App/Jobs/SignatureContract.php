<?php

namespace Armanbroker\Auth\App\Jobs;


use Armanbroker\Additional\Models\Bank;
use Armanbroker\Additional\Models\FinancialBroker;
use Armanbroker\Additional\Models\Location;
use Armanbroker\Additional\Models\Work;
use Armanbroker\Auth\Models\ContractVerify;
use Armanbroker\Auth\Models\TbsLog;
use Armanbroker\Auth\Services\UserService;
use Armanbroker\Media\Models\Media;
use Armanbroker\Sejam\Models\BankAccountInformation;
use Armanbroker\Sejam\Models\Sejam;
use Armanbroker\Sejam\Models\ShareholderCode;
use Armanbroker\Sejam\Services\SejamService;
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
use Jenssegers\Optimus\Optimus;
//use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Barryvdh\DomPDF\Facade as PDF;


class SignatureContract implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $sejam_id;
    protected $in_person=false;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user_id,$sejam_id,$in_person=false)
    {
        $this->user_id = $user_id;
        $this->sejam_id = $sejam_id;
        $this->in_person = $in_person;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $signature = Media::where("user_id",$this->user_id)->where("type","signature")->first();
        $sejam= Sejam::find($this->sejam_id);
        $user = User::with(["identityInformation","contactInformation","shareholderCodes","financialInformation","jobInformation","bankAccountInformation"])->find($this->user_id);
        if($sejam == null) return false;
        if(!$this->in_person) {
            if ($signature == null || ($signature && !file_exists(storage_path("app/public/signature/" . $user->national_code . "/" . $signature->file)))) {
                $signature = SejamService::getSignature($sejam);
                if ($signature) {
                    $sejam->refresh();
                    if (env("SEJAMOTHERSERVER", false))
                        $result = SejamService::requestGetSignature($sejam);
                    else
                        $result = SejamService::getFileSignature($sejam);
                    if ($result) {
                        $signature = Media::where("user_id", $user->id)->where("type", "signature")->first();
                    }


                }
            }
            if (!$signature || !file_exists(storage_path("app/public/signature/" . $user->national_code . "/" . $signature->file)))
                return false;
        }
        list($user_id, $user) = $this->getInfoContract($user);
        $path = "app/public/files/contract/" . $user->national_code;
        makeDirectoryStorage($path);
        $archive =  TbsLog::with("user")->whereHasMorph("model",[User::class],function ($query) {
            $query->where('model_id',$this->user_id);
        })->whereNotNull("num_archive")->first();
        $expert = null;
        $num_archive = null;
        if($archive){
            $expert = $archive->user;
            $num_archive = $archive->num_archive;
        }
        $image1 = UserService::writeImageehrazWithSignitor($user, $path,$expert,$num_archive,$this->in_person);
        $image2 = UserService::writeImageehraz2WithSignitor($user, $path,$expert,$num_archive,$this->in_person);
        $image3 = UserService::writeImageOnlineWithSignitor($user, $path,$expert,$num_archive,$this->in_person);
        $image4 = UserService::writeImageOnline11WithSignitor($user, $path,$expert,$num_archive,$this->in_person);
        $image5 = UserService::writeImageOnline12WithSignitor($user, $path,$expert,$num_archive,$this->in_person);
        $image6 = UserService::writeImageOnline2WithSignitor($user, $path,$expert,$num_archive,$this->in_person);
        $image7 = UserService::writeImageInternetWithSignitor($user, $path,$expert,$num_archive,$this->in_person);
        $image8 = UserService::writeImageRiskWithSignitor($user, $path,$expert,$num_archive,$this->in_person);
        $image9 = UserService::writeImageCredite1WithSignitor($user, $path,$num_archive,$this->in_person);
        $image10 = UserService::writeImageCredite2WithSignitor($user, $path,$num_archive,$this->in_person);
        $image11 = UserService::writeImageCredite3WithSignitor($user, $path,$num_archive,$this->in_person);
        $image12 = UserService::writeImageCredite4WithSignitor($user, $path,$num_archive,$this->in_person);

        $images = [
            $image1,
            $image2,
            $image3,
            $image4,
            $image5,
            $image6,
            $image7,
            $image8,
            $image9,
            $image10,
            $image11,
            $image12,
        ];

//        $medias["12"]=$this->saveContract([$image1, $image2], $user_id, $path, $user,"12");
//        $medias["24"]=$this->saveContract([ $image4], $user_id, $path, $user,"24");
//        $medias["24_1"]=$this->saveContract([ $image5], $user_id, $path, $user,"24_1");
//        $medias["24_2"]=$this->saveContract([ $image6], $user_id, $path, $user,"24_2");
//        $medias["24_3"]=$this->saveContract([ $image7], $user_id, $path, $user,"24_3");
//        $medias["26"]=$this->saveContract([$image3], $user_id, $path, $user,"26");
//        $medias["77"]=$this->saveContract([$image8], $user_id, $path, $user,"77");
//        $medias["265_1"]=$this->saveContract([$image9], $user_id, $path, $user,"265_1");
//        $medias["265_2"]=$this->saveContract([$image10], $user_id, $path, $user,"265_2");
//        $medias["265_3"]=$this->saveContract([$image11], $user_id, $path, $user,"265_3");
//        $medias["265_4"]=$this->saveContract([$image12], $user_id, $path, $user,"265_4");
        $pdf_all =  [$image1, $image2, $image3, $image4, $image5, $image6, $image7, $image8];
        $this->saveContractPdf($pdf_all,$user_id,$path,$user);
        $contract = ["12" , "12_1" , "24" , "24_1" , "24_2" , "26" , "22" , "77" ,"265_1","265_2","265_3","265_4"];
        $customer = $user;
        $customer->id = $user->uid;
        unset($customer->uid);
    }

    public function getInfoContract($user): array
    {

        $countries = Location::withDepth()->having('depth', '=', 0)->get();
        $bank_provinces = Location::withDepth()->having('depth', '=', 1)->where('parent_id', 1)->get();
        $jobs = Work::all();
        $financialBrokers = FinancialBroker::all();
        $banks = Bank::all();

        if ($user && $user->contactInformation) {
            $country_id = $user->contactInformation->country_id;
            $provinces = Location::withDepth()->having('depth', '=', 1)->where('parent_id', $country_id)->get();
            $province_id = $user->contactInformation->province_id;
            $cites = Location::withDepth()->having('depth', '=', 2)->where('parent_id', $province_id)->get();
            $city_id = $user->contactInformation->city_id;
            $sections = Location::withDepth()->having('depth', '=', 3)->where('parent_id', $city_id)->get();
        }
        if ($user && $user->bankAccountInformation) {
            $province_id = $user->bankAccountInformation->province_id;
            $bank_cities = Location::withDepth()->having('depth', '=', 2)->where('parent_id', $province_id)->get();
            $user->bankAccountInformation->type_title = BankAccountInformation::type_view($user->bankAccountInformation->type);
        }
        if ($user->shareholderCodes) {
            $user->shareholderCodes->map(function ($shareholder_code) {
                $shareholder_code->stock_name_title = ShareholderCode::typeView($shareholder_code->stock_name);
            });
        }
        $user->uid = $user->id;
        $user->id = app(Optimus::class)->encode($user->id);
        return array($user->uid , $user);
    }



    public function saveContractPdf($images,$user_id, string $path, $user)
    {
        try {
            $customer = $user;
            $pdf = PDF::loadView('auth::users.contract_pdf', compact('images','customer'));
            $name =  $user_id . "_" . time() . '.pdf';
            $pdf->save(storage_path($path . "/" . $name));
            $media_old = Media::where("user_id",$user->uid)->where("type","contract")->first();
            if($media_old)
            {
                $path_old = storage_path("app/public/files/contract" . $user->national_code."/".$media_old->name);
                if(file_exists($path_old))
                    unlink($path_old);
                $media_old->delete();
            }

            Media::create([
                'title' => "قرارداد",
                'file' => $name,
                'type' => "contract",
                "status"=>"1",
                'size' => filesize($path . $name),
                'path' => str_ireplace("app/public/files","",$path)."/",
                "user_id" => $user->uid
            ]);
        }catch (\Exception $exception){
            Log::error("exption save contract",[
                $exception->getCode(),
                $exception->getMessage(),
                $exception->getLine()
            ]);
        }

    }
}
