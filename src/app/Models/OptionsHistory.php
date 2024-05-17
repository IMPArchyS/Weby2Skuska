<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionsHistory extends Model
{
    use HasFactory;

    protected $table = "options_history";

    protected $fillable = ['year', 'times_answered', 'option_id'];

    public function option()
    {
        return $this->belongsTo(Option::class);
    }
}
