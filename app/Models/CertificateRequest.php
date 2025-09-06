<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;


class CertificateRequest extends Model
{
    protected $table = 'certificate_requests';


    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REFUSED = 'refused';


    protected $fillable = [
        'student_id','subject_id','request_at','status','user_id','reviewed_at','file_id'
    ];


    protected $casts = [
        'request_at' => 'datetime',
        'reviewed_at'=> 'datetime',
    ];


// Who is the certificate for
    public function student()
    {
        return $this->belongsTo(Student::class);
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }


// The reviewing admin (super admin) — optional
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }


// Scopes
    public function scopeForInstitute($query, int $instituteId)
    {
        return $query->whereHas('subject', fn($q) => $q->where('institute_id', $instituteId));
    }

    public function scopePending($q)  { return $q->where('status', self::STATUS_PENDING); }
    public function scopeApproved($q) { return $q->where('status', self::STATUS_APPROVED); }
    public function scopeRefused($q)  { return $q->where('status', self::STATUS_REFUSED); }
}
