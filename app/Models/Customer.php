<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

/**
 * @property int    $id
 * @property string $name
 * @property string $surname
 * @property string $photo_file
 * @property int    $creator_user_id
 * @property int    $updater_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property User   $creator
 * @property User   $updater
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'surname', 'photo_file', 'creator_user_id', 'updater_user_id'];
    protected $appends  = ['photo_url'];
    protected $casts    = ['creator_user_id' => 'integer',
                           'updater_user_id' => 'integer'];

    public static function booted()
    {
        // check Auth::id() to avoid writing null as creator_user_id when seeding
        static::creating(fn($model) => Auth::id() ? $model->creator_user_id = Auth::id() : '');
        static::updating(fn($model) => Auth::id() ? $model->updater_user_id = Auth::id() : '');
        // handle the file deletion
        static::deleting(fn($model) => Storage::delete($model->photo_file));
    }

    public function getPhotoUrlAttribute()
    {
        return asset('storage/' . $this->photo_file);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updater_user_id');
    }
}
