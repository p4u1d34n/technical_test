<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class invoice_lines extends Model
{


    public function headers() {
        return $this->belongsTo(invoice_headers::class);
    }

    public function states() {
        return $this->hasMany(invoice_headers::class);
    }
   
}
