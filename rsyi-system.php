<?php
/**
 * Plugin Name:       RSYI Unified Management System
 * Plugin URI:        https://github.com/aymanrag1/RSYI-SYSTEM
 * Description:       لوحة التحكم الموحدة لمعهد البحر الأحمر للسياحة البحرية — تدمج نظام الموارد البشرية ونظام المخازن وشئون الطلاب في واجهة إدارية موحدة.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.1
 * Author:            AYMAN RAGAB
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rsyi-system
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit;

// ─── Constants ───────────────────────────────────────────────────────────────

define( 'RSYI_SYS_VERSION',  '1.0.0' );
define( 'RSYI_SYS_DIR',      plugin_dir_path( __FILE__ ) );
define( 'RSYI_SYS_URL',      plugin_dir_url( __FILE__ ) );
define( 'RSYI_SYS_BASENAME', plugin_basename( __FILE__ ) );

// ─── Autoloader ──────────────────────────────────────────────────────────────

spl_autoload_register( function ( string $class ): void {
    $map = [
        'RSYI_Sys_DB_Installer'  => 'includes/class-rsyi-db-installer.php',
        'RSYI_Sys_Roles'         => 'includes/class-rsyi-roles.php',
        'RSYI_Sys_Settings'      => 'includes/class-rsyi-settings.php',
        'RSYI_Sys_Dependencies'  => 'includes/class-rsyi-dependencies.php',
        'RSYI_Sys_Admin'         => 'admin/class-rsyi-admin.php',
        'RSYI_Sys_Portal'        => 'portal/class-rsyi-portal.php',
    ];
    if ( isset( $map[ $class ] ) ) {
        require_once RSYI_SYS_DIR . $map[ $class ];
    }
} );

// ─── Activation / Deactivation ───────────────────────────────────────────────

register_activation_hook( __FILE__, function (): void {
    RSYI_Sys_DB_Installer::install();
    RSYI_Sys_Roles::add_roles();
    update_option( 'rsyi_sys_version', RSYI_SYS_VERSION );
} );

register_deactivation_hook( __FILE__, function (): void {
    // نحتفظ بالبيانات عند إلغاء التفعيل — تُحذف فقط عند إلغاء التثبيت
} );

// ─── Bootstrap ───────────────────────────────────────────────────────────────

add_action( 'plugins_loaded', 'rsyi_sys_init' );

/**
 * تهيئة البرنامج الموحد بعد تحميل كل الإضافات.
 */
function rsyi_sys_init(): void {

    // تحميل اللغات
    load_plugin_textdomain(
        'rsyi-system',
        false,
        dirname( RSYI_SYS_BASENAME ) . '/languages'
    );

    // التحقق من الإصدار وتحديث قاعدة البيانات إذا لزم
    if ( get_option( 'rsyi_sys_version' ) !== RSYI_SYS_VERSION ) {
        RSYI_Sys_DB_Installer::install();
        RSYI_Sys_Roles::sync_roles();
        update_option( 'rsyi_sys_version', RSYI_SYS_VERSION );
    }

    // التحقق من توافر الأنظمة الفرعية
    RSYI_Sys_Dependencies::check();

    // تهيئة الإعدادات
    RSYI_Sys_Settings::init();

    // تهيئة واجهة المشرف
    if ( is_admin() ) {
        RSYI_Sys_Admin::init();
    }

    // تهيئة البوابة (Shortcodes للموظفين والطلاب)
    if ( ! is_admin() ) {
        RSYI_Sys_Portal::init();
    }
}
