<?php

namespace App\Modules\Management\ProjectManagement\Project\Actions;

use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

/**
 * Update a project row and its related assets.
 *
 * – Keeps existing “document files” unless the request sets `"delete": true`
 * – Deletes only the files explicitly flagged for removal
 * – Adds any new uploads
 */
class UpdateData
{
    /** @var class-string<EloquentModel> */
    public static string $model = \App\Modules\Management\ProjectManagement\Project\Models\Model::class;

    /**
     * Handle the update request.
     */
    public static function execute($request, string $slug)
    {
        try {
            /** @var \Illuminate\Database\Eloquent\Model $project */
            $project = self::$model::query()->where('slug', $slug)->firstOrFail();

            return DB::transaction(function () use ($request, $project) {
                $payload     = $request->validated();
                $dateSegment = now()->format('Y/m');

                // ───── single‑file fields ────────────────────────────
                $payload['project_agrement_file'] = self::handleSingleFile(
                    $request,
                    $project,
                    'project_agrement_file',
                    "uploads/project/agrement/{$dateSegment}"
                );

                $payload['project_document'] = self::handleSingleFile(
                    $request,
                    $project,
                    'project_document',
                    "uploads/project/document/{$dateSegment}"
                );

                // ───── link array ────────────────────────────────────
                $payload['project_document_links'] = collect(
                    $request->input('project_document_links', [])
                )
                    ->filter(fn ($item) => filled($item['link'] ?? null))
                    ->map(fn ($item) => [
                        'name' => $item['name'] ?? '',
                        'link' => $item['link'],
                    ])
                    ->values()
                    ->all();

                // ───── file array (merge, don’t replace) ─────────────
                if ($request->filled('project_document_files')) {
                    $payload['project_document_files'] = self::syncDocumentFiles(
                        $request->project_document_files,
                        $project->project_document_files,
                        "uploads/project/documents/{$dateSegment}"
                    );
                }

                // ───── persist + sync relationships ─────────────────
                $project->update($payload);

                $projectUsers = json_decode(
                    $request->input('project_users', '[]'),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                );
                $project->project_users()->sync($projectUsers);

                return messageResponse('Item updated successfully', $project->fresh(), 200);
            });
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return messageResponse('Data not found…', null, 404, 'error');
        } catch (\Throwable $e) {
            return messageResponse($e->getMessage(), [], 500, 'server_error');
        }
    }

    // ────────────────────────────────────────────────────────────────
    // Helpers
    // ────────────────────────────────────────────────────────────────

    /**
     * Replace (or keep) a single file attribute.
     */
    private static function handleSingleFile($request, $model, string $field, string $dir): ?string
    {
        if (!$request->hasFile($field)) {
            return $model->{$field};       // keep existing
        }

        self::deleteIfExists($model->{$field});

        /** @var UploadedFile $file */
        $file = $request->file($field);

        return self::uploader($file, $dir);
    }

    /**
     * Merge document‑file array:
     *  – retains every existing file unless the client adds `"delete": true`
     *  – deletes and removes only those explicitly flagged
     *  – appends any new uploads
     *
     * @param array                             $incoming raw request array
     * @param array|string|null                 $existing value stored in DB
     * @param string                            $dir      base upload directory
     * @return array                                     updated array to store
     */
    private static function syncDocumentFiles(array $incoming, $existing, string $dir): array
    {
        // Cast the stored value to a Collection
        $existing = collect(
            is_array($existing) ? $existing : json_decode($existing ?? '[]', true)
        );

        // Build a fast lookup for items flagged “delete”
        $deletions = collect($incoming)
            ->filter(fn ($item) => ($item['delete'] ?? false) && is_string($item['file'] ?? null))
            ->mapWithKeys(fn ($item) => [
                self::docKey($item['name'] ?? '', $item['file']) => true,
            ]);

        // 1️⃣ Keep everything not flagged for removal
        $kept = $existing
            ->filter(function ($item) use ($deletions) {
                $key = self::docKey($item['name'] ?? '', $item['file'] ?? '');
                if ($deletions->has($key)) {
                    self::deleteIfExists($item['file'] ?? '');
                    return false;           // drop
                }
                return true;                // keep
            })
            ->values()
            ->all();

        // 2️⃣ Add new uploads
        foreach ($incoming as $item) {
            $file = $item['file'] ?? null;

            if ($file instanceof UploadedFile) {
                $kept[] = [
                    'name' => $item['name'] ?? '',
                    'file' => self::uploader($file, $dir),
                ];
            }
        }

        // dd($kept);

        return $kept;
    }

    /** Unique composite key for quick comparisons */
    private static function docKey(string $name, string $path): string
    {
        return "{$name}|{$path}";
    }

    /** Delete file from disk if it exists */
    private static function deleteIfExists(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /** Wrapper around Storage::putFile */
    private static function uploader(UploadedFile $file, string $dir): string
    {
        return $file->store($dir, 'public');
    }
}
