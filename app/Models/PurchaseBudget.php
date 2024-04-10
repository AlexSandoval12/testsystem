<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PurchaseBudget extends Model
{
    protected $fillable = ['wish_purchase_id','confirmation_user_id','confirmation_date','name','original_name','status'];

    protected $dates =  ['confirmation_date'];

    public function wish_purchase()
    {
        return $this->belongsto('App\Models\WishPurchase');
    }

    public function confirmation_user()
    {
        return $this->belongsto('App\Models\User');
    }

    public function filePath()
    {
        return asset('storage/wish_purchases_budgets/' . $this->name);
    }


    public function getFileUrl()
    {
        return Storage::url($this->file);
    }
}
