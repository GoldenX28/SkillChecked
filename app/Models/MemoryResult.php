<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemoryResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'moves',
        'time_seconds',
        'difficulty',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
