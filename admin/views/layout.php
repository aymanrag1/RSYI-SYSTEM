<?php
/**
 * RSYI Unified Admin Layout — Tabbed Interface
 * واجهة الإدارة الموحدة بالتبويبات
 */
defined( 'ABSPATH' ) || exit;

// تحديد الوحدة النشطة والتبويب الفرعي | Detect active module & sub-tab
$current_page   = sanitize_key( $_GET['page'] ?? 'rsyi-system' );
$current_tab    = sanitize_key( $_GET['tab']  ?? '' );

$modules_map = [
    'rsyi-system'     => 'dashboard',
    'rsyi-hr'         => 'hr',
    'rsyi-students'   => 'students',
    'rsyi-warehouse'  => 'warehouse',
    'rsyi-accounting' => 'accounting',
    'rsyi-settings'   => 'settings',
];
$active_module = $modules_map[ $current_page ] ?? 'dashboard';

// التبويبات الفرعية لكل وحدة | Sub-tabs per module
$sub_tabs = [
    'dashboard' => [],
    'hr' => [
        'employees'    => [ 'ar' => 'الموظفون',          'en' => 'Employees',    'icon' => 'fa-users' ],
        'departments'  => [ 'ar' => 'الأقسام',           'en' => 'Departments',  'icon' => 'fa-sitemap' ],
        'job-titles'   => [ 'ar' => 'المسميات الوظيفية', 'en' => 'Job Titles',   'icon' => 'fa-briefcase' ],
        'leaves'       => [ 'ar' => 'الإجازات',          'en' => 'Leaves',       'icon' => 'fa-calendar-days' ],
        'attendance'   => [ 'ar' => 'الحضور والانصراف',  'en' => 'Attendance',   'icon' => 'fa-clock' ],
        'overtime'     => [ 'ar' => 'العمل الإضافي',     'en' => 'Overtime',     'icon' => 'fa-hourglass-half' ],
        'violations'   => [ 'ar' => 'المخالفات',         'en' => 'Violations',   'icon' => 'fa-triangle-exclamation' ],
        'leave-balance'=> [ 'ar' => 'أرصدة الإجازات',   'en' => 'Leave Balance','icon' => 'fa-chart-pie' ],
        'permissions'  => [ 'ar' => 'الصلاحيات',         'en' => 'Permissions',  'icon' => 'fa-shield-halved' ],
    ],
    'students' => [
        'students'  => [ 'ar' => 'قيد الطلاب',         'en' => 'Students',     'icon' => 'fa-user-graduate' ],
        'documents' => [ 'ar' => 'المستندات',           'en' => 'Documents',    'icon' => 'fa-file-alt' ],
        'permits'   => [ 'ar' => 'التصاريح',            'en' => 'Permits',      'icon' => 'fa-id-card' ],
        'behavior'  => [ 'ar' => 'السلوك والمخالفات',  'en' => 'Behavior',     'icon' => 'fa-flag' ],
        'cohorts'   => [ 'ar' => 'الدفعات',            'en' => 'Cohorts',      'icon' => 'fa-layer-group' ],
    ],
    'warehouse' => [
        'products'          => [ 'ar' => 'الأصناف',       'en' => 'Products',          'icon' => 'fa-boxes-stacked' ],
        'add-stock'         => [ 'ar' => 'إضافة مخزون',  'en' => 'Add Stock',         'icon' => 'fa-circle-plus' ],
        'withdraw-stock'    => [ 'ar' => 'صرف مخزون',    'en' => 'Withdraw',          'icon' => 'fa-circle-minus' ],
        'purchase-requests' => [ 'ar' => 'طلبات الشراء', 'en' => 'Purchase Requests', 'icon' => 'fa-cart-shopping' ],
        'suppliers'         => [ 'ar' => 'الموردون',      'en' => 'Suppliers',         'icon' => 'fa-truck' ],
        'categories'        => [ 'ar' => 'التصنيفات',    'en' => 'Categories',        'icon' => 'fa-tags' ],
        'reports'           => [ 'ar' => 'التقارير',      'en' => 'Reports',           'icon' => 'fa-chart-bar' ],
    ],
    'accounting' => [],
    'settings'   => [],
];

// أول تبويب افتراضي | Default first sub-tab
if ( empty( $current_tab ) && ! empty( $sub_tabs[ $active_module ] ) ) {
    $current_tab = array_key_first( $sub_tabs[ $active_module ] );
}

// الوحدات الرئيسية | Main module tabs
$main_tabs = [
    'dashboard'  => [
        'ar'    => 'لوحة التحكم',
        'en'    => 'Dashboard',
        'icon'  => 'fa-gauge-high',
        'page'  => 'rsyi-system',
        'active'=> true,
    ],
    'hr' => [
        'ar'    => 'الموارد البشرية',
        'en'    => 'Human Resources',
        'icon'  => 'fa-users-gear',
        'page'  => 'rsyi-hr',
        'active'=> RSYI_Sys_Module_Loader::is_loaded( 'hr' ),
    ],
    'students' => [
        'ar'    => 'شئون الطلاب',
        'en'    => 'Student Affairs',
        'icon'  => 'fa-user-graduate',
        'page'  => 'rsyi-students',
        'active'=> RSYI_Sys_Module_Loader::is_loaded( 'students' ),
    ],
    'warehouse' => [
        'ar'    => 'المخازن',
        'en'    => 'Warehouse',
        'icon'  => 'fa-warehouse',
        'page'  => 'rsyi-warehouse',
        'active'=> RSYI_Sys_Module_Loader::is_loaded( 'warehouse' ),
    ],
    'accounting' => [
        'ar'    => 'الحسابات',
        'en'    => 'Accounting',
        'icon'  => 'fa-calculator',
        'page'  => 'rsyi-accounting',
        'active'=> false, // قيد الإنشاء | Coming soon
        'soon'  => true,
    ],
    'settings' => [
        'ar'    => 'الإعدادات',
        'en'    => 'Settings',
        'icon'  => 'fa-gear',
        'page'  => 'rsyi-settings',
        'active'=> true,
    ],
];

$institute_name = RSYI_Sys_Settings::get( 'institute_name', 'معهد البحر الأحمر' );
$logo_url       = RSYI_Sys_Settings::get( 'institute_logo_url', '' );
$unread         = RSYI_Sys_DB_Installer::unread_count( get_current_user_id() );
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo esc_html( $institute_name ); ?> — RSYI System</title>
</head>
<body class="rsyi-admin-body">

<div class="rsyi-wrap" id="rsyi-app">

    <!-- ═══ TOPBAR ══════════════════════════════════════════════════════════ -->
    <nav class="rsyi-topbar d-flex align-items-center justify-content-between px-4 py-2">

        <div class="rsyi-topbar-brand d-flex align-items-center gap-3">
            <?php if ( $logo_url ) : ?>
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="Logo" class="rsyi-logo">
            <?php else : ?>
                <span class="rsyi-logo-icon"><i class="fa-solid fa-anchor"></i></span>
            <?php endif; ?>
            <div>
                <div class="rsyi-brand-name"><?php echo esc_html( $institute_name ); ?></div>
                <div class="rsyi-brand-sub">RSYI Unified Management System | النظام الإداري الموحد</div>
            </div>
        </div>

        <div class="rsyi-topbar-actions d-flex align-items-center gap-3">
            <!-- إشعارات | Notifications -->
            <div class="dropdown rsyi-notif-dropdown">
                <button class="rsyi-icon-btn position-relative" data-bs-toggle="dropdown" aria-expanded="false" title="الإشعارات | Notifications">
                    <i class="fa-regular fa-bell fs-5"></i>
                    <?php if ( $unread > 0 ) : ?>
                        <span class="rsyi-badge-dot"><?php echo esc_html( $unread ); ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu dropdown-menu-end rsyi-notif-panel p-0">
                    <div class="rsyi-notif-header px-3 py-2 d-flex justify-content-between align-items-center">
                        <span class="fw-bold">الإشعارات | Notifications</span>
                        <a href="#" class="rsyi-mark-all-read small">تعيين كمقروء | Mark all read</a>
                    </div>
                    <div class="rsyi-notif-list" id="rsyi-notif-list">
                        <div class="rsyi-notif-empty px-3 py-4 text-center text-muted small">
                            <i class="fa-regular fa-bell-slash mb-2 d-block fs-4"></i>
                            لا توجد إشعارات | No notifications
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات المستخدم | User info -->
            <div class="dropdown">
                <button class="rsyi-user-btn d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                    <?php echo get_avatar( get_current_user_id(), 32, '', '', [ 'class' => 'rsyi-avatar' ] ); ?>
                    <span class="rsyi-username d-none d-md-inline"><?php echo esc_html( wp_get_current_user()->display_name ); ?></span>
                    <i class="fa-solid fa-chevron-down small"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?php echo esc_url( admin_url( 'profile.php' ) ); ?>">
                        <i class="fa-regular fa-user me-2"></i>الملف الشخصي | Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?php echo esc_url( wp_logout_url( admin_url() ) ); ?>">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>تسجيل الخروج | Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- ═══ MAIN MODULE TABS ════════════════════════════════════════════════ -->
    <div class="rsyi-module-tabs" role="tablist">
        <?php foreach ( $main_tabs as $key => $tab ) :
            $is_current = ( $active_module === $key );
            $tab_url    = admin_url( 'admin.php?page=' . $tab['page'] );
            $disabled   = ! empty( $tab['soon'] );
        ?>
            <a href="<?php echo $disabled ? '#' : esc_url( $tab_url ); ?>"
               class="rsyi-module-tab <?php echo $is_current ? 'active' : ''; ?> <?php echo $disabled ? 'disabled' : ''; ?>"
               title="<?php echo esc_attr( $tab['ar'] . ' | ' . $tab['en'] ); ?>"
               <?php echo $disabled ? 'aria-disabled="true"' : ''; ?>>
                <i class="fa-solid <?php echo esc_attr( $tab['icon'] ); ?>"></i>
                <span class="rsyi-tab-label-ar"><?php echo esc_html( $tab['ar'] ); ?></span>
                <span class="rsyi-tab-label-en"><?php echo esc_html( $tab['en'] ); ?></span>
                <?php if ( $disabled ) : ?>
                    <span class="rsyi-tab-soon">قريباً | Soon</span>
                <?php endif; ?>
                <?php if ( ! $tab['active'] && ! $disabled ) : ?>
                    <span class="rsyi-tab-off" title="معطّل | Disabled"><i class="fa-solid fa-circle-xmark"></i></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- ═══ SUB-TABS (if module has sub-tabs) ═══════════════════════════════ -->
    <?php if ( ! empty( $sub_tabs[ $active_module ] ) ) : ?>
        <div class="rsyi-sub-tabs" role="tablist">
            <?php foreach ( $sub_tabs[ $active_module ] as $stab_key => $stab ) :
                $is_active = ( $current_tab === $stab_key );
                $stab_url  = admin_url( 'admin.php?page=' . $main_tabs[ $active_module ]['page'] . '&tab=' . $stab_key );
            ?>
                <a href="<?php echo esc_url( $stab_url ); ?>"
                   class="rsyi-sub-tab <?php echo $is_active ? 'active' : ''; ?>">
                    <i class="fa-solid <?php echo esc_attr( $stab['icon'] ); ?>"></i>
                    <span><?php echo esc_html( $stab['ar'] ); ?></span>
                    <small class="rsyi-en"><?php echo esc_html( $stab['en'] ); ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ═══ CONTENT AREA ════════════════════════════════════════════════════ -->
    <div class="rsyi-content">
        <?php
        // عرض المحتوى حسب الوحدة والتبويب | Render content based on module/tab
        switch ( $active_module ) {

            case 'dashboard':
                require_once RSYI_SYS_DIR . 'admin/views/dashboard.php';
                break;

            case 'hr':
                $view_file = RSYI_SYS_DIR . 'admin/views/hr/' . $current_tab . '.php';
                if ( file_exists( $view_file ) ) {
                    require_once $view_file;
                } else {
                    require_once RSYI_SYS_DIR . 'admin/views/hr/employees.php';
                }
                break;

            case 'students':
                $view_file = RSYI_SYS_DIR . 'admin/views/students/' . $current_tab . '.php';
                if ( file_exists( $view_file ) ) {
                    require_once $view_file;
                } else {
                    require_once RSYI_SYS_DIR . 'admin/views/students/students.php';
                }
                break;

            case 'warehouse':
                $view_file = RSYI_SYS_DIR . 'admin/views/warehouse/' . $current_tab . '.php';
                if ( file_exists( $view_file ) ) {
                    require_once $view_file;
                } else {
                    require_once RSYI_SYS_DIR . 'admin/views/warehouse/products.php';
                }
                break;

            case 'accounting':
                require_once RSYI_SYS_DIR . 'admin/views/accounting-soon.php';
                break;

            case 'settings':
                require_once RSYI_SYS_DIR . 'admin/views/settings.php';
                break;

            default:
                echo '<div class="rsyi-alert rsyi-alert-warning">' . esc_html__( 'الصفحة غير موجودة | Page not found', 'rsyi-system' ) . '</div>';
        }
        ?>
    </div><!-- .rsyi-content -->

</div><!-- .rsyi-wrap -->

</body>
</html>
