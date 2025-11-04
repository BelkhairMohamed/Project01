<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model {
    use HasFactory;

    protected $fillable = [
        'name',
        'cin',
        'phone',
        'reason',
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
