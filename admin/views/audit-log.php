<?php
/**
 * Audit Log View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_sys_view_audit_log' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}

$filter_system = sanitize_key( $_GET['system'] ?? '' );
$logs = RSYI_Sys_DB_Installer::get_recent_logs( 50, $filter_system );
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-history ml-2"></i><?php esc_html_e( 'سجل العمليات', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'آخر 50 عملية', 'rsyi-system' ); ?></div>
    </div>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'النظام', 'rsyi-system' ); ?></label>
      <select class="form-control" name="system" onchange="this.form.submit()">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="hr"        <?php selected( $filter_system, 'hr' ); ?>><?php esc_html_e( 'الموارد البشرية', 'rsyi-system' ); ?></option>
        <option value="warehouse" <?php selected( $filter_system, 'warehouse' ); ?>><?php esc_html_e( 'المخازن', 'rsyi-system' ); ?></option>
        <option value="students"  <?php selected( $filter_system, 'students' ); ?>><?php esc_html_e( 'شئون الطلاب', 'rsyi-system' ); ?></option>
        <option value="system"    <?php selected( $filter_system, 'system' ); ?>><?php esc_html_e( 'النظام الموحد', 'rsyi-system' ); ?></option>
      </select>
    </div>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th><?php esc_html_e( 'النظام', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'العملية', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'النوع / المعرّف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الوصف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'المستخدم', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'عنوان IP', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'التاريخ', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php if ( empty( $logs ) ) : ?>
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <?php esc_html_e( 'لا توجد سجلات.', 'rsyi-system' ); ?>
            </td>
          </tr>
          <?php else : ?>
          <?php foreach ( $logs as $log ) :
              $user  = get_userdata( $log->user_id );
              $badge = match( $log->system ) {
                  'hr'        => 'badge-hr',
                  'warehouse' => 'badge-wh',
                  'students'  => 'badge-sa',
                  default     => 'badge-secondary',
              };
          ?>
          <tr>
            <td><span class="badge badge-sys <?php echo esc_attr( $badge ); ?>"><?php echo esc_html( strtoupper( $log->system ) ); ?></span></td>
            <td><?php echo esc_html( $log->action ); ?></td>
            <td>
              <?php if ( $log->object_type ) : ?>
              <small class="text-muted"><?php echo esc_html( $log->object_type ); ?> #<?php echo esc_html( $log->object_id ); ?></small>
              <?php endif; ?>
            </td>
            <td><?php echo esc_html( $log->description ); ?></td>
            <td><?php echo $user ? esc_html( $user->display_name ) : '<em class="text-muted">—</em>'; ?></td>
            <td><small><?php echo esc_html( $log->ip_address ); ?></small></td>
            <td><small><?php echo esc_html( wp_date( 'Y/m/d H:i', strtotime( $log->created_at ) ) ); ?></small></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
