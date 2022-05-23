<?php

namespace App\Models\Salesforce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hook extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'contact_id',
        'status_id',
        'pipeline_id',
        'salesforce_id',
        'is_send',
        'phone',
        'name',
        'email',
        'position',
        'company',
        'manager',
        'email_manager',
        'comment',
    ];
}
