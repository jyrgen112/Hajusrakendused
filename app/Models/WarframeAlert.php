<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarframeAlert extends Model
{
    protected $fillable = [
        'alert_id', 'title', 'description', 'image',
        'type', 'faction', 'eta', 'active'
    ];
}