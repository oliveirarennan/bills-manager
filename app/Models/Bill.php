<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $table = 'bills';
    protected $fillable = ['user_id', 'status', 'due_date', 'url'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
