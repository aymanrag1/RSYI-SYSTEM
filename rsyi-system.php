<?php
/**
 * Plugin Name:       RSYI Unified Management System | النظام الإداري الموحد
 * Plugin URI:        https://github.com/aymanrag1/RSYI-SYSTEM
 * Description:       النظام الإداري الموحد لمعهد البحر الأحمر للسياحة البحرية — يدمج الموارد البشرية وشئون الطلاب والمخازن في واجهة واحدة. | Unified management system for Red Sea Yacht Institute — integrates HR, Student Affairs, and Warehouse in one interface.
 * Version:           2.0.0
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

define( 'RSYI_SYS_VERSION',  '2.0.1' );
define( 'RSYI_SYS_DIR',      plugin_dir_path( __FILE__ ) );
define( 'RSYI_SYS_URL',      plugin_dir_url( __FILE__ ) );
define( 'RSYI_SYS_BASENAME', plugin_basename( __FILE__ ) );

// ثوابت الوحدات الفرعية — تُعرَّف هنا لتكون متاحة لكلاسات الوحدات
// Sub-module constants — defined here so module classes resolve their paths
if ( ! defined( 'RSYI_HR_DIR' ) ) {
    define( 'RSYI_HR_DIR', RSYI_SYS_DIR . 'modules/hr/' );
}
if ( ! defined( 'RSYI_HR_URL' ) ) {
    define( 'RSYI_HR_URL', RSYI_SYS_URL . 'modules/hr/' );
}
if ( ! defined( 'IW_PLUGIN_DIR' ) ) {
    define( 'IW_PLUGIN_DIR', RSYI_SYS_DIR . 'modules/warehouse/' );
}
if ( ! defined( 'IW_PLUGIN_URL' ) ) {
    define( 'IW_PLUGIN_URL', RSYI_SYS_URL . 'modules/warehouse/' );
}
if ( ! defined( 'RSYI_SA_DIR' ) ) {
    define( 'RSYI_SA_DIR', RSYI_SYS_DIR . 'modules/students/' );
}
if ( ! defined( 'RSYI_SA_URL' ) ) {
    define( 'RSYI_SA_URL', RSYI_SYS_URL . 'modules/students/' );
}

// ─── Autoloader ──────────────────────────────────────────────────────────────

spl_autoload_register( function ( string $class ): void {
    $map = [
        // Core unified system
        'RSYI_Sys_DB_Installer'   => 'includes/class-rsyi-db-installer.php',
        'RSYI_Sys_Roles'          => 'includes/class-rsyi-roles.php',
        'RSYI_Sys_Settings'       => 'includes/class-rsyi-settings.php',
        'RSYI_Sys_Module_Loader'  => 'includes/class-rsyi-module-loader.php',
        'RSYI_Sys_Admin'          => 'admin/class-rsyi-admin.php',
    ];
    if ( isset( $map[ $class ] ) ) {
        require_once RSYI_SYS_DIR . $map[ $class ];
    }
} );

// ─── Activation ──────────────────────────────────────────────────────────────

register_activation_hook( __FILE__, function (): void {
    try {
        // تثبيت جداول النظام الموحد فقط | Install ONLY unified system tables (safe)
        require_once RSYI_SYS_DIR . 'includes/class-rsyi-db-installer.php';
        RSYI_Sys_DB_Installer::install();

        // الإعدادات الافتراضية | Default options
        require_once RSYI_SYS_DIR . 'includes/class-rsyi-settings.php';
        if ( ! get_option( 'rsyi_sys_options' ) ) {
            update_option( 'rsyi_sys_options', RSYI_Sys_Settings::DEFAULTS );
        }

        update_option( 'rsyi_sys_version', RSYI_SYS_VERSION );
        // تمييز أن وحدات الموارد لم تُثبَّت بعد (تُثبَّت عند أول تشغيل)
        // Flag: sub-module DB tables not yet installed (done on first run)
        delete_option( 'rsyi_sys_modules_installed' );

    } catch ( \Throwable $e ) {
        // تسجيل الخطأ الفادح لتسهيل التشخيص | Log for diagnosis
        // phpcs:ignore WordPress.PHP.DevelopmentFunctions
        error_log( 'RSYI activation error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() );
        // تخزين في Option لعرضه في لوحة التحكم
        update_option( 'rsyi_sys_activation_error', $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() );
        throw $e; // re-throw so WP still reports the activation failure
    }
} );

register_deactivation_hook( __FILE__, function (): void {
    // لا نحذف البيانات عند إلغاء التفعيل | Keep data on deactivation
} );

// ─── Bootstrap ───────────────────────────────────────────────────────────────

// تهيئة عند priority 20 — بعد تحميل جميع البلاجنز الأخرى
// Init at priority 20 — after all other plugins have loaded their classes
// This prevents class redeclaration conflicts with legacy sub-plugins.
add_action( 'plugins_loaded', 'rsyi_sys_init', 20 );

/**
 * تهيئة النظام الموحد | Bootstrap the unified system.
 */
function rsyi_sys_init(): void {

    // تحميل اللغات | Load text domain
    load_plugin_textdomain(
        'rsyi-system',
        false,
        dirname( RSYI_SYS_BASENAME ) . '/languages'
    );

    // تحميل الوحدات المفعَّلة | Load enabled modules
    RSYI_Sys_Module_Loader::load();

    // تثبيت جداول الوحدات الفرعية عند أول تشغيل بعد التفعيل
    // Install sub-module DB tables on first run after activation
    if ( ! get_option( 'rsyi_sys_modules_installed' ) ) {
        RSYI_Sys_DB_Installer::install_modules();
        require_once RSYI_SYS_DIR . 'includes/class-rsyi-roles.php';
        RSYI_Sys_Roles::add_roles();
        update_option( 'rsyi_sys_modules_installed', '1' );
    }

    // تحديث قاعدة البيانات عند ترقية النسخة | DB upgrade on version bump
    if ( get_option( 'rsyi_sys_version' ) !== RSYI_SYS_VERSION ) {
        RSYI_Sys_DB_Installer::install();
        RSYI_Sys_DB_Installer::install_modules();
        RSYI_Sys_Roles::sync_roles();
        update_option( 'rsyi_sys_version', RSYI_SYS_VERSION );
    }

    // تهيئة الإعدادات | Init settings
    RSYI_Sys_Settings::init();

    // عرض خطأ التفعيل إن وجد | Display activation error if any (admin only)
    if ( is_admin() ) {
        $act_err = get_option( 'rsyi_sys_activation_error' );
        if ( $act_err ) {
            add_action( 'admin_notices', function () use ( $act_err ) {
                echo '<div class="notice notice-error"><p><strong>RSYI System — خطأ التفعيل:</strong> '
                    . esc_html( $act_err ) . '</p></div>';
            } );
        }
    }

    // تهيئة واجهة الإدارة | Init admin
    if ( is_admin() ) {
        RSYI_Sys_Admin::init();
    }

    // تهيئة بوابة شئون الطلاب | Init Student Affairs portal shortcodes
    if ( ! is_admin() && RSYI_Sys_Module_Loader::is_loaded( 'students' ) ) {
        if ( class_exists( 'RSYI_SA\Shortcodes' ) ) {
            \RSYI_SA\Shortcodes::init();
        }
    }
}
