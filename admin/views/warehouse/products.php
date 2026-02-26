<?php
/**
 * Warehouse — Products View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_wh_view_products' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-cubes ml-2"></i><?php esc_html_e( 'المنتجات والأصناف', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'المخازن &rarr; المنتجات والأصناف', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_wh_manage_products' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-wh-products&action=new' ) ); ?>" class="btn btn-sm btn-success">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'إضافة صنف', 'rsyi-system' ); ?>
      </a>
      <a href="#" class="btn btn-sm btn-outline-light">
        <i class="fa fa-file-excel-o ml-1"></i><?php esc_html_e( 'استيراد Excel', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Stats -->
  <div class="row mb-3">
    <div class="col-md-3 mb-2">
      <div class="card rsyi-stat-card text-white bg-success">
        <div class="card-body py-3">
          <div class="stat-label"><?php esc_html_e( 'إجمالي الأصناف', 'rsyi-system' ); ?></div>
          <div class="stat-value" style="font-size:22px;">—</div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="card rsyi-stat-card text-white" style="background:#16a085;">
        <div class="card-body py-3">
          <div class="stat-label"><?php esc_html_e( 'الأصناف النشطة', 'rsyi-system' ); ?></div>
          <div class="stat-value" style="font-size:22px;">—</div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="card rsyi-stat-card text-white bg-warning">
        <div class="card-body py-3">
          <div class="stat-label"><?php esc_html_e( 'منخفضة المخزون', 'rsyi-system' ); ?></div>
          <div class="stat-value" style="font-size:22px;">—</div>
        </div>
      </div>
    </div>
    <div class="col-md-3 mb-2">
      <div class="card rsyi-stat-card text-white bg-danger">
        <div class="card-body py-3">
          <div class="stat-label"><?php esc_html_e( 'نفد المخزون', 'rsyi-system' ); ?></div>
          <div class="stat-value" style="font-size:22px;">—</div>
        </div>
      </div>
    </div>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'الفئة', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="category_id">
        <option value=""><?php esc_html_e( '— كل الفئات —', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'حالة المخزون', 'rsyi-system' ); ?></label>
      <select class="form-control" name="stock_status">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="in_stock"><?php esc_html_e( 'متوفر', 'rsyi-system' ); ?></option>
        <option value="low"><?php esc_html_e( 'منخفض', 'rsyi-system' ); ?></option>
        <option value="out"><?php esc_html_e( 'نفد', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></label>
      <input type="text" class="form-control" name="s" placeholder="<?php esc_attr_e( 'اسم الصنف أو الكود', 'rsyi-system' ); ?>">
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th><?php esc_html_e( 'كود الصنف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'اسم الصنف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الفئة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الوحدة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الكمية الحالية', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الحد الأدنى', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="9" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام المخازن.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
