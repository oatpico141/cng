<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * BranchScope - Automatically filters queries by the currently selected branch
 *
 * This scope is applied to Transaction models (Treatment, Queue, DfPayment, etc.)
 * to ensure data isolation - users can only see transactions from their current branch.
 *
 * CRITICAL SECURITY: Prevents cross-branch data leakage
 */
class BranchScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Get the currently selected branch from session
        $selectedBranchId = session('selected_branch_id');

        // ถ้าเลือกสาขาแล้ว ต้อง filter ตามสาขานั้น (รวม Super Admin ด้วย)
        // เพราะทุกคนต้องทำงานในบริบทของสาขาที่เลือก
        if ($selectedBranchId) {
            $builder->where($model->getTable() . '.branch_id', $selectedBranchId);
        }
        // ถ้าไม่ได้เลือกสาขา และไม่ใช่ Super Admin ให้ filter ตาม branch ของ user
        elseif (auth()->check() && auth()->user()->username !== 'admin') {
            $userBranchId = auth()->user()->branch_id;
            if ($userBranchId) {
                $builder->where($model->getTable() . '.branch_id', $userBranchId);
            }
        }
        // Super Admin ที่ไม่ได้เลือกสาขา จะเห็นทุกสาขา (สำหรับ dashboard รวม)
    }
}
