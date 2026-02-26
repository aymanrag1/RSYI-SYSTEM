<?php
/**
 * RSYI Unified System — Roles & Capabilities
 *
 * يُوحِّد الأدوار الموجودة في الأنظمة الفرعية ويضيف صلاحيات
 * خاصة بلوحة التحكم الموحدة دون إنشاء أدوار مكررة.
 *
 * الأدوار المُعرَّفة في HR plugin تُستخدم كمرجع:
 *   rsyi_dean | rsyi_hr_manager | rsyi_dept_head | rsyi_staff | rsyi_readonly
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_Roles {

    /**
     * صلاحيات النظام الموحد التي تُضاف على الأدوار الموجودة.
     *
     * @var array<string, array<string, bool>>
     */
    private static array $unified_caps = [

        // العميد / المدير العام
        'rsyi_dean' => [
            'rsyi_sys_view_dashboard'   => true,
            'rsyi_sys_view_all_systems' => true,
            'rsyi_sys_manage_settings'  => true,
            'rsyi_sys_view_audit_log'   => true,
            'rsyi_sys_manage_roles'     => true,
        ],

        // مدير الموارد البشرية
        'rsyi_hr_manager' => [
            'rsyi_sys_view_dashboard'  => true,
            'rsyi_sys_view_hr'         => true,
            'rsyi_sys_view_warehouse'  => true,
            'rsyi_sys_view_audit_log'  => true,
        ],

        // رئيس القسم
        'rsyi_dept_head' => [
            'rsyi_sys_view_dashboard' => true,
            'rsyi_sys_view_hr'        => true,
            'rsyi_sys_view_warehouse' => true,
        ],

        // موظف
        'rsyi_staff' => [
            'rsyi_sys_view_dashboard' => true,
            'rsyi_sys_view_hr'        => true,
        ],

        // قراءة فقط
        'rsyi_readonly' => [
            'rsyi_sys_view_dashboard' => true,
        ],
    ];

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * إضافة الصلاحيات الموحدة على الأدوار الموجودة عند التفعيل.
     */
    public static function add_roles(): void {
        self::sync_roles();
    }

    /**
     * مزامنة الصلاحيات — آمن للاستدعاء المتكرر.
     */
    public static function sync_roles(): void {
        foreach ( self::$unified_caps as $role_slug => $caps ) {
            $role = get_role( $role_slug );
            if ( ! $role ) {
                continue; // الدور غير موجود بعد (الـ HR plugin لم يُفعَّل)
            }
            foreach ( $caps as $cap => $grant ) {
                $role->add_cap( $cap, $grant );
            }
        }

        // المدير يرث كل الصلاحيات تلقائيًا
        self::sync_admin();
    }

    /**
     * إزالة الصلاحيات الموحدة عند إلغاء التثبيت.
     */
    public static function remove_caps(): void {
        $all_caps = array_keys( array_merge( ...array_values( self::$unified_caps ) ) );

        foreach ( array_keys( self::$unified_caps ) as $role_slug ) {
            $role = get_role( $role_slug );
            if ( ! $role ) {
                continue;
            }
            foreach ( $all_caps as $cap ) {
                $role->remove_cap( $cap );
            }
        }
    }

    /**
     * التحقق من أن المستخدم الحالي لديه صلاحية معينة.
     */
    public static function current_user_can( string $cap ): bool {
        return current_user_can( $cap );
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    /** إضافة جميع صلاحيات النظام الموحد للمدير (administrator). */
    private static function sync_admin(): void {
        $admin = get_role( 'administrator' );
        if ( ! $admin ) {
            return;
        }
        foreach ( self::$unified_caps as $caps ) {
            foreach ( $caps as $cap => $grant ) {
                $admin->add_cap( $cap, true );
            }
        }
    }
}
