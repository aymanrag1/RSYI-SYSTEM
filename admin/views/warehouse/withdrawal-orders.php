<?php
/**
 * Warehouse — Withdrawal Orders (Stock Out) View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_wh_manage_orders' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-minus-circle ml-2"></i><?php esc_html_e( 'أوامر الصرف', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'المخازن &rarr; أوامر الصرف', 'rsyi-system' ); ?></div>
    </div>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-wh-withdrawal-orders&action=new' ) ); ?>" class="btn btn-sm btn-danger">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'أمر صرف جديد', 'rsyi-system' ); ?>
      </a>
    </div>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'القسم المستلم', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="dept_id">
        <option value=""><?php esc_html_e( '— كل الأقسام —', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'من تاريخ', 'rsyi-system' ); ?></label>
      <input type="date" class="form-control" name="date_from">
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'إلى تاريخ', 'rsyi-system' ); ?></label>
      <input type="date" class="form-control" name="date_to">
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th><?php esc_html_e( 'رقم الأمر', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'القسم المستلم', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الصنف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الكمية', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الغرض', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'تاريخ الصرف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'بواسطة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام المخازن.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
