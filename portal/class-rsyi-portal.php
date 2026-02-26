<?php
/**
 * RSYI Unified System — Portal (Frontend Shortcodes)
 *
 * يُتيح للموظفين والطلاب الوصول لبياناتهم عبر الموقع الأمامي
 * باستخدام shortcodes.
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_Portal {

    // ─── Init ────────────────────────────────────────────────────────────────

    public static function init(): void {
        add_shortcode( 'rsyi_dashboard',        [ self::class, 'sc_dashboard'        ] );
        add_shortcode( 'rsyi_my_leaves',        [ self::class, 'sc_my_leaves'        ] );
        add_shortcode( 'rsyi_my_attendance',    [ self::class, 'sc_my_attendance'    ] );
        add_shortcode( 'rsyi_student_profile',  [ self::class, 'sc_student_profile'  ] );
        add_shortcode( 'rsyi_submit_permit',    [ self::class, 'sc_submit_permit'    ] );

        add_action( 'wp_enqueue_scripts', [ self::class, 'enqueue_frontend_assets' ] );
    }

    // ─── Shortcodes ──────────────────────────────────────────────────────────

    /**
     * [rsyi_dashboard] — لوحة بيانات شخصية للمستخدم المسجّل
     */
    public static function sc_dashboard( array $atts ): string {
        if ( ! is_user_logged_in() ) {
            return '<p class="rsyi-notice">' . esc_html__( 'يجب تسجيل الدخول لعرض هذه الصفحة.', 'rsyi-system' ) . '</p>';
        }

        ob_start();
        require_once RSYI_SYS_DIR . 'portal/views/dashboard.php';
        return ob_get_clean();
    }

    /**
     * [rsyi_my_leaves] — إجازات الموظف الحالي
     */
    public static function sc_my_leaves( array $atts ): string {
        if ( ! is_user_logged_in() || ! current_user_can( 'rsyi_hr_view_leaves' ) ) {
            return '<p class="rsyi-notice">' . esc_html__( 'غير مصرح.', 'rsyi-system' ) . '</p>';
        }

        ob_start();
        require_once RSYI_SYS_DIR . 'portal/views/my-leaves.php';
        return ob_get_clean();
    }

    /**
     * [rsyi_my_attendance] — سجل حضور الموظف
     */
    public static function sc_my_attendance( array $atts ): string {
        if ( ! is_user_logged_in() || ! current_user_can( 'rsyi_hr_view_attendance' ) ) {
            return '<p class="rsyi-notice">' . esc_html__( 'غير مصرح.', 'rsyi-system' ) . '</p>';
        }

        ob_start();
        require_once RSYI_SYS_DIR . 'portal/views/my-attendance.php';
        return ob_get_clean();
    }

    /**
     * [rsyi_student_profile] — ملف الطالب
     */
    public static function sc_student_profile( array $atts ): string {
        if ( ! is_user_logged_in() || ! current_user_can( 'rsyi_sa_view_students' ) ) {
            return '<p class="rsyi-notice">' . esc_html__( 'غير مصرح.', 'rsyi-system' ) . '</p>';
        }

        ob_start();
        require_once RSYI_SYS_DIR . 'portal/views/student-profile.php';
        return ob_get_clean();
    }

    /**
     * [rsyi_submit_permit] — نموذج طلب تصريح
     */
    public static function sc_submit_permit( array $atts ): string {
        if ( ! is_user_logged_in() || ! current_user_can( 'rsyi_sa_view_permits' ) ) {
            return '<p class="rsyi-notice">' . esc_html__( 'غير مصرح.', 'rsyi-system' ) . '</p>';
        }

        ob_start();
        require_once RSYI_SYS_DIR . 'portal/views/submit-permit.php';
        return ob_get_clean();
    }

    // ─── Frontend Assets ─────────────────────────────────────────────────────

    public static function enqueue_frontend_assets(): void {
        // نحمّل الأصول فقط في الصفحات التي تحتوي على shortcodes
        global $post;
        if ( ! $post ) {
            return;
        }

        $shortcodes = [ 'rsyi_dashboard', 'rsyi_my_leaves', 'rsyi_my_attendance', 'rsyi_student_profile', 'rsyi_submit_permit' ];
        $has_shortcode = false;

        foreach ( $shortcodes as $sc ) {
            if ( has_shortcode( $post->post_content, $sc ) ) {
                $has_shortcode = true;
                break;
            }
        }

        if ( ! $has_shortcode ) {
            return;
        }

        wp_enqueue_style(
            'rsyi-sys-portal',
            RSYI_SYS_URL . 'assets/css/portal.css',
            [],
            RSYI_SYS_VERSION
        );

        wp_enqueue_script(
            'rsyi-sys-portal',
            RSYI_SYS_URL . 'assets/js/portal.js',
            [ 'jquery' ],
            RSYI_SYS_VERSION,
            true
        );

        wp_localize_script( 'rsyi-sys-portal', 'rsyiPortal', [
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'rsyi_sys_portal' ),
        ] );
    }
}
