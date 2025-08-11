<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    /** @use HasFactory<\Database\Factories\TodoFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'is_done',
        'deadline',
    ];
    
    protected $hidden = ['user_id'];

    public function user():BelongsTo 
    {
        return $this->belongsTo(User::class);
    }
}
