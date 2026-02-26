<?php
/**
 * HR — Overtime View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_hr_view_overtime' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-hourglass-half ml-2"></i><?php esc_html_e( 'العمل الإضافي', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'الموارد البشرية &rarr; العمل الإضافي', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_hr_manage_overtime' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-hr-overtime&action=new' ) ); ?>" class="btn btn-sm btn-success">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'طلب أوفرتايم', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'الشهر', 'rsyi-system' ); ?></label>
      <input type="month" class="form-control" name="month" value="<?php echo esc_attr( current_time( 'Y-m' ) ); ?>">
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></label>
      <select class="form-control" name="status">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="pending"><?php esc_html_e( 'معلق', 'rsyi-system' ); ?></option>
        <option value="approved"><?php esc_html_e( 'موافق عليه', 'rsyi-system' ); ?></option>
        <option value="rejected"><?php esc_html_e( 'مرفوض', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th><?php esc_html_e( 'الموظف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'التاريخ', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الساعات', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'السبب', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام الموارد البشرية.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
