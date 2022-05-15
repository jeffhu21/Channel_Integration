<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifyFailedDespatchedOrder extends Model
{
    use HasFactory;

    //protected $primaryKey = 'ReferenceNumber';

    protected $fillable = [

        'app_user_id',
        'ReferenceNumber',
        'ShippingVendor',
        'ShippingMethod',
        'TrackingNumber',
        'SecondaryTrackingNumbers',
        'ProcessedOn'

    ];

    public function appUser()
    {
        return $this->belongsTo(AppUser::class);
    }

    
    public function NotifyFailedDespatchedItem()
    {
        return $this->hasMany(NotifyFailedDespatchedItem::class);
    }
    

}
