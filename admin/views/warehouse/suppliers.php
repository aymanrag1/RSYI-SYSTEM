<?php
/**
 * Warehouse — Suppliers View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_wh_view_suppliers' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-truck ml-2"></i><?php esc_html_e( 'الموردون', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'المخازن &rarr; الموردون', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_wh_manage_products' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-wh-suppliers&action=new' ) ); ?>" class="btn btn-sm btn-success">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'إضافة مورد', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></label>
      <input type="text" class="form-control" name="s" placeholder="<?php esc_attr_e( 'اسم المورد أو رقم الهاتف', 'rsyi-system' ); ?>">
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th><?php esc_html_e( 'اسم المورد', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الشخص المسؤول', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الهاتف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'البريد الإلكتروني', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'عدد الأصناف', 'rsyi-system' ); ?></th>
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
