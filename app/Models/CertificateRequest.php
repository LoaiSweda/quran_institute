<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateRequest extends Model
{
    protected $table = 'certificate_requests';
    protected $fillable = [
        'student_id','subject_id','request_at','status','user_id','revieweded_at','file_id'
    ];

    public function file()
    {
        return $this->belongsTo(\App\Models\File::class, 'file_id');
    }
}

