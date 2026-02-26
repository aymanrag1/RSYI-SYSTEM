<?php
/**
 * Student Affairs — Students (Accounts) View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_sa_view_students' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-graduation-cap ml-2"></i><?php esc_html_e( 'قيد الطلاب', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'شئون الطلاب &rarr; قيد الطلاب', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_sa_manage_students' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-sa-students&action=new' ) ); ?>" class="btn btn-sm btn-warning">
        <i class="fa fa-user-plus ml-1"></i><?php esc_html_e( 'تسجيل طالب', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- KPIs -->
  <div class="row mb-3">
    <?php
    $st_stats = [
        ['#e67e22', 'fa-users',      'إجمالي الطلاب',   '—'],
        ['#27ae60', 'fa-check',      'طلاب نشطون',       '—'],
        ['#e74c3c', 'fa-times',      'طلاب مفصولون',     '—'],
        ['#2980b9', 'fa-object-group','الدفعة الحالية',  '—'],
    ];
    foreach ($st_stats as [$color, $icon, $label, $val]) :
    ?>
    <div class="col-md-3 mb-2">
      <div class="card rsyi-stat-card text-white" style="background:<?php echo esc_attr($color); ?>">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between">
            <div>
              <div class="stat-label"><?php echo esc_html($label); ?></div>
              <div class="stat-value" style="font-size:22px;"><?php echo esc_html($val); ?></div>
            </div>
            <i class="fa <?php echo esc_attr($icon); ?> stat-icon" style="font-size:28px;"></i>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'الدفعة', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="cohort_id">
        <option value=""><?php esc_html_e( '— كل الدفعات —', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></label>
      <select class="form-control" name="status">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="active"><?php esc_html_e( 'نشط', 'rsyi-system' ); ?></option>
        <option value="suspended"><?php esc_html_e( 'موقوف', 'rsyi-system' ); ?></option>
        <option value="expelled"><?php esc_html_e( 'مفصول', 'rsyi-system' ); ?></option>
        <option value="graduated"><?php esc_html_e( 'خريج', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></label>
      <input type="text" class="form-control" name="s" placeholder="<?php esc_attr_e( 'اسم الطالب أو رقم الملف', 'rsyi-system' ); ?>">
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th><?php esc_html_e( 'الطالب', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'رقم الملف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الدفعة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'تاريخ القيد', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'المستندات', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="8" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام شئون الطلاب.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
