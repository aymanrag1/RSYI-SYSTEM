<?php
/**
 * HR — Leaves View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_hr_view_leaves' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">

  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-calendar-check-o ml-2"></i><?php esc_html_e( 'طلبات الإجازات', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'الموارد البشرية &rarr; الإجازات', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_hr_manage_leaves' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-hr-leaves&action=new' ) ); ?>" class="btn btn-sm btn-success">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'طلب إجازة جديد', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Summary Cards -->
  <div class="row mb-3">
    <?php
    $leave_cards = [
        ['bg-warning', 'fa-clock-o',        'معلق',    'pending',  0],
        ['bg-success', 'fa-check-circle',   'موافق عليه', 'approved', 0],
        ['bg-danger',  'fa-times-circle',   'مرفوض',   'rejected', 0],
        ['bg-primary', 'fa-list',           'الإجمالي', 'all',      0],
    ];
    foreach ( $leave_cards as [$bg, $icon, $label, $status, $count] ) :
    ?>
    <div class="col-md-3 mb-2">
      <div class="card text-white <?php echo esc_attr($bg); ?> rsyi-stat-card">
        <div class="card-body py-3">
          <div class="d-flex justify-content-between">
            <div>
              <div class="stat-label"><?php echo esc_html($label); ?></div>
              <div class="stat-value" style="font-size:22px;"><?php echo esc_html($count); ?></div>
            </div>
            <i class="fa <?php echo esc_attr($icon); ?> stat-icon" style="font-size:28px;"></i>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Filters -->
  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></label>
      <select class="form-control" name="status">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="pending"><?php esc_html_e( 'معلق', 'rsyi-system' ); ?></option>
        <option value="approved"><?php esc_html_e( 'موافق عليه', 'rsyi-system' ); ?></option>
        <option value="rejected"><?php esc_html_e( 'مرفوض', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'نوع الإجازة', 'rsyi-system' ); ?></label>
      <select class="form-control" name="type">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="annual"><?php esc_html_e( 'سنوية', 'rsyi-system' ); ?></option>
        <option value="sick"><?php esc_html_e( 'مرضية', 'rsyi-system' ); ?></option>
        <option value="emergency"><?php esc_html_e( 'طارئة', 'rsyi-system' ); ?></option>
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

  <!-- Table -->
  <div class="card rsyi-card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table rsyi-table mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th><?php esc_html_e( 'الموظف', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'نوع الإجازة', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'من', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'إلى', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'الأيام', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colspan="8" class="text-center text-muted py-4">
                <i class="fa fa-info-circle ml-2"></i>
                <?php esc_html_e( 'سيتم جلب البيانات من نظام الموارد البشرية.', 'rsyi-system' ); ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
