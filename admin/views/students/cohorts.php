<?php
/**
 * Student Affairs — Cohorts View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_sa_view_cohorts' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-object-group ml-2"></i><?php esc_html_e( 'الدفعات', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'شئون الطلاب &rarr; الدفعات', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_sa_manage_cohorts' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-sa-cohorts&action=new' ) ); ?>" class="btn btn-sm btn-warning">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'إضافة دفعة', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th><?php esc_html_e( 'اسم الدفعة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'تاريخ البدء', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'تاريخ الانتهاء', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'عدد الطلاب', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام شئون الطلاب.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
