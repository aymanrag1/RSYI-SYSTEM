<?php
/**
 * RSYI Unified System — Settings Manager
 *
 * يُسجِّل ويُدير إعدادات النظام الموحد ويعرض صفحة الإعدادات
 * في لوحة الإدارة.
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_Settings {

    const OPTION_GROUP = 'rsyi_sys_settings';
    const OPTION_PAGE  = 'rsyi-system-settings';

    /** الإعدادات الافتراضية */
    const DEFAULTS = [
        'institute_name'     => 'معهد البحر الأحمر للسياحة البحرية',
        'institute_name_en'  => 'Red Sea Yacht Institute',
        'institute_logo_url' => '',
        'default_language'   => 'ar',
        'date_format'        => 'Y-m-d',
        'timezone'           => 'Africa/Cairo',
        'hr_enabled'         => '1',
        'warehouse_enabled'  => '1',
        'students_enabled'   => '1',
        'audit_log_enabled'  => '1',
        'notifications_enabled' => '1',
        'items_per_page'     => '25',
        'admin_email'        => '',
    ];

    // ─── Init ────────────────────────────────────────────────────────────────

    public static function init(): void {
        add_action( 'admin_init', [ self::class, 'register' ] );
        add_action( 'admin_menu', [ self::class, 'add_settings_page' ], 99 );
    }

    // ─── Registration ────────────────────────────────────────────────────────

    public static function register(): void {

        register_setting(
            self::OPTION_GROUP,
            'rsyi_sys_options',
            [
                'sanitize_callback' => [ self::class, 'sanitize' ],
                'default'           => self::DEFAULTS,
            ]
        );

        // ── قسم: معلومات المعهد ─────────────────────────────────────────
        add_settings_section(
            'rsyi_sys_institute',
            __( 'معلومات المعهد', 'rsyi-system' ),
            null,
            self::OPTION_PAGE
        );

        self::field( 'institute_name',    __( 'اسم المعهد (عربي)',    'rsyi-system' ), 'rsyi_sys_institute', 'text' );
        self::field( 'institute_name_en', __( 'اسم المعهد (إنجليزي)', 'rsyi-system' ), 'rsyi_sys_institute', 'text' );
        self::field( 'institute_logo_url', __( 'رابط الشعار',          'rsyi-system' ), 'rsyi_sys_institute', 'url' );
        self::field( 'admin_email',       __( 'البريد الإلكتروني',     'rsyi-system' ), 'rsyi_sys_institute', 'email' );

        // ── قسم: الأنظمة المُفعَّلة ──────────────────────────────────────
        add_settings_section(
            'rsyi_sys_modules',
            __( 'الأنظمة المُفعَّلة', 'rsyi-system' ),
            null,
            self::OPTION_PAGE
        );

        self::field( 'hr_enabled',        __( 'نظام الموارد البشرية', 'rsyi-system' ), 'rsyi_sys_modules', 'checkbox' );
        self::field( 'warehouse_enabled', __( 'نظام المخازن',         'rsyi-system' ), 'rsyi_sys_modules', 'checkbox' );
        self::field( 'students_enabled',  __( 'نظام شئون الطلاب',     'rsyi-system' ), 'rsyi_sys_modules', 'checkbox' );

        // ── قسم: الإعدادات العامة ────────────────────────────────────────
        add_settings_section(
            'rsyi_sys_general',
            __( 'الإعدادات العامة', 'rsyi-system' ),
            null,
            self::OPTION_PAGE
        );

        self::field( 'default_language',       __( 'اللغة الافتراضية', 'rsyi-system' ), 'rsyi_sys_general', 'select',
            [ 'ar' => 'العربية', 'en' => 'English' ] );
        self::field( 'items_per_page',         __( 'عدد العناصر في الصفحة', 'rsyi-system' ), 'rsyi_sys_general', 'number' );
        self::field( 'audit_log_enabled',      __( 'تفعيل سجل العمليات',    'rsyi-system' ), 'rsyi_sys_general', 'checkbox' );
        self::field( 'notifications_enabled',  __( 'تفعيل الإشعارات',       'rsyi-system' ), 'rsyi_sys_general', 'checkbox' );
    }

    // ─── Getters ─────────────────────────────────────────────────────────────

    /**
     * الحصول على قيمة إعداد واحد.
     */
    public static function get( string $key, mixed $default = null ): mixed {
        $options = get_option( 'rsyi_sys_options', self::DEFAULTS );
        return $options[ $key ] ?? ( $default ?? self::DEFAULTS[ $key ] ?? '' );
    }

    /**
     * تحديث قيمة إعداد واحد.
     */
    public static function set( string $key, mixed $value ): void {
        $options         = get_option( 'rsyi_sys_options', self::DEFAULTS );
        $options[ $key ] = $value;
        update_option( 'rsyi_sys_options', $options );
    }

    // ─── Page ────────────────────────────────────────────────────────────────

    public static function add_settings_page(): void {
        add_submenu_page(
            'rsyi-system',
            __( 'إعدادات النظام الموحد', 'rsyi-system' ),
            __( 'الإعدادات', 'rsyi-system' ),
            'rsyi_sys_manage_settings',
            self::OPTION_PAGE,
            [ self::class, 'render_page' ]
        );
    }

    public static function render_page(): void {
        if ( ! current_user_can( 'rsyi_sys_manage_settings' ) ) {
            wp_die( esc_html__( 'غير مصرح لك بالوصول لهذه الصفحة.', 'rsyi-system' ) );
        }
        require_once RSYI_SYS_DIR . 'admin/views/settings.php';
    }

    // ─── Sanitization ────────────────────────────────────────────────────────

    public static function sanitize( array $input ): array {
        $clean = [];

        $text_fields     = [ 'institute_name', 'institute_name_en', 'date_format', 'timezone' ];
        $email_fields    = [ 'admin_email' ];
        $url_fields      = [ 'institute_logo_url' ];
        $number_fields   = [ 'items_per_page' ];
        $checkbox_fields = [ 'hr_enabled', 'warehouse_enabled', 'students_enabled', 'audit_log_enabled', 'notifications_enabled' ];
        $select_fields   = [ 'default_language' ];

        foreach ( $text_fields as $f ) {
            $clean[ $f ] = sanitize_text_field( $input[ $f ] ?? '' );
        }
        foreach ( $email_fields as $f ) {
            $clean[ $f ] = sanitize_email( $input[ $f ] ?? '' );
        }
        foreach ( $url_fields as $f ) {
            $clean[ $f ] = esc_url_raw( $input[ $f ] ?? '' );
        }
        foreach ( $number_fields as $f ) {
            $clean[ $f ] = (string) absint( $input[ $f ] ?? 25 );
        }
        foreach ( $checkbox_fields as $f ) {
            $clean[ $f ] = empty( $input[ $f ] ) ? '0' : '1';
        }
        foreach ( $select_fields as $f ) {
            $clean[ $f ] = sanitize_key( $input[ $f ] ?? 'ar' );
        }

        return $clean;
    }

    // ─── Private Helper ──────────────────────────────────────────────────────

    private static function field(
        string $id,
        string $label,
        string $section,
        string $type    = 'text',
        array  $options = []
    ): void {
        add_settings_field(
            'rsyi_sys_' . $id,
            $label,
            [ self::class, 'render_field' ],
            self::OPTION_PAGE,
            $section,
            [ 'id' => $id, 'type' => $type, 'options' => $options ]
        );
    }

    public static function render_field( array $args ): void {
        $id      = $args['id'];
        $type    = $args['type'];
        $opts    = $args['options'] ?? [];
        $value   = self::get( $id );
        $name    = "rsyi_sys_options[{$id}]";

        switch ( $type ) {
            case 'checkbox':
                printf(
                    '<input type="checkbox" name="%s" id="%s" value="1" %s>',
                    esc_attr( $name ),
                    esc_attr( $id ),
                    checked( '1', $value, false )
                );
                break;

            case 'select':
                echo '<select name="' . esc_attr( $name ) . '" id="' . esc_attr( $id ) . '">';
                foreach ( $opts as $val => $label ) {
                    printf(
                        '<option value="%s" %s>%s</option>',
                        esc_attr( $val ),
                        selected( $value, $val, false ),
                        esc_html( $label )
                    );
                }
                echo '</select>';
                break;

            default:
                printf(
                    '<input type="%s" name="%s" id="%s" value="%s" class="regular-text">',
                    esc_attr( $type ),
                    esc_attr( $name ),
                    esc_attr( $id ),
                    esc_attr( (string) $value )
                );
        }
    }
}
