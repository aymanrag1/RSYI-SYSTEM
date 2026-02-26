<?php
/**
 * HR — Attendance View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_hr_view_attendance' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-clock-o ml-2"></i><?php esc_html_e( 'الحضور والانصراف', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'الموارد البشرية &rarr; الحضور والانصراف', 'rsyi-system' ); ?></div>
    </div>
    <div class="rsyi-actions">
      <a href="#" class="btn btn-sm btn-success"><i class="fa fa-download ml-1"></i><?php esc_html_e( 'تصدير Excel', 'rsyi-system' ); ?></a>
    </div>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'التاريخ', 'rsyi-system' ); ?></label>
      <input type="date" class="form-control" name="date" value="<?php echo esc_attr( current_time( 'Y-m-d' ) ); ?>">
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'القسم', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="dept_id">
        <option value=""><?php esc_html_e( '— كل الأقسام —', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></label>
      <select class="form-control" name="status">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="present"><?php esc_html_e( 'حاضر', 'rsyi-system' ); ?></option>
        <option value="absent"><?php esc_html_e( 'غائب', 'rsyi-system' ); ?></option>
        <option value="late"><?php esc_html_e( 'متأخر', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'عرض', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th><?php esc_html_e( 'الموظف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'القسم', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'وقت الحضور', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'وقت الانصراف', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'ملاحظات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب بيانات الحضور من نظام الموارد البشرية.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
