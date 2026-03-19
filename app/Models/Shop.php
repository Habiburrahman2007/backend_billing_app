<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address_line1',
        'address_line2',
        'phone_number',
        'upi_id',
        'footer_text',
        'logo_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
