<?php
/**
 * Warehouse — Reports View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_wh_view_reports' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-bar-chart ml-2"></i><?php esc_html_e( 'تقارير المخازن', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'المخازن &rarr; التقارير', 'rsyi-system' ); ?></div>
    </div>
    <div class="rsyi-actions">
      <a href="#" class="btn btn-sm btn-outline-light"><i class="fa fa-print ml-1"></i><?php esc_html_e( 'طباعة', 'rsyi-system' ); ?></a>
      <a href="#" class="btn btn-sm btn-outline-light"><i class="fa fa-file-excel-o ml-1"></i><?php esc_html_e( 'Excel', 'rsyi-system' ); ?></a>
    </div>
  </div>

  <!-- Report Types -->
  <div class="row mb-4">
    <?php
    $report_types = [
        ['fa-cubes',        'تقرير المخزون الحالي',    'inventory',  'text-success'],
        ['fa-arrow-down',   'تقرير حركة الإضافات',    'additions',  'text-primary'],
        ['fa-arrow-up',     'تقرير حركة الصرف',       'withdrawals','text-danger' ],
        ['fa-warning',      'تقرير الأصناف المنخفضة', 'low_stock',  'text-warning'],
    ];
    foreach ($report_types as [$icon, $label, $type, $color]) :
    ?>
    <div class="col-md-3 mb-3">
      <div class="card rsyi-card text-center" style="cursor:pointer;" onclick="location.href='?page=rsyi-wh-reports&type=<?php echo esc_attr($type); ?>'">
        <div class="card-body">
          <i class="fa <?php echo esc_attr($icon); ?> fa-2x <?php echo esc_attr($color); ?> mb-2 d-block"></i>
          <strong><?php echo esc_html($label); ?></strong>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'الفئة', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="category_id">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
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
    <button class="btn btn-primary"><?php esc_html_e( 'عرض التقرير', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-header">
      <i class="fa fa-table ml-2"></i><?php esc_html_e( 'نتائج التقرير', 'rsyi-system' ); ?>
    </div>
    <div class="card-body text-center text-muted py-5">
      <i class="fa fa-bar-chart fa-3x mb-3 d-block text-muted"></i>
      <?php esc_html_e( 'اختر نوع التقرير والتاريخ ثم اضغط "عرض التقرير".', 'rsyi-system' ); ?>
    </div>
  </div>
</div>
