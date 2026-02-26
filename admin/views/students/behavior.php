<?php
/**
 * Student Affairs — Behavior & Violations View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_sa_view_behavior' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-warning ml-2"></i><?php esc_html_e( 'السلوك والمخالفات', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'شئون الطلاب &rarr; السلوك والمخالفات', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_sa_manage_behavior' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-sa-behavior&action=new' ) ); ?>" class="btn btn-sm btn-danger">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'تسجيل مخالفة', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Summary -->
  <div class="row mb-3">
    <?php
    $beh_stats = [
        ['bg-warning',  'fa-exclamation',       'إنذارات نشطة',    '—'],
        ['bg-danger',   'fa-ban',               'حالات فصل',       '—'],
        ['bg-secondary','fa-history',            'مخالفات هذا الشهر','—'],
        ['bg-info',     'fa-check-circle-o',    'تحت المراقبة',    '—'],
    ];
    foreach ($beh_stats as [$bg, $icon, $label, $val]) :
    ?>
    <div class="col-md-3 mb-2">
      <div class="card rsyi-stat-card text-white <?php echo esc_attr($bg); ?>">
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
      <label><?php esc_html_e( 'نوع المخالفة', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="violation_type">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'الدفعة', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="cohort_id">
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
            <th><?php esc_html_e( 'الطالب', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'نوع المخالفة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'النقاط', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'التاريخ', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الجزاء', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'بواسطة', 'rsyi-system' ); ?></th>
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
