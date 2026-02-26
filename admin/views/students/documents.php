<?php
/**
 * Student Affairs — Documents View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_sa_view_documents' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-file-text-o ml-2"></i><?php esc_html_e( 'مستندات الطلاب', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'شئون الطلاب &rarr; المستندات', 'rsyi-system' ); ?></div>
    </div>
  </div>

  <div class="rsyi-filters">
    <div class="form-group">
      <label><?php esc_html_e( 'نوع المستند', 'rsyi-system' ); ?></label>
      <select class="form-control rsyi-select2" name="doc_type">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="national_id"><?php esc_html_e( 'بطاقة شخصية', 'rsyi-system' ); ?></option>
        <option value="certificate"><?php esc_html_e( 'شهادة دراسية', 'rsyi-system' ); ?></option>
        <option value="photo"><?php esc_html_e( 'صورة شخصية', 'rsyi-system' ); ?></option>
        <option value="medical"><?php esc_html_e( 'كشف طبي', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <div class="form-group">
      <label><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></label>
      <select class="form-control" name="status">
        <option value=""><?php esc_html_e( '— الكل —', 'rsyi-system' ); ?></option>
        <option value="pending"><?php esc_html_e( 'بانتظار المراجعة', 'rsyi-system' ); ?></option>
        <option value="approved"><?php esc_html_e( 'موافق عليه', 'rsyi-system' ); ?></option>
        <option value="rejected"><?php esc_html_e( 'مرفوض', 'rsyi-system' ); ?></option>
      </select>
    </div>
    <button class="btn btn-primary"><?php esc_html_e( 'بحث', 'rsyi-system' ); ?></button>
  </div>

  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th><?php esc_html_e( 'الطالب', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'نوع المستند', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'تاريخ الرفع', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'الحالة', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'ملاحظات', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'إجراءات', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td colspan="6" class="text-center text-muted py-4">
              <?php esc_html_e( 'سيتم جلب البيانات من نظام شئون الطلاب.', 'rsyi-system' ); ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>
