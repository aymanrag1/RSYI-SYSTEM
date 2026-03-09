<?php
/**
 * RSYI Unified System — Module Loader
 * مُحمِّل الوحدات الفرعية للنظام الموحد
 *
 * يقوم بتحميل كلاسات كل وحدة مباشرةً داخل البلاجن الموحدة
 * دون الحاجة لتثبيت بلاجنز منفصلة.
 *
 * Namespaces:
 *   HR classes      → RSYI_HR\*
 *   Student classes → RSYI_SA\* and RSYI_SA\Modules\*
 *   Warehouse       → IW_* (global namespace)
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_Module_Loader {

    /** مسارات ملفات وحدة الموارد البشرية | HR module class files */
    private static array $hr_files = [
        'modules/hr/includes/class-hr-db-installer.php',
        'modules/hr/includes/class-hr-roles.php',
        'modules/hr/includes/class-hr-api.php',
        'modules/hr/includes/class-hr-departments.php',
        'modules/hr/includes/class-hr-employees.php',
        'modules/hr/includes/class-hr-leaves.php',
        'modules/hr/includes/class-hr-overtime.php',
        'modules/hr/includes/class-hr-attendance.php',
        'modules/hr/includes/class-hr-violations.php',
        'modules/hr/includes/class-hr-permissions-mgr.php',
        'modules/hr/includes/class-hr-leave-balance.php',
        'modules/hr/includes/class-hr-portal.php',
        'modules/hr/admin/class-hr-admin.php',
    ];

    /** مسارات ملفات وحدة شئون الطلاب | Student Affairs module class files */
    private static array $students_files = [
        'modules/students/includes/class-db-installer.php',
        'modules/students/includes/class-roles.php',
        'modules/students/includes/class-audit-log.php',
        'modules/students/includes/class-email-notifications.php',
        'modules/students/includes/class-secure-download.php',
        'modules/students/modules/class-accounts.php',
        'modules/students/modules/class-documents.php',
        'modules/students/modules/class-requests.php',
        'modules/students/modules/class-behavior.php',
        'modules/students/modules/class-cohorts.php',
        'modules/students/modules/class-evaluations.php',
        'modules/students/admin/class-sa-admin.php',
        'modules/students/portal/class-portal-shortcodes.php',
    ];

    /** مسارات ملفات وحدة المخازن | Warehouse module class files */
    private static array $warehouse_files = [
        'modules/warehouse/includes/class-iw-database.php',
        'modules/warehouse/includes/class-iw-permissions.php',
        'modules/warehouse/includes/class-iw-categories.php',
        'modules/warehouse/includes/class-iw-suppliers.php',
        'modules/warehouse/includes/class-iw-departments.php',
        'modules/warehouse/includes/class-iw-products.php',
        'modules/warehouse/includes/class-iw-transactions.php',
        'modules/warehouse/includes/class-iw-opening-balance.php',
        'modules/warehouse/includes/class-iw-add-orders.php',
        'modules/warehouse/includes/class-iw-withdrawal-orders.php',
        'modules/warehouse/includes/class-iw-purchase-requests.php',
        'modules/warehouse/includes/class-iw-excel-import.php',
        'modules/warehouse/includes/class-iw-reports.php',
        'modules/warehouse/admin/class-iw-admin.php',
    ];

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * تحميل جميع الوحدات المفعَّلة | Load all enabled modules.
     */
    public static function load(): void {
        $opts = get_option( 'rsyi_sys_options', [] );

        if ( ! empty( $opts['hr_enabled'] ) && $opts['hr_enabled'] === '1' ) {
            self::load_module( self::$hr_files, 'HR' );
        }

        if ( ! empty( $opts['students_enabled'] ) && $opts['students_enabled'] === '1' ) {
            self::load_module( self::$students_files, 'Students' );
        }

        if ( ! empty( $opts['warehouse_enabled'] ) && $opts['warehouse_enabled'] === '1' ) {
            self::load_module( self::$warehouse_files, 'Warehouse' );
        }
    }

    /**
     * تحميل جميع الوحدات دائمًا (للـ DB installer والـ Roles).
     * Load all modules always (used during activation for DB + roles).
     */
    public static function load_all(): void {
        self::load_module( self::$hr_files,        'HR' );
        self::load_module( self::$students_files,  'Students' );
        self::load_module( self::$warehouse_files, 'Warehouse' );
    }

    /**
     * هل وحدة معينة محمَّلة؟ | Is a module loaded?
     */
    public static function is_loaded( string $module ): bool {
        $opts = get_option( 'rsyi_sys_options', [] );
        return ! empty( $opts[ $module . '_enabled' ] ) && $opts[ $module . '_enabled' ] === '1';
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    private static function load_module( array $files, string $name ): void {
        foreach ( $files as $rel_path ) {
            $abs = RSYI_SYS_DIR . $rel_path;
            if ( file_exists( $abs ) ) {
                require_once $abs;
            } else {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    // phpcs:ignore WordPress.PHP.DevelopmentFunctions
                    error_log( "RSYI [{$name}] Missing: {$abs}" );
                }
            }
        }
    }
}
