<?php
/**
 * RSYI Unified System — Admin Interface
 * واجهة الإدارة الموحدة للنظام الإداري لمعهد البحر الأحمر
 *
 * يُسجِّل قائمة موحدة بتبويبات لكل وحدة + يُحمِّل الأصول.
 * Single admin menu with tabs for each enabled module.
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_Admin {

    // ─── Init ────────────────────────────────────────────────────────────────

    public static function init(): void {
        add_action( 'admin_menu',            [ self::class, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ self::class, 'enqueue_assets' ] );
        add_action( 'wp_ajax_rsyi_sys_mark_notification_read', [ self::class, 'ajax_mark_read' ] );
        add_action( 'wp_ajax_rsyi_sys_get_notifications',      [ self::class, 'ajax_get_notifications' ] );
        add_action( 'wp_ajax_rsyi_toggle_module',              [ self::class, 'ajax_toggle_module' ] );
    }

    // ─── Menu ────────────────────────────────────────────────────────────────

    public static function register_menu(): void {

        // القائمة الرئيسية | Main menu
        add_menu_page(
            __( 'RSYI — النظام الإداري الموحد', 'rsyi-system' ),
            __( 'RSYI System', 'rsyi-system' ),
            'manage_options',
            'rsyi-system',
            [ self::class, 'render_main' ],
            'dashicons-networking',
            2
        );

        // لوحة التحكم | Dashboard
        add_submenu_page(
            'rsyi-system',
            __( 'لوحة التحكم | Dashboard', 'rsyi-system' ),
            __( 'لوحة التحكم', 'rsyi-system' ),
            'manage_options',
            'rsyi-system',
            [ self::class, 'render_main' ]
        );

        // الموارد البشرية | HR
        if ( RSYI_Sys_Module_Loader::is_loaded( 'hr' ) ) {
            add_submenu_page(
                'rsyi-system',
                __( 'الموارد البشرية | HR', 'rsyi-system' ),
                __( 'الموارد البشرية', 'rsyi-system' ),
                'manage_options',
                'rsyi-hr',
                [ self::class, 'render_hr' ]
            );
        }

        // شئون الطلاب | Student Affairs
        if ( RSYI_Sys_Module_Loader::is_loaded( 'students' ) ) {
            add_submenu_page(
                'rsyi-system',
                __( 'شئون الطلاب | Student Affairs', 'rsyi-system' ),
                __( 'شئون الطلاب', 'rsyi-system' ),
                'manage_options',
                'rsyi-students',
                [ self::class, 'render_students' ]
            );
        }

        // المخازن | Warehouse
        if ( RSYI_Sys_Module_Loader::is_loaded( 'warehouse' ) ) {
            add_submenu_page(
                'rsyi-system',
                __( 'المخازن | Warehouse', 'rsyi-system' ),
                __( 'المخازن', 'rsyi-system' ),
                'manage_options',
                'rsyi-warehouse',
                [ self::class, 'render_warehouse' ]
            );
        }

        // الحسابات (قيد الإنشاء) | Accounting (coming soon)
        add_submenu_page(
            'rsyi-system',
            __( 'الحسابات | Accounting', 'rsyi-system' ),
            __( 'الحسابات 🔒', 'rsyi-system' ),
            'manage_options',
            'rsyi-accounting',
            [ self::class, 'render_accounting' ]
        );

        // الإعدادات | Settings
        add_submenu_page(
            'rsyi-system',
            __( 'الإعدادات | Settings', 'rsyi-system' ),
            __( 'الإعدادات', 'rsyi-system' ),
            'manage_options',
            'rsyi-settings',
            [ self::class, 'render_settings' ]
        );
    }

    // ─── Renderers ───────────────────────────────────────────────────────────

    public static function render_main(): void {
        $tab = sanitize_key( $_GET['tab'] ?? 'dashboard' );
        self::render_wrapper( $tab );
    }

    public static function render_hr(): void {
        $tab = sanitize_key( $_GET['tab'] ?? 'employees' );
        self::render_wrapper( 'hr', $tab );
    }

    public static function render_students(): void {
        $tab = sanitize_key( $_GET['tab'] ?? 'students' );
        self::render_wrapper( 'students', $tab );
    }

    public static function render_warehouse(): void {
        $tab = sanitize_key( $_GET['tab'] ?? 'products' );
        self::render_wrapper( 'warehouse', $tab );
    }

    public static function render_accounting(): void {
        self::render_wrapper( 'accounting' );
    }

    public static function render_settings(): void {
        self::render_wrapper( 'settings' );
    }

    // ─── Core Wrapper ────────────────────────────────────────────────────────

    /**
     * يعرض الغلاف الرئيسي مع شريط التبويبات | Renders wrapper with tab bar.
     */
    private static function render_wrapper( string $module, string $sub_tab = '' ): void {
        $opts = get_option( 'rsyi_sys_options', RSYI_Sys_Settings::DEFAULTS );
        require_once RSYI_SYS_DIR . 'admin/views/layout.php';
    }

    // ─── Assets ──────────────────────────────────────────────────────────────

    public static function enqueue_assets( string $hook ): void {
        if ( strpos( $hook, 'rsyi' ) === false && $hook !== 'toplevel_page_rsyi-system' ) {
            return;
        }

        // Bootstrap 5 RTL
        wp_enqueue_style(
            'rsyi-bootstrap',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css',
            [],
            '5.3.3'
        );
        wp_enqueue_script(
            'rsyi-bootstrap-js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            [],
            '5.3.3',
            true
        );

        // Font Awesome 6
        wp_enqueue_style(
            'rsyi-fa',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css',
            [],
            '6.5.0'
        );

        // Chart.js
        wp_enqueue_script(
            'rsyi-chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            [],
            '4.4.0',
            true
        );

        // Admin CSS (unified)
        wp_enqueue_style(
            'rsyi-admin',
            RSYI_SYS_URL . 'assets/css/rsyi-admin.css',
            [ 'rsyi-bootstrap' ],
            RSYI_SYS_VERSION
        );

        // Admin JS (unified)
        wp_enqueue_script(
            'rsyi-admin',
            RSYI_SYS_URL . 'assets/js/rsyi-admin.js',
            [ 'jquery', 'rsyi-bootstrap-js' ],
            RSYI_SYS_VERSION,
            true
        );

        wp_localize_script( 'rsyi-admin', 'rsyiSys', [
            'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'rsyi_sys_admin' ),
            'adminUrl' => admin_url( 'admin.php' ),
            'unread'   => RSYI_Sys_DB_Installer::unread_count( get_current_user_id() ),
            'i18n'     => [
                'confirm_delete' => __( 'هل أنت متأكد؟ | Are you sure? This cannot be undone.', 'rsyi-system' ),
                'saving'         => __( 'جارٍ الحفظ... | Saving...', 'rsyi-system' ),
                'saved'          => __( 'تم الحفظ | Saved', 'rsyi-system' ),
                'error'          => __( 'حدث خطأ | Error occurred', 'rsyi-system' ),
                'module_enabled' => __( 'تم تفعيل الوحدة | Module enabled', 'rsyi-system' ),
                'module_disabled'=> __( 'تم إيقاف الوحدة | Module disabled', 'rsyi-system' ),
            ],
        ] );
    }

    // ─── AJAX ────────────────────────────────────────────────────────────────

    public static function ajax_mark_read(): void {
        check_ajax_referer( 'rsyi_sys_admin', 'nonce' );
        global $wpdb;
        $id = absint( $_POST['id'] ?? 0 );
        $wpdb->update(
            $wpdb->prefix . 'rsyi_sys_notifications',
            [ 'is_read' => 1 ],
            [ 'id' => $id, 'recipient_id' => get_current_user_id() ],
            [ '%d' ],
            [ '%d', '%d' ]
        );
        wp_send_json_success();
    }

    public static function ajax_get_notifications(): void {
        check_ajax_referer( 'rsyi_sys_admin', 'nonce' );
        global $wpdb;
        $rows = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}rsyi_sys_notifications
                 WHERE recipient_id = %d ORDER BY created_at DESC LIMIT 10",
                get_current_user_id()
            )
        );
        wp_send_json_success( $rows );
    }

    public static function ajax_toggle_module(): void {
        check_ajax_referer( 'rsyi_sys_admin', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        $module  = sanitize_key( $_POST['module'] ?? '' );
        $enabled = (bool) ( $_POST['enabled'] ?? false );

        $allowed = [ 'hr', 'students', 'warehouse' ];
        if ( ! in_array( $module, $allowed, true ) ) {
            wp_send_json_error( 'Invalid module' );
        }

        RSYI_Sys_Settings::set( $module . '_enabled', $enabled ? '1' : '0' );
        wp_send_json_success( [
            'module'  => $module,
            'enabled' => $enabled,
        ] );
    }
}
