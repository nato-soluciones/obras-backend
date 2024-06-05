<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\Notifiable;

class Income extends Model
{
    use HasFactory, Notifiable, SoftDeletes;

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
                $income->receipt_number = static::generateReceiptNumber($income->obra_id);
            }
        });
    }

    protected static function generateReceiptNumber($obraId)
    {
        // Genera el número de recibo de fora correlativa para cada obra.
        // El formato es: 000-00000 -> primeros 3 Nros obra + 5 Nros consecutivos de ingresos
        $obraIdStr = str_pad($obraId, 3, '0', STR_PAD_LEFT);

        return DB::transaction(function () use ($obraId, $obraIdStr) {
            $lastIncome = static::where('obra_id', $obraId)->latest('id')->lockForUpdate()->first();
            $lastNumber = $lastIncome ? intval(substr($lastIncome->receipt_number, 6)) : 0;
            $newNumber = $lastNumber + 1;
            $receiptNumber = $obraIdStr . sprintf('-%05d', $newNumber);

            // Ensure uniqueness in case of race conditions
            while (static::where('obra_id', $obraId)
                ->where('receipt_number', $receiptNumber)
                ->exists()
            ) {
                $newNumber++;
                $receiptNumber = $obraIdStr . sprintf('-%05d', $newNumber);
            }

            return $receiptNumber;
        }, 5); // The second parameter is the number of attempts to run the transaction
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
