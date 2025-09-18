<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the invoice items for the product.
     */
    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
