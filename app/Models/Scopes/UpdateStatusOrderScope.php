<?php

namespace App\Models\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UpdateStatusOrderScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->getModel()->newQueryWithoutScopes()->each(function ($order) {
            if ($order->balance == 0) {
                $order->status = 'PAGADO';
            } else {
                $order->status = 'PENDIENTE';
            }
            $order->save();
        });
    }
}
