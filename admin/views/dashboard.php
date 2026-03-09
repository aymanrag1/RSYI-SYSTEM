<?php
/**
 * RSYI Dashboard View — لوحة التحكم الرئيسية
 */
defined( 'ABSPATH' ) || exit;

global $wpdb;

$hr_active   = RSYI_Sys_Module_Loader::is_loaded( 'hr' );
$sa_active   = RSYI_Sys_Module_Loader::is_loaded( 'students' );
$wh_active   = RSYI_Sys_Module_Loader::is_loaded( 'warehouse' );

$hr_emp_count        = 0; $hr_leave_pending = 0; $hr_attendance_today = 0;
$sa_students         = 0; $sa_permits       = 0; $sa_violations       = 0;
$wh_products         = 0; $wh_low_stock     = 0; $wh_orders           = 0;

if ( $hr_active && $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . 'rsyi_hr_employees' ) ) ) {
    $hr_emp_count        = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}rsyi_hr_employees WHERE status='active'" );
    $hr_leave_pending    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}rsyi_hr_leaves WHERE status='pending'" );
    $hr_attendance_today = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}rsyi_hr_attendance WHERE DATE(check_in)=%s", current_time('Y-m-d') ) );
}
if ( $sa_active && $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . 'rsyi_student_profiles' ) ) ) {
    $sa_students   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}rsyi_student_profiles WHERE status='active'" );
    $sa_permits    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}rsyi_exit_permits WHERE status='pending'" );
    $sa_violations = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}rsyi_violations WHERE DATE(created_at) >= DATE_SUB(NOW(), INTERVAL 30 DAY)" );
}
if ( $wh_active && $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . 'iw_products' ) ) ) {
    $wh_products  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}iw_products WHERE is_active=1" );
    $wh_low_stock = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}iw_products WHERE current_quantity <= min_quantity AND is_active=1" );
    $wh_orders    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}iw_withdrawal_orders WHERE status='pending'" );
}

$recent_logs = RSYI_Sys_DB_Installer::get_recent_logs( 10 );
?>

<div class="rsyi-page-header">
    <div class="rsyi-page-title">
        لوحة التحكم
        <small>Dashboard — <?php echo esc_html( date_i18n( 'l، j F Y', current_time('timestamp') ) ); ?></small>
    </div>
    <a href="<?php echo esc_url( admin_url('admin.php?page=rsyi-settings') ); ?>" class="rsyi-btn rsyi-btn-ghost rsyi-btn-sm">
        <i class="fa-solid fa-gear"></i> الإعدادات | Settings
    </a>
</div>

<?php if ( ! $hr_active || ! $sa_active || ! $wh_active ) : ?>
<div class="rsyi-alert rsyi-alert-warning">
    <i class="fa-solid fa-circle-info"></i>
    <?php
    $off = [];
    if(!$hr_active) $off[]='الموارد البشرية (HR)';
    if(!$sa_active) $off[]='شئون الطلاب (Students)';
    if(!$wh_active) $off[]='المخازن (Warehouse)';
    echo 'بعض الوحدات معطّلة | Some modules disabled: <strong>'.esc_html(implode('، ',$off)).'</strong>';
    ?>
    — <a href="<?php echo esc_url( admin_url('admin.php?page=rsyi-settings') ); ?>">الإعدادات | Settings</a>
</div>
<?php endif; ?>

<!-- ── Stats Grid ─────────────────────────────────────────────────────────── -->
<div class="rsyi-stats-grid mb-4">
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon blue"><i class="fa-solid fa-users-gear"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($hr_emp_count); ?></div>
            <div class="rsyi-stat-label-ar">موظف نشط</div><div class="rsyi-stat-label-en">Active Employees</div></div>
    </div>
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon amber"><i class="fa-regular fa-calendar-xmark"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($hr_leave_pending); ?></div>
            <div class="rsyi-stat-label-ar">إجازة معلّقة</div><div class="rsyi-stat-label-en">Pending Leaves</div></div>
    </div>
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon green"><i class="fa-regular fa-clock"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($hr_attendance_today); ?></div>
            <div class="rsyi-stat-label-ar">حاضر اليوم</div><div class="rsyi-stat-label-en">Attended Today</div></div>
    </div>
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon cyan"><i class="fa-solid fa-user-graduate"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($sa_students); ?></div>
            <div class="rsyi-stat-label-ar">طالب نشط</div><div class="rsyi-stat-label-en">Active Students</div></div>
    </div>
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon purple"><i class="fa-solid fa-id-card"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($sa_permits); ?></div>
            <div class="rsyi-stat-label-ar">تصاريح معلّقة</div><div class="rsyi-stat-label-en">Pending Permits</div></div>
    </div>
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon red"><i class="fa-solid fa-flag"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($sa_violations); ?></div>
            <div class="rsyi-stat-label-ar">مخالفة هذا الشهر</div><div class="rsyi-stat-label-en">Violations (30 days)</div></div>
    </div>
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon blue"><i class="fa-solid fa-boxes-stacked"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($wh_products); ?></div>
            <div class="rsyi-stat-label-ar">صنف في المخزن</div><div class="rsyi-stat-label-en">Warehouse Products</div></div>
    </div>
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon amber"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($wh_low_stock); ?></div>
            <div class="rsyi-stat-label-ar">منخفض المخزون</div><div class="rsyi-stat-label-en">Low Stock</div></div>
    </div>
    <div class="rsyi-stat-card">
        <div class="rsyi-stat-icon green"><i class="fa-solid fa-cart-shopping"></i></div>
        <div><div class="rsyi-stat-value"><?php echo number_format($wh_orders); ?></div>
            <div class="rsyi-stat-label-ar">طلب صرف معلّق</div><div class="rsyi-stat-label-en">Pending Withdrawals</div></div>
    </div>
</div>

<!-- ── Module Cards ───────────────────────────────────────────────────────── -->
<div class="row g-3 mb-4">
<?php
$mods = [
    ['ar'=>'الموارد البشرية','en'=>'Human Resources','icon'=>'fa-users-gear','url'=>'rsyi-hr','on'=>$hr_active],
    ['ar'=>'شئون الطلاب','en'=>'Student Affairs','icon'=>'fa-user-graduate','url'=>'rsyi-students','on'=>$sa_active],
    ['ar'=>'المخازن','en'=>'Warehouse','icon'=>'fa-warehouse','url'=>'rsyi-warehouse','on'=>$wh_active],
    ['ar'=>'الحسابات','en'=>'Accounting','icon'=>'fa-calculator','url'=>'#','on'=>false,'soon'=>true],
];
foreach($mods as $m): ?>
    <div class="col-md-3">
        <div class="rsyi-module-card <?php echo $m['on']?'enabled':'disabled'; ?>">
            <div class="d-flex align-items-center gap-3">
                <span class="rsyi-stat-icon <?php echo $m['on']?'green':'gray'; ?>" style="width:40px;height:40px;font-size:1rem;">
                    <i class="fa-solid <?php echo esc_attr($m['icon']); ?>"></i>
                </span>
                <div>
                    <div class="rsyi-text-ar fw-semibold"><?php echo esc_html($m['ar']); ?></div>
                    <div class="rsyi-text-muted" style="font-size:.72rem"><?php echo esc_html($m['en']); ?></div>
                </div>
            </div>
            <?php if(!empty($m['soon'])): ?>
                <span class="rsyi-badge rsyi-badge-warning">قريباً | Soon</span>
            <?php elseif($m['on']): ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page='.$m['url'])); ?>" class="rsyi-btn rsyi-btn-primary rsyi-btn-sm">
                    فتح | Open
                </a>
            <?php else: ?>
                <span class="rsyi-badge rsyi-badge-gray">معطّل | Off</span>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
</div>

<!-- ── Recent Activity ────────────────────────────────────────────────────── -->
<div class="rsyi-card">
    <div class="rsyi-card-title">آخر النشاطات <span class="rsyi-en rsyi-text-muted">| Recent Activity</span></div>
    <hr class="rsyi-divider rsyi-mt-0">
    <?php if(empty($recent_logs)): ?>
        <div class="text-center py-5 rsyi-text-muted">
            <i class="fa-regular fa-clock-rotate-left d-block mb-2 fs-3"></i>
            لا توجد نشاطات مسجّلة | No activity recorded yet
        </div>
    <?php else: ?>
        <div class="rsyi-table-wrap">
        <table class="rsyi-table">
            <thead><tr>
                <th>المستخدم <small>User</small></th>
                <th>النظام <small>System</small></th>
                <th>العملية <small>Action</small></th>
                <th>الوصف <small>Description</small></th>
                <th>التوقيت <small>Time</small></th>
            </tr></thead>
            <tbody>
            <?php
            $sys_badges=['hr'=>'<span class="rsyi-badge rsyi-badge-info">HR</span>','students'=>'<span class="rsyi-badge rsyi-badge-success">طلاب</span>','warehouse'=>'<span class="rsyi-badge rsyi-badge-warning">مخازن</span>'];
            foreach($recent_logs as $log):
                $user=get_userdata($log->user_id);
            ?>
                <tr>
                    <td><?php echo $user?esc_html($user->display_name):'<span class="rsyi-text-muted">—</span>'; ?></td>
                    <td><?php echo $sys_badges[$log->system]??'<span class="rsyi-badge rsyi-badge-gray">'.esc_html($log->system).'</span>'; ?></td>
                    <td><?php echo esc_html($log->action); ?></td>
                    <td class="rsyi-text-muted" style="font-size:.8rem"><?php echo esc_html($log->description); ?></td>
                    <td class="rsyi-text-muted" style="font-size:.78rem;white-space:nowrap"><?php echo esc_html(mysql2date('j M Y — H:i',$log->created_at)); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    <?php endif; ?>
</div>
