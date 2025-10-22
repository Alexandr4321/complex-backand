<?php

namespace App\Base\Classes;

use App\Auth\Models\User;
use App\Base\Models\File;
use App\Base\Services\FileService;
use App\System\Models\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile as UploadedFile;

class FileExtender extends Model
{
    protected $casts = [
        'fileId' => 'integer',
    ];
    
    protected $with = [ 'file' ];
    
    
    /*************
     * Relations *
     *************/
    
    public function file()
    {
        return $this->belongsTo(File::class, 'fileId');
    }
}
