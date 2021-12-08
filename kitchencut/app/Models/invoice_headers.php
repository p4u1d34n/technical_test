<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice_headers extends Model
{
    
    /**
     * appends
     *
     * @var array
     */
    protected $appends = [
        'value'
    ];

     /**
     * getValueAttribute
     *
     * @return float invoice_lines aggregated by value
     */
    public function getValueAttribute()
    {
        $invoice_lines = $this->hasMany(invoice_lines::class, 'invoice_header_id');
        return $invoice_lines->sum('value');
    }

 
    /**
     * invoiceLines
     *
     * @return array hasMany relationship
     */
    public function invoiceLines()
    {
        return $this->hasMany(invoice_lines::class, 'invoice_header_id');
    }

    
    /**
     * sumizeInvoice
     *
     * @return float $total
     */
    public function sumizeInvoice()
    {
        $total = 0;
        foreach ($this->invoiceLines as $line) {
            $total += $line->value;
        }
        return $total;
    }

    
    /**
     * getSummedInvoiceAttribute
     *
     * @return appendable attribute
     */
    public function getSummedInvoiceAttribute() {
        return $this->sumizeInvoice();
    }


}
