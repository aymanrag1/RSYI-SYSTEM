<?php
/**
 * HR — Job Titles View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_hr_view_departments' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-id-badge ml-2"></i><?php esc_html_e( 'المسميات الوظيفية', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'الموارد البشرية &rarr; المسميات الوظيفية', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_hr_manage_departments' ) ) : ?>
    <div class="rsyi-actions">
      <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#addJobTitleModal">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'إضافة مسمى', 'rsyi-system' ); ?>
      </button>
    </div>
    <?php endif; ?>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th><?php esc_html_e( 'المسمى الوظيفي', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'القسم', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'عدد الموظفين', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام الموارد البشرية.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
