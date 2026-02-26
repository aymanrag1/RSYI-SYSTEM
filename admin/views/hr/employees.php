<?php
/**
 * HR — Employees View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_hr_view_employees' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
$action = sanitize_key( $_GET['action'] ?? 'list' );
?>
<div class="rsyi-wrap">

  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-users ml-2"></i><?php esc_html_e( 'الموظفون', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'الموارد البشرية &rarr; الموظفون', 'rsyi-system' ); ?></div>
    </div>
    <?php if ( current_user_can( 'rsyi_hr_manage_employees' ) ) : ?>
    <div class="rsyi-actions">
      <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-hr-employees&action=new' ) ); ?>" class="btn btn-sm btn-success">
        <i class="fa fa-plus ml-1"></i><?php esc_html_e( 'إضافة موظف', 'rsyi-system' ); ?>
      </a>
    </div>
    <?php endif; ?>
  </div>

  <!-- Filters -->
  <div class="rsyi-filters">
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
        <option value="active"><?php esc_html_e( 'فعّال', 'rsyi-system' ); ?></option>
        <option value="inactive"><?php esc_html_e( 'غير فعّال', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></label>
      <input type="text" class="form-control" name="s" placeholder="<?php esc_attr_e( 'اسم الموظف أو الرقم الوظيفي', 'rsyi-system' ); ?>">
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <!-- Table -->
  <div class="card rsyi-card">
    <div class="card-header">
      <i class="fa fa-list ml-2"></i><?php esc_html_e( 'قائمة الموظفين', 'rsyi-system' ); ?>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table rsyi-table mb-0">
          <thead>
            <tr>
              <th>#</th>
              <th><?php esc_html_e( 'الموظف', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'الرقم الوظيفي', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'القسم', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'المسمى الوظيفي', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'تاريخ التعيين', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
              <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
            </tr>
          </thead>
          <tbody>
            <?php
            // في البيئة الحقيقية: $employees = rsyi_hr_get_employees([...]);
            if ( function_exists( 'rsyi_hr_get_employees' ) ) :
                $employees = rsyi_hr_get_employees();
                foreach ( $employees as $i => $emp ) :
                    $status_class = $emp->status === 'active' ? 'status-active' : 'status-inactive';
            ?>
            <tr>
              <td><?php echo $i + 1; ?></td>
              <td>
                <div class="d-flex align-items-center">
                  <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center ml-2"
                       style="width:32px;height:32px;font-size:13px;flex-shrink:0;">
                    <?php echo esc_html( mb_substr( $emp->name, 0, 1 ) ); ?>
                  </div>
                  <span><?php echo esc_html( $emp->name ); ?></span>
                </div>
              </td>
              <td><?php echo esc_html( $emp->emp_number ); ?></td>
              <td><?php echo esc_html( $emp->dept_name ); ?></td>
              <td><?php echo esc_html( $emp->job_title ); ?></td>
              <td><?php echo esc_html( $emp->hire_date ); ?></td>
              <td><span class="status-pill <?php echo esc_attr( $status_class ); ?>"><?php echo esc_html( $emp->status === 'active' ? 'فعّال' : 'غير فعّال' ); ?></span></td>
              <td>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-hr-employees&action=edit&id=' . $emp->id ) ); ?>" class="btn btn-sm btn-outline-primary btn-action">
                  <i class="fa fa-edit"></i>
                </a>
                <?php if ( current_user_can( 'rsyi_hr_manage_employees' ) ) : ?>
                <a href="#" class="btn btn-sm btn-outline-danger btn-action rsyi-confirm-delete">
                  <i class="fa fa-trash"></i>
                </a>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php else : ?>
            <tr>
              <td colspan="8" class="text-center text-muted py-4">
                <i class="fa fa-info-circle ml-2"></i>
                <?php esc_html_e( 'نظام الموارد البشرية غير مفعّل أو لا توجد بيانات.', 'rsyi-system' ); ?>
              </td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
