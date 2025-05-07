<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceCollection extends Model
{
	protected $table = 'invoice_collections';
	protected $fillable = [
		'invoice_id',
		'amount_egp',
		'amount_sar',
		'amount_usd',
		'link'
	];

	public function invoice()
	{
		return $this->belongsTo(Invoice::class);
	}
}
