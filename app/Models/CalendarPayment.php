<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class CalendarPayment extends Model
{
    protected $fillable = [
        'social_reason_id',
        'date',
        'purchase_id',
        'provider_id',
        'purchase_collect_id',
        'description',
        'amount',
        'last_calendar_payment_id',
        'user_id',
        'user_delete_id',
        'user_rescheduled_id',
        'reason',
        'status',
    ];

    protected $dates = ['date'];

    public function setDateAttribute($date)
    {
        if (str_contains($date, '/'))
        {
            $this->attributes['date'] = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        }
        else
        {
            $this->attributes['date'] = $date;
        }
    }

    public function setAmountAttribute($amount)
    {
        $this->attributes['amount'] = str_replace('.', '', $amount);
    }


    /**
     * Get the purchase that owns the CalendarPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    /**
     * Get the purchases_provider that owns the CalendarPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
    
    /**
     * Get the last_calendar_payment that owns the CalendarPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function last_calendar_payment()
    {
        return $this->belongsTo(CalendarPayment::class);
    }

    /**
     * Get the user that owns the CalendarPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user_delete that owns the CalendarPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_delete()
    {
        return $this->belongsTo(User::class, 'user_delete_id');
    }

    /**
     * Get the user_rescheduled that owns the CalendarPayment
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user_rescheduled()
    {
        return $this->belongsTo(User::class, 'user_rescheduled_id');
    }

}
