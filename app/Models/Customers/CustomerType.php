<?php

namespace App\Models\Customers;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
  protected $fillable = ['name'];

  public $timestamps = true;

  public function customers()
  {
    return $this->hasMany(Customer::class, 'customer_type_id', 'id');
  }
}
