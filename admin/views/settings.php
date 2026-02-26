<?php
/**
 * Settings Page View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_sys_manage_settings' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}
?>
<div class="rsyi-wrap">
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name"><i class="fa fa-cog ml-2"></i><?php esc_html_e( 'إعدادات النظام الموحد', 'rsyi-system' ); ?></div>
      <div class="rsyi-breadcrumb"><?php esc_html_e( 'الإعدادات', 'rsyi-system' ); ?></div>
    </div>
  </div>

  <div class="card rsyi-card">
    <div class="card-body">
      <form method="POST" action="options.php">
        <?php
        settings_fields( RSYI_Sys_Settings::OPTION_GROUP );
        do_settings_sections( RSYI_Sys_Settings::OPTION_PAGE );
        submit_button( __( 'حفظ الإعدادات', 'rsyi-system' ) );
        ?>
      </form>
    </div>
  </div>
</div>
