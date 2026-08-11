<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AdminActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    public function log(
        string $action,
        ?Model $subject = null,
        ?array $properties = null,
        ?string $description = null
    ): AdminActivityLog {
        $admin = Auth::guard('admin')->user();

        return AdminActivityLog::create([
            'admin_id' => $admin?->id,
            'admin_name' => $admin?->name ?? 'Système',
            'action' => $action,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject?->id,
            'description' => $description,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function logCreate(Model $model, ?string $description = null): AdminActivityLog
    {
        return $this->log(
            action: 'create',
            subject: $model,
            properties: ['attributes' => $model->getAttributes()],
            description: $description ?? $this->getDefaultDescription('create', $model)
        );
    }

    public function logUpdate(Model $model, array $oldValues, ?string $description = null): AdminActivityLog
    {
        $changes = [];
        foreach ($model->getDirty() as $key => $newValue) {
            if (!in_array($key, ['updated_at', 'remember_token', 'password'])) {
                $changes[$key] = [
                    'old' => $oldValues[$key] ?? null,
                    'new' => $newValue,
                ];
            }
        }

        return $this->log(
            action: 'update',
            subject: $model,
            properties: ['changes' => $changes],
            description: $description ?? $this->getDefaultDescription('update', $model)
        );
    }

    public function logDelete(Model $model, ?string $description = null): AdminActivityLog
    {
        return $this->log(
            action: 'delete',
            subject: $model,
            properties: ['attributes' => $model->getAttributes()],
            description: $description ?? $this->getDefaultDescription('delete', $model)
        );
    }

    public function logLogin(?string $guardName = 'admin'): AdminActivityLog
    {
        $user = Auth::guard($guardName)->user();

        return $this->log(
            action: 'login',
            subject: $user,
            description: "Connexion de {$user->name}"
        );
    }

    public function logLogout(): AdminActivityLog
    {
        return $this->log(
            action: 'logout',
            description: 'Déconnexion'
        );
    }

    public function logExport(string $type, int $count): AdminActivityLog
    {
        return $this->log(
            action: 'export',
            properties: ['type' => $type, 'count' => $count],
            description: "Export de {$count} {$type}"
        );
    }

    public function logRefund(Model $booking, float $amount): AdminActivityLog
    {
        return $this->log(
            action: 'refund',
            subject: $booking,
            properties: ['amount' => $amount],
            description: "Remboursement de {$amount}€ pour la réservation #{$booking->reference}"
        );
    }

    public function logStatusChange(Model $model, string $oldStatus, string $newStatus): AdminActivityLog
    {
        return $this->log(
            action: 'status_change',
            subject: $model,
            properties: ['old_status' => $oldStatus, 'new_status' => $newStatus],
            description: "Changement de statut: {$oldStatus} → {$newStatus}"
        );
    }

    protected function getDefaultDescription(string $action, Model $model): string
    {
        $modelName = class_basename($model);
        $identifier = $model->name ?? $model->title ?? $model->reference ?? $model->id;

        return match ($action) {
            'create' => "{$modelName} créé: {$identifier}",
            'update' => "{$modelName} modifié: {$identifier}",
            'delete' => "{$modelName} supprimé: {$identifier}",
            default => "{$action} sur {$modelName}: {$identifier}",
        };
    }

    public function getRecentActivity(int $limit = 50): \Illuminate\Database\Eloquent\Collection
    {
        return AdminActivityLog::with('admin')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getActivityForSubject(Model $subject, int $limit = 20): \Illuminate\Database\Eloquent\Collection
    {
        return AdminActivityLog::where('subject_type', get_class($subject))
            ->where('subject_id', $subject->id)
            ->latest()
            ->limit($limit)
            ->get();
    }
}
