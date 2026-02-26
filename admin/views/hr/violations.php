<?php
/**
 * HR — Violations View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_hr_view_violations' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-exclamation-triangle ml-2"></i><?php esc_html_e( 'المخالفات', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'الموارد البشرية &rarr; المخالفات', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_hr_manage_violations' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-hr-violations&action=new' ) ); ?>" class="btn btn-sm btn-danger">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'تسجيل مخالفة', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'من تاريخ', 'rsyi-system' ); ?></label>
      <input type="date" class="form-control" name="date_from">
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'إلى تاريخ', 'rsyi-system' ); ?></label>
      <input type="date" class="form-control" name="date_to">
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'القسم', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="dept_id">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th><?php esc_html_e( 'الموظف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'نوع المخالفة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'التاريخ', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الجزاء', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'بواسطة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام الموارد البشرية.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
