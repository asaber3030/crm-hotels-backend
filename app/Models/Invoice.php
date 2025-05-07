<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{

	use HasFactory, SoftDeletes;

	protected $table = 'invoices';
	protected $fillable = [
		'agent_id',
		'hotel_id',
		'category_id',
		'amount',
		'creation_date',
		'from1',
		'to1',
		'from2',
		'to2',
		'customer_name',
		'proxy_name',
		'reservation_number',
		'nights_count'
	];

	public function agent()
	{
		return $this->belongsTo(Agent::class);
	}

	public function hotel()
	{
		return $this->belongsTo(Hotel::class);
	}

	public function category()
	{
		return $this->belongsTo(Category::class);
	}

	public function payment()
	{
		return $this->hasOne(InvoicePayment::class);
	}

	public function collection()
	{
		return $this->hasOne(InvoiceCollection::class);
	}
}
