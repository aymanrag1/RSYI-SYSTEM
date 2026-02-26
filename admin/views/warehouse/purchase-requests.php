<?php
/**
 * Warehouse — Purchase Requests View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_wh_view_purchases' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-shopping-cart ml-2"></i><?php esc_html_e( 'طلبات الشراء', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'المخازن &rarr; طلبات الشراء', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_wh_manage_orders' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-wh-purchase-requests&action=new' ) ); ?>" class="btn btn-sm btn-success">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'طلب شراء جديد', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></label>
      <select class="form-control" name="status">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="pending"><?php esc_html_e( 'معلق', 'rsyi-system' ); ?></option>
        <option value="approved"><?php esc_html_e( 'موافق عليه', 'rsyi-system' ); ?></option>
        <option value="ordered"><?php esc_html_e( 'تم الطلب', 'rsyi-system' ); ?></option>
        <option value="received"><?php esc_html_e( 'تم الاستلام', 'rsyi-system' ); ?></option>
        <option value="rejected"><?php esc_html_e( 'مرفوض', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'القسم الطالب', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="dept_id">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'من تاريخ', 'rsyi-system' ); ?></label>
      <input type="date" class="form-control" name="date_from">
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th><?php esc_html_e( 'رقم الطلب', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'القسم', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الأصناف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'القيمة التقديرية', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'تاريخ الطلب', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="7" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام المخازن.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
