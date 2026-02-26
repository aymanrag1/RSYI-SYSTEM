<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * WordPress User Model
 * يتعامل مع جدول wp_users مباشرةً للمصادقة.
 */
class WpUser extends Authenticatable
{
    use Notifiable;

    protected $table      = 'wp_users';
    protected $primaryKey = 'ID';
    public    $timestamps = false;

    protected $fillable = [
        'user_login',
        'user_email',
        'display_name',
    ];

    protected $hidden = [
        'user_pass',
    ];

    // WordPress يستخدم user_pass بدلاً من password
    public function getAuthPassword(): string
    {
        return $this->user_pass;
    }

    // اسم الحقل المستخدم في تسجيل الدخول
    public function getAuthIdentifierName(): string
    {
        return 'ID';
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function meta()
    {
        return $this->hasMany(WpUserMeta::class, 'user_id', 'ID');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * الحصول على meta value بالمفتاح.
     */
    public function getMeta(string $key): mixed
    {
        $meta = $this->meta()->where('meta_key', $key)->first();
        return $meta ? maybe_unserialize($meta->meta_value) : null;
    }

    /**
     * الأدوار المعيّنة للمستخدم من WordPress.
     */
    public function wpRoles(): array
    {
        $cap = $this->meta()
            ->where('meta_key', 'wp_capabilities')
            ->first();

        if (! $cap) {
            return [];
        }

        $caps = @unserialize($cap->meta_value);
        if (! is_array($caps)) {
            return [];
        }

        return array_keys(array_filter($caps));
    }

    /**
     * هل للمستخدم هذا الدور؟
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->wpRoles(), true);
    }

    /**
     * هل للمستخدم أي من هذه الأدوار؟
     */
    public function hasAnyRole(array $roles): bool
    {
        return (bool) array_intersect($roles, $this->wpRoles());
    }

    /**
     * هل المستخدم مدير (administrator أو rsyi_dean)؟
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['administrator', 'rsyi_dean']);
    }

    /**
     * الاسم المعروض.
     */
    public function getNameAttribute(): string
    {
        return $this->display_name ?: $this->user_login;
    }
}
