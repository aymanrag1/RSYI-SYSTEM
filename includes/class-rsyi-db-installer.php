<?php
/**
 * RSYI Unified System — Database Installer
 *
 * يُنشئ ويُحدّث جداول قاعدة البيانات الخاصة بالنظام الموحد.
 * الجداول الخاصة بالأنظمة الفرعية (HR، المخازن، الطلاب) تُنشأ
 * من إضافاتها المستقلة؛ هذا المُثبِّت ينشئ فقط جداول اللوحة الموحدة.
 */

defined( 'ABSPATH' ) || exit;

class RSYI_Sys_DB_Installer {

    /** نسخة مخطط قاعدة البيانات — ارفعها عند إضافة أعمدة/جداول. */
    const DB_VERSION = '1.0';

    /** قائمة الجداول التي يملكها هذا البرنامج */
    const TABLES = [
        'rsyi_sys_audit_log',        // سجل العمليات الموحد
        'rsyi_sys_notifications',    // الإشعارات الداخلية
        'rsyi_sys_settings',         // إعدادات النظام
    ];

    // ─── Public API ──────────────────────────────────────────────────────────

    /**
     * تثبيت / تحديث جداول قاعدة البيانات.
     * آمن للاستدعاء عدة مرات (idempotent).
     */
    public static function install(): void {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset = $wpdb->get_charset_collate();

        // ── سجل العمليات الموحد ──────────────────────────────────────────
        dbDelta( "
            CREATE TABLE {$wpdb->prefix}rsyi_sys_audit_log (
                id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id     BIGINT UNSIGNED NOT NULL DEFAULT 0,
                system      VARCHAR(20)  NOT NULL DEFAULT '',
                action      VARCHAR(100) NOT NULL DEFAULT '',
                object_type VARCHAR(50)  NOT NULL DEFAULT '',
                object_id   BIGINT UNSIGNED NOT NULL DEFAULT 0,
                description TEXT,
                ip_address  VARCHAR(45)  NOT NULL DEFAULT '',
                created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY user_id    (user_id),
                KEY system     (system),
                KEY created_at (created_at)
            ) $charset;
        " );

        // ── الإشعارات الداخلية ────────────────────────────────────────────
        dbDelta( "
            CREATE TABLE {$wpdb->prefix}rsyi_sys_notifications (
                id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                recipient_id BIGINT UNSIGNED NOT NULL DEFAULT 0,
                sender_id    BIGINT UNSIGNED NOT NULL DEFAULT 0,
                system       VARCHAR(20)  NOT NULL DEFAULT '',
                type         VARCHAR(50)  NOT NULL DEFAULT '',
                title        VARCHAR(255) NOT NULL DEFAULT '',
                message      TEXT,
                url          VARCHAR(500) NOT NULL DEFAULT '',
                is_read      TINYINT(1)   NOT NULL DEFAULT 0,
                created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY recipient_id (recipient_id),
                KEY is_read      (is_read),
                KEY created_at   (created_at)
            ) $charset;
        " );

        // ── إعدادات النظام الموحد ─────────────────────────────────────────
        dbDelta( "
            CREATE TABLE {$wpdb->prefix}rsyi_sys_settings (
                id           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                setting_key  VARCHAR(100) NOT NULL DEFAULT '',
                setting_val  LONGTEXT,
                autoload     TINYINT(1)   NOT NULL DEFAULT 1,
                updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY setting_key (setting_key)
            ) $charset;
        " );

        update_option( 'rsyi_sys_db_version', self::DB_VERSION );
    }

    /**
     * حذف جداول هذا البرنامج فقط (يُستدعى من uninstall.php).
     */
    public static function uninstall(): void {
        global $wpdb;

        foreach ( self::TABLES as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}{$table}" ); // phpcs:ignore
        }

        delete_option( 'rsyi_sys_version' );
        delete_option( 'rsyi_sys_db_version' );
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * إضافة سجل عملية في جدول audit_log.
     */
    public static function log(
        string $system,
        string $action,
        string $object_type = '',
        int    $object_id   = 0,
        string $description = ''
    ): void {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'rsyi_sys_audit_log',
            [
                'user_id'     => get_current_user_id(),
                'system'      => sanitize_key( $system ),
                'action'      => sanitize_text_field( $action ),
                'object_type' => sanitize_key( $object_type ),
                'object_id'   => absint( $object_id ),
                'description' => sanitize_textarea_field( $description ),
                'ip_address'  => self::get_ip(),
                'created_at'  => current_time( 'mysql' ),
            ],
            [ '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s' ]
        );
    }

    /**
     * استرجاع آخر N سجل من audit_log.
     *
     * @param  int    $limit  عدد السجلات
     * @param  string $system تصفية حسب النظام (اختياري)
     * @return array<object>
     */
    public static function get_recent_logs( int $limit = 20, string $system = '' ): array {
        global $wpdb;

        $table = $wpdb->prefix . 'rsyi_sys_audit_log';

        if ( $system ) {
            return $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT * FROM {$table} WHERE system = %s ORDER BY created_at DESC LIMIT %d",
                    $system,
                    $limit
                )
            );
        }

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d",
                $limit
            )
        );
    }

    /**
     * إرسال إشعار داخلي لمستخدم.
     */
    public static function notify(
        int    $recipient_id,
        string $system,
        string $type,
        string $title,
        string $message = '',
        string $url     = ''
    ): void {
        global $wpdb;

        $wpdb->insert(
            $wpdb->prefix . 'rsyi_sys_notifications',
            [
                'recipient_id' => $recipient_id,
                'sender_id'    => get_current_user_id(),
                'system'       => sanitize_key( $system ),
                'type'         => sanitize_key( $type ),
                'title'        => sanitize_text_field( $title ),
                'message'      => sanitize_textarea_field( $message ),
                'url'          => esc_url_raw( $url ),
                'created_at'   => current_time( 'mysql' ),
            ],
            [ '%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s' ]
        );
    }

    /**
     * عدد الإشعارات غير المقروءة لمستخدم.
     */
    public static function unread_count( int $user_id ): int {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}rsyi_sys_notifications
                 WHERE recipient_id = %d AND is_read = 0",
                $user_id
            )
        );
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    private static function get_ip(): string {
        foreach ( [ 'HTTP_X_REAL_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' ] as $key ) {
            if ( ! empty( $_SERVER[ $key ] ) ) {
                return sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
            }
        }
        return '';
    }
}
