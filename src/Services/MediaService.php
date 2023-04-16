<?php
namespace Apachish\Media\Services;
use Apachish\Media\Models\Media;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaService
{

    public static function deleteLogo($file,$type)
    {
        try {
            $accessToken = self::setTokenStorage();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
            ];
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'read_timeout' => 10,
                'connect_timeout' => 10,
                'headers' => $headers
            ]);
            $request = $client->request('POST', env("APP_URL_STORAGE") . 'api/media/delete/logo/' . $type, [
                'json' => [
                    'image' => $file,
                ]
            ]);
            return true;
        }catch (\Exception $exception){
            logger("delete",[$exception->getMessage()]);
            return false;

        }
    }

    public static function uploadFileLogo($file,$type,$olde_file= null)
    {
        try {
            $data['account_bank']['logo'] = "";
            $accessToken = self::setTokenStorage();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
            ];
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'read_timeout' => 10,
                'connect_timeout' => 10,
                'headers' => $headers
            ]);
            $image_path = $file->getPathname();
            $image_mime = $file->getmimeType();
            $image_org = $file->getClientOriginalName();
            $request = $client->request('POST', env("APP_URL_STORAGE") . 'api/media/store/logo/'.$type, [
                'multipart' => [
                    [
                        'name' => 'image',
                        'filename' => $image_org,
                        'Mime-Type' => $image_mime,
                        'contents' => fopen($image_path, 'r'),
                    ],
                    [
                        'name' => 'old_image',
                        'contents' => $olde_file,
                    ],

                ]
            ]);
            $responce = json_decode($request->getBody(), true);
            return data_get($responce,"data.image");
        }catch (Exception $exception){
            logger("upload file failed",[$exception->getMessage()]);
        }
        return  null;
    }

    private static function setTokenStorage()
    {

        $agent = new Agent();
        $slug = null;
        $platform = $agent->platform();
        $version_platform = $agent->version($platform);

        $browser = $agent->browser();
        $version_browser = $agent->version($browser);

        $type_descktop = $agent->isDesktop() ? 'descktop' : false;
        $type_mobile = $agent->isPhone() ? 'mobile' : false;
        $type_robot = $agent->isRobot() ? 'robot' : false;
        $type = null;
        if ($type_descktop)
            $type = $type_descktop;
        if ($type_mobile)
            $type = $type_mobile;
        if ($type_robot)
            $type = $type_robot;
        $slug = md5($browser . $version_browser . $type . $platform . $version_platform . request()->header('User-Agent'));
        $ip = request()->ip();
        $separator = env('SEPARATOR_TOKEN',";;");
        $payload = Str::random(50) . $separator .
            $slug . $separator .
            $ip . $separator .
            time() . $separator
        ;
        return openssl_encrypt($payload, env('CIPHER_METHOD_TOKEN'), env("TOKEN_STORAGE"), 0, env('INITIALIZATION_VECTOR_TOKEN'));
    }

    public static function getFileCustomer($path,$file,$type)
    {
        try {
            $accessToken = self::setTokenStorage();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
            ];
            logger("header",$headers);
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'read_timeout' => 10,
                'connect_timeout' => 10,
                'headers' => $headers
            ]);
            $request = $client->request('GET', env("APP_URL_STORAGE") . 'api/media/get/file/'.$type, [
                'json' => [
                    'path' => $path,
                    'file' => $file,
                ],
               ['stream' => true]
            ]);
//            $responce = json_decode($request->getBody(), true);

            $body =  $request->getBody()->getContents();
            $base64 = base64_encode($body);
            $mime_type =  'text/xml';
            if(str_contains(strtolower($file),".jpg") || str_contains(strtolower($file),".jpeg")){
                $mime_type = "image/jpeg";

            }
            elseif(str_contains(strtolower($file),".png")){
                $mime_type = "image/png";

            }elseif(str_contains(strtolower($file),".svg")){
                $mime_type = "image/svg+xml";

            }elseif(str_contains(strtolower($file),".tiff")){
                $mime_type = "image/tiff";

            }          elseif(str_contains(strtolower($file),".gif")){
                $mime_type = "image/gif";

            }
            elseif(str_contains(strtolower($file),".pdf")){
                $mime_type = "application/pdf";
            }
            $img = ('data:' . $mime_type . ';base64,' . $base64);
            if($mime_type == "application/pdf")
                return "<embed src=$img alt='ok' />";
            else
                return "<img src=$img alt='ok' />";
              $body =  $request->getBody();
            $response = new StreamedResponse(function() use ($body) {
                while (!$body->eof()) {
                    echo $body->read(1024);
                }
            });


            $response->headers->set('Content-Type',$mime_type);

            return $response;
        }catch (Exception $exception){
            logger("upload file failed",[$exception->getMessage()]);
        }
        return  null;
    }

    public static function uploadFileStorage($file,$type,$user)
    {
        try {
            $data['account_bank']['logo'] = "";
            $accessToken = self::setTokenStorage();
            $headers = [
                'Authorization' => 'Bearer ' . $accessToken,
            ];
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'read_timeout' => 10,
                'connect_timeout' => 10,
                'headers' => $headers
            ]);
            $image_path = $file->getPathname();
            $image_mime = $file->getmimeType();
            $image_org = $file->getClientOriginalName();
            $request = $client->request('POST', env("APP_URL_STORAGE") . 'api/media/upload/file/'.$type."/".$user->id."/".$user->national_code, [
                'multipart' => [
                    [
                        'name' => 'upload',
                        'filename' => $image_org,
                        'Mime-Type' => $image_mime,
                        'contents' => fopen($image_path, 'r'),
                    ],
                    [
                        'name' => 'type',
                        'contents' => $type,
                    ],

                ]
            ]);
            $responce = json_decode($request->getBody(), true);
            return data_get($responce,"data.document");
        }catch (Exception $exception){
            logger("upload file failed",[$exception->getMessage()]);
        }
        return  null;
    }

    
    public static function storeFileImage($title,$request_file,$file="images", $type,$old_file=null)
    {
        if ($old_file) {
            File::delete(public_path($file."/" . $type . "/" . $old_file));
        }

        $name_file = time() . $request_file->getClientOriginalName();
//        'type' => data_get($request,'project.logo')->getmimeType(),
//                'size' => data_get($request,'project.logo')->getSize(),
        $path = $file."/" .$type."/";
        if(in_array($request_file->getmimeType(),["image/svg+xml"]))
            $name_file = self::uploadFileSvg($title,$request_file,$path);
        else
            $name_file = self::uploadFileImage($title,$request_file,$path);

//        Storage::disk('public_storage')->putFileAs($file."/" .$type, $request_file, $name_file);
        logger("name file" . $name_file);
        $data = [
            "title"=>$title,
            "file" => $name_file,
            "type"=> pathinfo(public_path($file."/" . $type . "/" .$name_file), PATHINFO_EXTENSION),
            "size"=>filesize(public_path($file."/" . $type . "/" .$name_file)),
            "path"=>$file."/" . $type . "/",
        ];
        if($data && file_exists(public_path($file."/" . $type . "/" .$name_file)))
            return self::createMedia($data);
        return false;
    }

    protected static function createMedia($data)
    {
        $data["created_by"] = auth()->id();
        $media = Media::create($data);
        return $media;
    }


    private static function uploadFileSvg($title,$request_file,$path): string
    {

        $name = Str::slug($title).".". $request_file->getClientOriginalExtension();
        $request_file->storeAs($path,$name,"public_images");
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->load(public_path($path.$name));
        $svg = $dom->documentElement;

        if ( ! $svg->hasAttribute('viewBox') ) { // viewBox is needed to establish
            // userspace coordinates
            $pattern = '/^(\d*\.\d+|\d+)(px)?$/'; // positive number, px unit optional

            $interpretable =  preg_match( $pattern, $svg->getAttribute('width'), $width ) &&
                preg_match( $pattern, $svg->getAttribute('height'), $height );

            if ( $interpretable ) {
                $view_box = implode(' ', [0, 0, $width[0], $height[0]]);
                $svg->setAttribute('viewBox', $view_box);
            } else { // this gets sticky
                throw new \Exception("viewBox is dependent on environment");
            }
        }

        $svg->setAttribute('width', '142');
        $svg->setAttribute('height', '136');
        $dom->save(public_path($path.$name));

        return $name;

    }

    private static function uploadFileImage($title,$request_file,$path): string
    {
//
//
//            $token = ;
//            $name = $token.'.jpg';
//            $img = Image::make($this->image)->encode('jpg')->resize(100, null, function ($constraint) {
//                $constraint->aspectRatio();
//            });
//            $img->stream();
//
//            Storage::disk('public')->put('users/'.$name, $img);

        $name = Str::slug($title).'_'.time() .".". $request_file->getClientOriginalExtension();

        //$this->upload->storeAs("articles",$name,"public_images");

        $image = Image::make($request_file);
        $width = 463;
        $height = 263;
        $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        $image->save(public_path($path . $name));

        return $name;

    }
    
}