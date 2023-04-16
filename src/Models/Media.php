<?php

namespace Apachish\Media\Models;


use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = ['title','file','status','type','size','path',"created_by"];

    protected $table = 'medias';


    public function createdBy()
    {
        return $this->belongsTo(User::class,'created_by');
    }
    

}
