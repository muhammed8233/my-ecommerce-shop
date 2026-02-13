<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAudit
{
   
    protected static function bootHasAudit()
    {
        static::creating(function ($model) {
            if (!$model->created_by) {
                $model->created_by = Auth::id();
            }
        });

        static::updating(function ($model) {
            $model->last_modified_by = Auth::id(); 
        });
    }
}
