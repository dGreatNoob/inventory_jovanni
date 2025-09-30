<?php
namespace App\Observers;

use App\Models\SalesOrder;
use Illuminate\Support\Facades\Auth;
class SalesOrderObserver
{ 
    public function updated(SalesOrder $model)
    {
        if ($model->wasChanged('status')) {
            $model->approver = Auth::user()->id;
            $model->saveQuietly();
        }
    }
}   