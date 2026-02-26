<?php
/**
 * RSYI Unified System — Admin Interface
 *
 * يُسجِّل قائمة لوحة الإدارة الموحدة ويحمِّل الـ assets الخاصة بها.
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_Admin {

    // ─── Init ────────────────────────────────────────────────────────────────

    public static function init(): void {
        add_action( 'admin_menu',           [ self::class, 'register_menu' ] );
        add_action( 'admin_enqueue_scripts', [ self::class, 'enqueue_assets' ] );
        add_action( 'wp_ajax_rsyi_sys_mark_notification_read', [ self::class, 'ajax_mark_read' ] );
        add_action( 'wp_ajax_rsyi_sys_get_notifications',      [ self::class, 'ajax_get_notifications' ] );
    }

    // ─── Menu ────────────────────────────────────────────────────────────────

    public static function register_menu(): void {

        // القائمة الرئيسية
        add_menu_page(
            __( 'RSYI — النظام الموحد', 'rsyi-system' ),
            __( 'RSYI الموحد', 'rsyi-system' ),
            'rsyi_sys_view_dashboard',
            'rsyi-system',
            [ self::class, 'render_dashboard' ],
            'dashicons-networking',
            2
        );

        // لوحة التحكم
        add_submenu_page(
            'rsyi-system',
            __( 'لوحة التحكم', 'rsyi-system' ),
            __( 'لوحة التحكم', 'rsyi-system' ),
            'rsyi_sys_view_dashboard',
            'rsyi-system',
            [ self::class, 'render_dashboard' ]
        );

        // ── نظام الموارد البشرية ─────────────────────────────────────────
        if ( RSYI_Sys_Dependencies::is_active( 'hr' ) ) {
            self::hr_submenu();
        }

        // ── نظام المخازن ──────────────────────────────────────────────────
        if ( RSYI_Sys_Dependencies::is_active( 'warehouse' ) ) {
            self::warehouse_submenu();
        }

        // ── شئون الطلاب ───────────────────────────────────────────────────
        if ( RSYI_Sys_Dependencies::is_active( 'students' ) ) {
            self::students_submenu();
        }

        // سجل العمليات
        add_submenu_page(
            'rsyi-system',
            __( 'سجل العمليات', 'rsyi-system' ),
            __( 'سجل العمليات', 'rsyi-system' ),
            'rsyi_sys_view_audit_log',
            'rsyi-audit-log',
            [ self::class, 'render_audit_log' ]
        );
    }

    // ─── Sub-menus ───────────────────────────────────────────────────────────

    private static function hr_submenu(): void {
        $pages = [
            [ 'rsyi-hr-employees',   __( 'الموظفون',           'rsyi-system' ), 'rsyi_hr_view_employees'   ],
            [ 'rsyi-hr-departments', __( 'الأقسام',            'rsyi-system' ), 'rsyi_hr_view_departments' ],
            [ 'rsyi-hr-job-titles',  __( 'المسميات الوظيفية', 'rsyi-system' ), 'rsyi_hr_view_departments' ],
            [ 'rsyi-hr-leaves',      __( 'الإجازات',           'rsyi-system' ), 'rsyi_hr_view_leaves'      ],
            [ 'rsyi-hr-attendance',  __( 'الحضور والانصراف',   'rsyi-system' ), 'rsyi_hr_view_attendance'  ],
            [ 'rsyi-hr-overtime',    __( 'العمل الإضافي',      'rsyi-system' ), 'rsyi_hr_view_overtime'    ],
            [ 'rsyi-hr-violations',  __( 'المخالفات',          'rsyi-system' ), 'rsyi_hr_view_violations'  ],
        ];

        // عنوان قسم (separator trick)
        add_submenu_page( 'rsyi-system', '', '<span class="rsyi-menu-sep">── HR ──</span>', 'rsyi_sys_view_hr', '#rsyi-hr-sep', '__return_false' );

        foreach ( $pages as [ $slug, $title, $cap ] ) {
            add_submenu_page(
                'rsyi-system',
                $title,
                $title,
                $cap,
                $slug,
                [ self::class, 'render_hr_page' ]
            );
        }
    }

    private static function warehouse_submenu(): void {
        $pages = [
            [ 'rsyi-wh-products',          __( 'المنتجات والأصناف',   'rsyi-system' ), 'rsyi_wh_view_products'  ],
            [ 'rsyi-wh-add-orders',         __( 'أوامر الإضافة',       'rsyi-system' ), 'rsyi_wh_manage_orders'  ],
            [ 'rsyi-wh-withdrawal-orders',  __( 'أوامر الصرف',         'rsyi-system' ), 'rsyi_wh_manage_orders'  ],
            [ 'rsyi-wh-purchase-requests',  __( 'طلبات الشراء',        'rsyi-system' ), 'rsyi_wh_view_purchases' ],
            [ 'rsyi-wh-suppliers',          __( 'الموردون',            'rsyi-system' ), 'rsyi_wh_view_suppliers' ],
            [ 'rsyi-wh-reports',            __( 'تقارير المخازن',      'rsyi-system' ), 'rsyi_wh_view_reports'   ],
        ];

        add_submenu_page( 'rsyi-system', '', '<span class="rsyi-menu-sep">── مخازن ──</span>', 'rsyi_sys_view_warehouse', '#rsyi-wh-sep', '__return_false' );

        foreach ( $pages as [ $slug, $title, $cap ] ) {
            add_submenu_page( 'rsyi-system', $title, $title, $cap, $slug, [ self::class, 'render_warehouse_page' ] );
        }
    }

    private static function students_submenu(): void {
        $pages = [
            [ 'rsyi-sa-students',  __( 'قيد الطلاب',          'rsyi-system' ), 'rsyi_sa_view_students'  ],
            [ 'rsyi-sa-documents', __( 'المستندات',            'rsyi-system' ), 'rsyi_sa_view_documents' ],
            [ 'rsyi-sa-permits',   __( 'التصاريح',             'rsyi-system' ), 'rsyi_sa_view_permits'   ],
            [ 'rsyi-sa-behavior',  __( 'السلوك والمخالفات',   'rsyi-system' ), 'rsyi_sa_view_behavior'  ],
            [ 'rsyi-sa-cohorts',   __( 'الدفعات',             'rsyi-system' ), 'rsyi_sa_view_cohorts'   ],
        ];

        add_submenu_page( 'rsyi-system', '', '<span class="rsyi-menu-sep">── طلاب ──</span>', 'rsyi_sa_view_students', '#rsyi-sa-sep', '__return_false' );

        foreach ( $pages as [ $slug, $title, $cap ] ) {
            add_submenu_page( 'rsyi-system', $title, $title, $cap, $slug, [ self::class, 'render_students_page' ] );
        }
    }

    // ─── Assets ──────────────────────────────────────────────────────────────

    public static function enqueue_assets( string $hook ): void {
        // تحميل الـ assets فقط في صفحات RSYI
        if ( strpos( $hook, 'rsyi' ) === false && $hook !== 'toplevel_page_rsyi-system' ) {
            return;
        }

        // Bootstrap 4 RTL
        wp_enqueue_style(
            'rsyi-sys-bootstrap',
            'https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css',
            [],
            '4.2.1'
        );

        // Font Awesome
        wp_enqueue_style(
            'rsyi-sys-fa',
            'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
            [],
            '4.7.0'
        );

        // Admin CSS
        wp_enqueue_style(
            'rsyi-sys-admin',
            RSYI_SYS_URL . 'assets/css/admin.css',
            [ 'rsyi-sys-bootstrap' ],
            RSYI_SYS_VERSION
        );

        // Chart.js
        wp_enqueue_script( 'rsyi-sys-chartjs', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.js', [], '2.7.1', true );

        // Select2
        wp_enqueue_style(  'rsyi-sys-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', [], '4.0.13' );
        wp_enqueue_script( 'rsyi-sys-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', [ 'jquery' ], '4.0.13', true );

        // Admin JS
        wp_enqueue_script(
            'rsyi-sys-admin',
            RSYI_SYS_URL . 'assets/js/admin.js',
            [ 'jquery', 'rsyi-sys-select2', 'rsyi-sys-chartjs' ],
            RSYI_SYS_VERSION,
            true
        );

        // Localize
        wp_localize_script( 'rsyi-sys-admin', 'rsyiSys', [
            'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
            'nonce'     => wp_create_nonce( 'rsyi_sys_admin' ),
            'adminUrl'  => admin_url( 'admin.php' ),
            'unread'    => RSYI_Sys_DB_Installer::unread_count( get_current_user_id() ),
            'i18n'      => [
                'confirm_delete' => __( 'هل أنت متأكد من الحذف؟ لا يمكن التراجع.', 'rsyi-system' ),
                'saving'         => __( 'جارٍ الحفظ...', 'rsyi-system' ),
                'saved'          => __( 'تم الحفظ بنجاح', 'rsyi-system' ),
                'error'          => __( 'حدث خطأ، حاول مجددًا', 'rsyi-system' ),
            ],
        ] );
    }

    // ─── Renderers ───────────────────────────────────────────────────────────

    public static function render_dashboard(): void {
        if ( ! current_user_can( 'rsyi_sys_view_dashboard' ) ) {
            wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
        }
        require_once RSYI_SYS_DIR . 'admin/views/dashboard.php';
    }

    public static function render_hr_page(): void {
        if ( ! current_user_can( 'rsyi_sys_view_hr' ) ) {
            wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
        }
        $page = sanitize_key( $_GET['page'] ?? '' );
        $view = str_replace( 'rsyi-hr-', '', $page );
        $file = RSYI_SYS_DIR . "admin/views/hr/{$view}.php";
        if ( file_exists( $file ) ) {
            require_once $file;
        } else {
            echo '<div class="wrap"><p>' . esc_html__( 'الصفحة غير موجودة.', 'rsyi-system' ) . '</p></div>';
        }
    }

    public static function render_warehouse_page(): void {
        if ( ! current_user_can( 'rsyi_sys_view_warehouse' ) ) {
            wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
        }
        $page = sanitize_key( $_GET['page'] ?? '' );
        $view = str_replace( 'rsyi-wh-', '', $page );
        $file = RSYI_SYS_DIR . "admin/views/warehouse/{$view}.php";
        if ( file_exists( $file ) ) {
            require_once $file;
        } else {
            echo '<div class="wrap"><p>' . esc_html__( 'الصفحة غير موجودة.', 'rsyi-system' ) . '</p></div>';
        }
    }

    public static function render_students_page(): void {
        if ( ! current_user_can( 'rsyi_sa_view_students' ) ) {
            wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
        }
        $page = sanitize_key( $_GET['page'] ?? '' );
        $view = str_replace( 'rsyi-sa-', '', $page );
        $file = RSYI_SYS_DIR . "admin/views/students/{$view}.php";
        if ( file_exists( $file ) ) {
            require_once $file;
        } else {
            echo '<div class="wrap"><p>' . esc_html__( 'الصفحة غير موجودة.', 'rsyi-system' ) . '</p></div>';
        }
    }

    public static function render_audit_log(): void {
        if ( ! current_user_can( 'rsyi_sys_view_audit_log' ) ) {
            wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
        }
        require_once RSYI_SYS_DIR . 'admin/views/audit-log.php';
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
}
