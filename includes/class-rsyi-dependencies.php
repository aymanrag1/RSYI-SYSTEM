<?php
/**
 * RSYI Unified System — Dependencies Checker
 *
 * يتحقق من وجود وتفعيل الأنظمة الفرعية المطلوبة ويعرض
 * رسائل تحذيرية للمدير إذا كان أي نظام غير مفعّل.
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_Dependencies {

    /**
     * الأنظمة الفرعية المطلوبة مع معرّف التحقق لكل منها.
     *
     * التحقق يتم بوجود الجدول الرئيسي في قاعدة البيانات؛
     * هذا أكثر موثوقية من check_plugin_active لأن الـ plugin
     * قد يكون مثبتًا تحت مسار مختلف.
     *
     * @var array<string, array{label:string, table:string, plugin_file:string}>
     */
    const REQUIRED = [
        'hr' => [
            'label'       => 'نظام الموارد البشرية (RSYI HR System)',
            'table'       => 'rsyi_hr_employees',
            'plugin_file' => 'rsyi-hr-system/rsyi-hr-system.php',
        ],
        'warehouse' => [
            'label'       => 'نظام المخازن (Institute Warehouse)',
            'table'       => 'rsyi_wh_products',
            'plugin_file' => 'institute-warehouse/institute-warehouse.php',
        ],
        'students' => [
            'label'       => 'نظام شئون الطلاب (Student Affairs)',
            'table'       => 'rsyi_sa_students',
            'plugin_file' => 'rsyi-student-affairs/rsyi-student-affairs.php',
        ],
    ];

    /** حالة توافر الأنظمة بعد الفحص */
    private static array $status = [];

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * فحص الأنظمة وعرض تحذيرات للمدير إذا لزم.
     */
    public static function check(): void {
        self::$status = self::detect();
        add_action( 'admin_notices', [ self::class, 'render_notices' ] );
    }

    /**
     * هل النظام الفرعي متاح؟
     */
    public static function is_active( string $key ): bool {
        return self::$status[ $key ] ?? false;
    }

    /**
     * إرجاع مصفوفة الحالة كاملة.
     *
     * @return array<string, bool>
     */
    public static function get_status(): array {
        if ( empty( self::$status ) ) {
            self::$status = self::detect();
        }
        return self::$status;
    }

    /**
     * عرض إشعارات الإدارة للأنظمة الغائبة.
     */
    public static function render_notices(): void {
        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        foreach ( self::REQUIRED as $key => $info ) {
            if ( ! self::is_active( $key ) ) {
                printf(
                    '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
                    sprintf(
                        /* translators: %s: system name */
                        esc_html__( 'نظام RSYI الموحد: الإضافة "%s" غير مفعّلة — بعض ميزات لوحة التحكم لن تكون متاحة.', 'rsyi-system' ),
                        esc_html( $info['label'] )
                    )
                );
            }
        }
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    /**
     * فحص وجود الجداول الرئيسية لكل نظام.
     *
     * @return array<string, bool>
     */
    private static function detect(): array {
        global $wpdb;
        $result = [];

        foreach ( self::REQUIRED as $key => $info ) {
            $table  = $wpdb->prefix . $info['table'];
            $exists = (bool) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                $wpdb->prepare( 'SHOW TABLES LIKE %s', $table )
            );
            $result[ $key ] = $exists;
        }

        return $result;
    }
}
