<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifyFailedDespatchedItem extends Model
{
    use HasFactory;

    //protected $primaryKey = 'SKU';

    protected $fillable = [

        'ReferenceNumber',
        'SKU',
        'OrderLineNumber',
        'DespatchedQuantity'

    ];

    
    public function NotifyFailedDespatchedOrder()
    {
        return $this->belongsToMany(NotifyFailedDespatchedOrder::class);
    }
    

}
