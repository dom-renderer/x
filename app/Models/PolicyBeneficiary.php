<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PolicyBeneficiary extends Model
{
    use SoftDeletes, \App\Traits\Draftable;

    protected $guarded = [];

    public function insured () {
        return $this->belongsTo(PolicyInsuredLifeInformation::class, 'insured_life_id');
    }
}
