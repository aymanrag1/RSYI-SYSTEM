<?php
/**
 * Student Affairs — Permits View (Exit / Overnight)
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_sa_view_permits' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-id-card-o ml-2"></i><?php esc_html_e( 'التصاريح', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'شئون الطلاب &rarr; التصاريح والإذن', 'rsyi-system' ); ?></div>
    </div>
  </div>

  <!-- Permit type tabs -->
  <ul class="nav nav-tabs mb-3">
    <li class="nav-item">
      <a class="nav-link active" href="?page=rsyi-sa-permits&type=exit">
        <i class="fa fa-sign-out ml-1"></i><?php esc_html_e( 'تصاريح الخروج', 'rsyi-system' ); ?>
        <span class="badge badge-warning">—</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="?page=rsyi-sa-permits&type=overnight">
        <i class="fa fa-moon-o ml-1"></i><?php esc_html_e( 'تصاريح المبيت', 'rsyi-system' ); ?>
        <span class="badge badge-secondary">—</span>
      </a>
    </li>
  </ul>

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
      <label><?php esc_html_e( 'التاريخ', 'rsyi-system' ); ?></label>
      <input type="date" class="form-control" name="date">
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
            <th><?php esc_html_e( 'نوع التصريح', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'التاريخ المطلوب', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الغرض', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'مرحلة الموافقة', 'rsyi-system' ); ?></th>
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
