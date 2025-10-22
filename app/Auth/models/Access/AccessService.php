<?php

namespace App\Auth\Service;

use App\Auth\Models\Access;
use App\Auth\Models\Grant;
use App\Auth\Models\GrantAuth;
use App\Auth\Models\Permit;
use App\Auth\Models\PermitGrant;
use App\Auth\Models\Role;
use App\Auth\Models\RolePermit;
use App\System\Models\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AccessService
{
    /**
     * @param  Model  $owner
     * @param  integer|null  $modelId
     */
    public static function getGrants($owner, $modelId = null)
    {
        $true = config('database.default') === 'sqlite' ? 1 : 'true';
        $false = config('database.default') === 'sqlite' ? 0 : 'false';

        $selectModelId = DB::raw('(case '.
            'when '.table(Access::class, '"modelId"').' is not NULL '.
            'and '.table(Grant::class, '"modelType"').' is not NULL '.
            'then '.table(Access::class, '"modelId"').' '.
            'else NULL end) as modelId');

        $selectIsGlobal = DB::raw('(case '.
            'when '.table(Grant::class, '"modelType"').' is NULL '.
            'then '.$true.' '.
            'when '.table(Access::class, '"modelId"').' is NULL '.
            'and '.table(Permit::class, '"modelType"').' is not NULL '.
            'then '.$true.' '.
            'when '.table(Permit::class, '"modelType"').' is NULL '.
            'then '.table(PermitGrant::class, '"isGlobal"').' '.
            'else '.$false.' end) as isGlobal');

        $grants = GrantAuth::query()
            ->select(table(Grant::class, 'name'), $selectModelId, $selectIsGlobal)
            ->distinct()
            ->leftJoin(table(PermitGrant::class), table(PermitGrant::class, 'grantId'), table(Grant::class, 'id'))
            ->leftJoin(table(Permit::class), table(Permit::class, 'id'), table(PermitGrant::class, 'permitId'))
            ->leftJoin(table(RolePermit::class), table(RolePermit::class, 'permitId'), table(Permit::class, 'id'))
            ->leftJoin(table(Role::class), table(Role::class, 'id'), table(RolePermit::class, 'roleId'))
            ->leftJoin(table(Access::class), function (JoinClause $query) {
                $concat = config('database.default') === 'sqlite' ? DB::raw(table(Access::class, 'name')." || '.%'")
                    : DB::raw('concat('.table(Access::class, 'name').", '.%')");
                $query->on(function(JoinClause $query) use ($concat) {
                    $query->on(table(Permit::class, 'name'), table(Access::class, 'name'))
                        ->orOn(table(Permit::class, 'name'), 'like', $concat)
                        ->where(table(Access::class, 'type'), Permit::class);
                })->orOn(function(JoinClause $query) {
                    $query->on(table(Role::class, 'name'), table(Access::class, 'name'))
                        ->where(table(Access::class, 'type'), Role::class);
                });
            })
            ->where(table(Access::class, 'ownerType'), get_class($owner))
            ->where(table(Access::class, 'ownerId'), $owner->id)
            ->where(function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->whereNotNull(table(Access::class, 'modelId'))
                        ->whereNotNull(table(Permit::class, 'modelType'));
                })->orWhereNull(table(Access::class, 'modelId'));
            })
            ->when($modelId, function (Builder $query, $modelId) {
                $query->where(function (Builder $query) use ($modelId) {
                    $query->whereNull(table(Access::class, 'modelId'))
                        ->orWhere(table(Access::class, 'modelId'), $modelId);
                });
            })
            ->get();

        $result = [];
        foreach ($grants as $grant) {
            $res = Arr::get($result, $grant->name);
            if (is_null($res)) {
                $res = [];
                if (!$grant->isGlobal) {
                    $res['id'] = [];
                    if ($grant->modelId) {
                        $res['id'][] = $grant->modelId;
                    }
                }
            } else {
                if (isset($res['id'])) {
                    if ($grant->isGlobal) {
                        unset($res['id']);
                    } elseif($grant->modelId) {
                        $res['id'][] = $grant->modelId;
                    }
                }
            }
            $result[$grant->name] = $res;
        }

//        $example = [
//            'project_read' => [ 'id' => [], 'precisions' => [] ],
//            'project_create' => [],
//        ];

        return $result;
    }
}
