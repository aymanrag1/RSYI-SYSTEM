<?php
/**
 * RSYI Unified System — Uninstall
 *
 * يُنفَّذ عند حذف الإضافة من لوحة تحكم WordPress.
 * يحذف جداول قاعدة البيانات والخيارات الخاصة بهذه الإضافة فقط.
 * بيانات الأنظمة الفرعية (HR، المخازن، الطلاب) لا تُحذف من هنا.
 */

// منع الاستدعاء المباشر
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// تحميل الـ DB Installer
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rsyi-db-installer.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-rsyi-roles.php';

// حذف الجداول
RSYI_Sys_DB_Installer::uninstall();

// حذف الصلاحيات المضافة على الأدوار
RSYI_Sys_Roles::remove_caps();

// حذف الخيارات
delete_option( 'rsyi_sys_version' );
delete_option( 'rsyi_sys_db_version' );
delete_option( 'rsyi_sys_options' );
