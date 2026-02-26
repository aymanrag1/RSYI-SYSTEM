<?php
/**
 * HR — Permissions View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_hr_manage_settings' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}

$users = get_users( [ 'role__in' => [ 'rsyi_dean', 'rsyi_hr_manager', 'rsyi_dept_head', 'rsyi_staff', 'rsyi_readonly' ] ] );
$selected_user_id = absint( $_GET['user_id'] ?? 0 );

$modules_hr = [
    'dashboard'    => 'لوحة التحكم',
    'employees'    => 'الموظفون',
    'departments'  => 'الأقسام',
    'job_titles'   => 'المسميات الوظيفية',
    'leaves'       => 'الإجازات',
    'overtime'     => 'العمل الإضافي',
    'attendance'   => 'الحضور والانصراف',
    'violations'   => 'المخالفات',
    'reports'      => 'التقارير',
    'settings'     => 'الإعدادات',
];

$permission_levels = [
    'none'       => 'بدون صلاحية',
    'view'       => 'عرض فقط',
    'read'       => 'قراءة كاملة',
    'read_write' => 'قراءة وكتابة',
];
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-lock ml-2"></i><?php esc_html_e( 'إدارة الصلاحيات', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'الموارد البشرية &rarr; الصلاحيات', 'rsyi-system' ); ?></div>
    </div>
  </div>

  <div class="row">
    <!-- Users list -->
    <div class="col-md-3">
      <div class="card rsyi-card">
        <div class="card-header"><i class="fa fa-users ml-2"></i><?php esc_html_e( 'المستخدمون', 'rsyi-system' ); ?></div>
        <div class="list-group list-group-flush">
          <?php foreach ( $users as $user ) :
              $active = $selected_user_id === $user->ID;
          ?>
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-hr-permissions&user_id=' . $user->ID ) ); ?>"
             class="list-group-item list-group-item-action <?php echo $active ? 'active' : ''; ?>">
            <i class="fa fa-user ml-2"></i>
            <?php echo esc_html( $user->display_name ); ?>
            <small class="d-block text-<?php echo $active ? 'white-50' : 'muted'; ?>">
              <?php echo esc_html( implode( ', ', $user->roles ) ); ?>
            </small>
          </a>
          <?php endforeach; ?>
          <?php if ( empty( $users ) ) : ?>
          <div class="list-group-item text-muted"><?php esc_html_e( 'لا يوجد مستخدمون.', 'rsyi-system' ); ?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Permissions grid -->
    <div class="col-md-9">
      <?php if ( $selected_user_id ) : ?>
      <div class="card rsyi-card">
        <div class="card-header">
          <i class="fa fa-shield ml-2"></i>
          <?php
          $sel_user = get_userdata( $selected_user_id );
          echo $sel_user ? esc_html( sprintf( __( 'صلاحيات: %s', 'rsyi-system' ), $sel_user->display_name ) ) : '';
          ?>
        </div>
        <div class="card-body">
          <form id="rsyi-permissions-form">
            <?php wp_nonce_field( 'rsyi_sys_admin', 'nonce' ); ?>
            <input type="hidden" name="user_id" value="<?php echo esc_attr( $selected_user_id ); ?>">
            <table class="table rsyi-table">
              <thead>
                <tr>
                  <th><?php esc_html_e( 'الوحدة', 'rsyi-system' ); ?></th>
                  <?php foreach ( $permission_levels as $level => $label ) : ?>
                  <th class="text-center"><?php echo esc_html( $label ); ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ( $modules_hr as $module => $module_label ) : ?>
                <tr>
                  <td><strong><?php echo esc_html( $module_label ); ?></strong></td>
                  <?php foreach ( $permission_levels as $level => $level_label ) : ?>
                  <td class="text-center">
                    <input type="radio"
                           name="permissions[<?php echo esc_attr( $module ); ?>]"
                           value="<?php echo esc_attr( $level ); ?>">
                  </td>
                  <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-primary" onclick="rsyiAjaxSubmit('rsyi-permissions-form','rsyi_hr_save_permissions')">
                <i class="fa fa-save ml-1"></i><?php esc_html_e( 'حفظ الصلاحيات', 'rsyi-system' ); ?>
              </button>
              <button type="button" class="btn btn-outline-danger rsyi-confirm-delete">
                <i class="fa fa-refresh ml-1"></i><?php esc_html_e( 'إعادة تعيين', 'rsyi-system' ); ?>
              </button>
            </div>
          </form>
        </div>
      </div>
      <?php else : ?>
      <div class="card rsyi-card">
        <div class="card-body text-center text-muted py-5">
          <i class="fa fa-arrow-right fa-2x mb-3 d-block"></i>
          <?php esc_html_e( 'اختر مستخدمًا من القائمة على اليمين لإدارة صلاحياته.', 'rsyi-system' ); ?>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
