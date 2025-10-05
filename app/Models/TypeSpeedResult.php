<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TypeSpeedResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'wpm',
        'correct_chars',
        'errors',
        'typed_chars',
        'accuracy',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
