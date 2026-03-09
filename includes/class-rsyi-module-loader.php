<?php
/**
 * RSYI Unified System — Module Loader
 * مُحمِّل الوحدات الفرعية للنظام الموحد
 *
 * يحمّل كلاسات كل وحدة مباشرةً داخل البلاجن الموحدة.
 * Loads each module's classes directly — no separate plugins needed.
 *
 * الحماية من التضارب | Conflict protection:
 *   إذا كانت البلاجن القديمة (HR / SA / Warehouse) لا تزال مفعَّلة،
 *   يتخطى الـ loader تحميل ملفاتها لأن كلاساتها موجودة بالفعل.
 *   If legacy sub-plugins are still active, their classes are already
 *   declared — we skip our copies to avoid "Cannot redeclare class" fatal.
 *
 * Namespaces:
 *   HR classes      → RSYI_HR\*
 *   Student classes → RSYI_SA\* / RSYI_SA\Modules\* / RSYI_SA\Admin\*
 *   Warehouse       → IW_* (global namespace)
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_Module_Loader {

    /**
     * الكلاسات الرئيسية لكل وحدة — تُستخدم لاكتشاف التضارب مع البلاجن القديمة.
     * Primary classes per module — used to detect legacy plugin conflicts.
     */
    private const HR_GUARD        = 'RSYI_HR\DB_Installer';
    private const STUDENTS_GUARD  = 'RSYI_SA\DB_Installer';
    private const WAREHOUSE_GUARD = 'IW_Database';

    /** ملفات وحدة الموارد البشرية | HR module files (no admin class — unified admin handles rendering) */
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
        // NOTE: class-hr-admin.php excluded — it uses RSYI_HR_DIR constant
        //       and registers its own admin menu. Unified admin handles views.
    ];

    /** ملفات وحدة شئون الطلاب | Student Affairs module files */
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
        'modules/students/portal/class-portal-shortcodes.php',
        // NOTE: class-sa-admin.php excluded — registers its own WP admin menu.
        //       Unified admin handles all views.
        // NOTE: class-updater.php excluded — GitHub auto-updater not needed
        //       in unified plugin.
    ];

    /** ملفات وحدة المخازن | Warehouse module files */
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
        // NOTE: class-iw-admin.php excluded — uses IW_PLUGIN_DIR constant
        //       and registers its own admin menu. Unified admin handles views.
    ];

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * تحميل الوحدات المفعَّلة فقط | Load only enabled modules.
     */
    public static function load(): void {
        $opts = get_option( 'rsyi_sys_options', [] );

        if ( ( $opts['hr_enabled'] ?? '1' ) === '1' ) {
            self::load_hr();
        }

        if ( ( $opts['students_enabled'] ?? '1' ) === '1' ) {
            self::load_students();
        }

        if ( ( $opts['warehouse_enabled'] ?? '1' ) === '1' ) {
            self::load_warehouse();
        }
    }

    /**
     * تحميل جميع الوحدات (يُستخدم عند التفعيل لتثبيت قاعدة البيانات).
     * Load all modules — used during plugin activation for DB install.
     */
    public static function load_all(): void {
        self::load_hr();
        self::load_students();
        self::load_warehouse();
    }

    /**
     * هل وحدة مفعَّلة؟ | Is a module enabled in settings?
     */
    public static function is_loaded( string $module ): bool {
        $opts = get_option( 'rsyi_sys_options', [] );
        return ( $opts[ $module . '_enabled' ] ?? '1' ) === '1';
    }

    // ─── Module loaders ───────────────────────────────────────────────────────

    private static function load_hr(): void {
        // إذا كانت البلاجن الأصلية مفعَّلة، الكلاسات موجودة بالفعل — تجاهل.
        // If original HR plugin is active, classes already declared — skip.
        if ( class_exists( self::HR_GUARD, false ) ) {
            return;
        }
        self::load_module( self::$hr_files, 'HR' );
    }

    private static function load_students(): void {
        if ( class_exists( self::STUDENTS_GUARD, false ) ) {
            return;
        }
        self::load_module( self::$students_files, 'Students' );
    }

    private static function load_warehouse(): void {
        if ( class_exists( self::WAREHOUSE_GUARD, false ) ) {
            return;
        }
        self::load_module( self::$warehouse_files, 'Warehouse' );
    }

    // ─── Private helper ───────────────────────────────────────────────────────

    private static function load_module( array $files, string $name ): void {
        foreach ( $files as $rel_path ) {
            $abs = RSYI_SYS_DIR . $rel_path;
            if ( file_exists( $abs ) ) {
                require_once $abs;
            } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions
                error_log( "RSYI [{$name}] Missing file: {$abs}" );
            }
        }
    }
}
