<?php

namespace Apachish\Apartment\App\Http\Controllers;

use App\Http\Resources\ImageCollection;
use App\Models\Image;
use App\Models\Project;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Jenssegers\Optimus\Optimus;

class ImagesController extends Controller
{
    public function gets(Request $request,Optimus $optimus,$project_id)
    {
        $token = $request->bearerToken();
        $project_id = cache()->get("project".$token);
        $project = Project::find($optimus->decode($project_id));
        $images = new Image();
        if($project) {
            $database_name = $project->database_name;
            if ($database_name) {
                $config = config("append_database." . $database_name);
                config(['database.connections.' . $database_name => $config]);
                $images->setConnection($database_name);
            }
        }
        $images = $images->whereStatus("1")->simplePaginate(6);
        $data = [
            'images' => (new ImageCollection($images))->setProject($project_id)
        ];
        return $this->responseData(self::SUCCESS, $data);
    }


    public function download(Request $request, Optimus $optimus,$project_id, $image_id)
    {
        $project = Project::find($optimus->decode($project_id));
        if($project ==  null) return $this->respondNotFound();
        $images = new Image();
        $database_name =$project->database_name;
        if ($database_name)
        {
            $config = config("append_database.".$database_name);
            config(['database.connections.'.$database_name => $config]);
            $images->setConnection($database_name);
        }
        $image = $images->find($optimus->decode($image_id));
        if ($image == null) return $this->respondNotFound();
        $user_id = $optimus->encode($project->user_id);
        if(!$image || !file_exists(storage_path("app/public/" .$user_id."/". $project_id . "/images/". $image->image)))  return $this->respondNotFound("not exist file");
        return response()->download(storage_path("app/public/" .$user_id."/". $project_id . "/images/".$image->image), $image->image);

    }
}
