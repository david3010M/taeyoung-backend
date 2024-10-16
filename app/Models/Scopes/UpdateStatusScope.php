<?php

namespace App\Models\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class UpdateStatusScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $builder->getModel()->newQueryWithoutScopes()->each(function ($account) {
            if ($account->balance == 0) {
                $account->status = 'PAGADO';
            } else if ($account->date < Carbon::today()) {
                $account->status = 'VENCIDO';
            } else {
                $account->status = 'PENDIENTE';
            }
            $account->save();
        });
    }
}
