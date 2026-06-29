<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SliderImage extends Model
{
    protected $fillable = ['setting_id', 'title', 'description', 'image', 'link', 'order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }
}
