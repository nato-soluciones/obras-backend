<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;

class Income extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'location',
        'email',
        'exchange_rate',
        'amount_usd',
        'amount_ars',
        'amount_ars_text',
        'payment_concept',
        'comments',
        'obra_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
      'date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($income) {
            if (empty($income->receipt_number)) {
                $income->receipt_number = static::generateReceiptNumber();
            }
        });
    }

    protected static function generateReceiptNumber()
    {
        return DB::transaction(function () {
            $lastIncome = static::latest('id')->lockForUpdate()->first();
            $lastNumber = $lastIncome ? intval(substr($lastIncome->receipt_number, 4)) : 0;
            $newNumber = $lastNumber + 1;
            $receiptNumber = sprintf('000-%05d', $newNumber);
            
            // Ensure uniqueness in case of race conditions
            while (static::where('receipt_number', $receiptNumber)->exists()) {
                $newNumber++;
                $receiptNumber = sprintf('000-%05d', $newNumber);
            }
            
            return $receiptNumber;
        }, 5); // The second parameter is the number of attempts to run the transaction
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
