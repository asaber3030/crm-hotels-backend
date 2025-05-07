<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoicePayment extends Model
{
	protected $table = 'invoice_payments';
	protected $fillable = [
		'invoice_id',
		'amount_egp',
		'amount_sar',
		'amount_usd',
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}
}
