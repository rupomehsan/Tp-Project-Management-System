<?php

namespace App\Modules\Management\ProjectManagement\ProjectPlan\Actions;

use Illuminate\Support\Facades\DB;

class StoreData
{
    static $ProjectModuleModel = \App\Modules\Management\ProjectManagement\ProjectModule\Models\Model::class;
    static $PlanManagementModel = \App\Modules\Management\ProjectManagement\ProjectPlan\Models\PlanManagementModel::class;



    public static function execute($request)
    {
        try {

            // Use DB transaction so changes are atomic (create/update/delete)
            DB::beginTransaction();


            $modules = $request->modules;
            $moduleManagement = $request->module_management;

            foreach ($modules as $module) {

                $moduleData =   self::$ProjectModuleModel::where('id', $module['module_id'])->first();

                if ($moduleData) {
                    $updatedData = [
                        "project_id" => $module['project_id'],
                        "start_date" => $module['start_date'],
                        "end_date" => $module['end_date'],
                        "is_done" => $module['is_done'],
                    ];
                    $moduleData->update($updatedData);
                }
            }



            // keep track of DB ids that should remain for each module
            $keptIdsByModule = [];

            foreach ($moduleManagement as $management) {

                $data = [
                    'module_id' => $management['module_id'] ?? null,
                    'management_name' => $management['title'] ?? null,
                    'date' => $management['due'] ?? null,
                    'is_done' => isset($management['is_done']) ? (bool)$management['is_done'] : false,
                ];

                // Prefer explicit server id, fallback to local_id sent from client
                $identifier = null;
                if (isset($management['id']) && $management['id']) {
                    $identifier = $management['id'];
                } elseif (isset($management['local_id']) && $management['local_id']) {
                    $identifier = $management['local_id'];
                }

                // If an identifier is present, try to find (including trashed) and update
                if ($identifier) {
                    $existing = self::$PlanManagementModel::withTrashed()->find($identifier);
                    if ($existing) {
                        // restore if it was soft-deleted
                        if (method_exists($existing, 'restore') && $existing->trashed()) {
                            $existing->restore();
                        }
                        $existing->update($data);
                        // mark as kept (ensure array exists)
                        $mid = $data['module_id'];
                        if (!isset($keptIdsByModule[$mid])) $keptIdsByModule[$mid] = [];
                        $keptIdsByModule[$mid][] = $existing->id;
                        continue;
                    }
                }

                // As a defensive measure, try to find an existing record by unique-ish keys
                // (module_id + management_name + date). This prevents duplicate creations
                // when the client didn't send an id but the record already exists.
                $query = self::$PlanManagementModel::where('module_id', $data['module_id'])
                    ->where('management_name', $data['management_name']);
                if ($data['date']) {
                    // match by date part to handle DATETIME storage in DB
                    $query->whereDate('date', $data['date']);
                } else {
                    $query->whereNull('date');
                }

                $found = $query->first();
                if ($found) {
                    $found->update($data);
                    // mark as kept
                    $mid = $data['module_id'];
                    if (!isset($keptIdsByModule[$mid])) $keptIdsByModule[$mid] = [];
                    $keptIdsByModule[$mid][] = $found->id;
                    continue;
                }

                // Otherwise create a new record and mark it kept
                $created = self::$PlanManagementModel::query()->create($data);
                if ($created) {
                    $mid = $data['module_id'];
                    if (!isset($keptIdsByModule[$mid])) $keptIdsByModule[$mid] = [];
                    $keptIdsByModule[$mid][] = $created->id;
                }
            }
            // After processing payload, delete (soft-delete) any management rows for modules
            // that are not present in the kept ids. Only consider modules that appear in the
            // submitted module_management payload. This avoids deleting management rows for
            // modules that the client didn't intend to update (because `modules` is always sent).
            if (!empty($moduleManagement)) {
                $moduleIdsInPayload = array_values(array_unique(array_filter(array_map(function ($m) {
                    return isset($m['module_id']) ? $m['module_id'] : null;
                }, $moduleManagement))));

                foreach ($moduleIdsInPayload as $mid) {
                    $keepIds = isset($keptIdsByModule[$mid]) ? array_unique($keptIdsByModule[$mid]) : [];
                    $query = self::$PlanManagementModel::where('module_id', $mid);
                    if (!empty($keepIds)) {
                        $query->whereNotIn('id', $keepIds);
                    }
                    // soft delete the remaining ones
                    $query->delete();
                }
            }

            DB::commit();



            return messageResponse('Data stored successfully', [], 200);
        } catch (\Exception $e) {
            // rollback transaction on error
            try {
                DB::rollBack();
            } catch (\Exception $__) {
            }
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }
}
