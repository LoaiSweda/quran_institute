<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Library extends Model
{
    protected $table = 'libraries';
    protected $fillable = [
        'name', 'author', 'description', 'category_id', 'file_id', 'isbn', 'is_visible', 'user_id', 'institute_id'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
    ];

    /**
     * Get the category that owns the library item.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the file associated with the library item.
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Get the user who uploaded the library item.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the institute this library item belongs to (if any).
     */
    public function institute(): BelongsTo
    {
        return $this->belongsTo(Institute::class);
    }
}

