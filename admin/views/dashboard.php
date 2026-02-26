<?php
/**
 * لوحة التحكم الموحدة — Dashboard View
 */
defined( 'ABSPATH' ) || exit;
if ( ! current_user_can( 'rsyi_sys_view_dashboard' ) ) {
    wp_die( esc_html__( 'غير مصرح.', 'rsyi-system' ) );
}

$dep_status = RSYI_Sys_Dependencies::get_status();

// KPIs — في البيئة الحقيقية تُجلب من كل إضافة فرعية
$kpi = [
    'hr' => [
        'employees'   => function_exists( 'rsyi_hr_count_employees' ) ? rsyi_hr_count_employees() : '—',
        'leaves'      => function_exists( 'rsyi_hr_pending_leaves' )  ? rsyi_hr_pending_leaves()  : '—',
        'absent'      => function_exists( 'rsyi_hr_absent_today' )    ? rsyi_hr_absent_today()    : '—',
        'violations'  => function_exists( 'rsyi_hr_violations_month' )? rsyi_hr_violations_month(): '—',
    ],
    'wh' => [
        'products'    => function_exists( 'rsyi_wh_count_products' )     ? rsyi_wh_count_products()     : '—',
        'purchases'   => function_exists( 'rsyi_wh_pending_purchases' )  ? rsyi_wh_pending_purchases()  : '—',
        'withdrawals' => function_exists( 'rsyi_wh_today_withdrawals' )  ? rsyi_wh_today_withdrawals()  : '—',
        'low_stock'   => function_exists( 'rsyi_wh_low_stock_count' )    ? rsyi_wh_low_stock_count()    : '—',
    ],
    'sa' => [
        'students'    => function_exists( 'rsyi_sa_count_students' )     ? rsyi_sa_count_students()     : '—',
        'documents'   => function_exists( 'rsyi_sa_pending_documents' )  ? rsyi_sa_pending_documents()  : '—',
        'permits'     => function_exists( 'rsyi_sa_pending_permits' )    ? rsyi_sa_pending_permits()    : '—',
        'behavior'    => function_exists( 'rsyi_sa_behavior_cases' )     ? rsyi_sa_behavior_cases()     : '—',
    ],
];

$recent_logs = RSYI_Sys_DB_Installer::get_recent_logs( 8 );
?>
<div class="rsyi-wrap">

  <!-- Top Bar -->
  <div class="rsyi-top-bar">
    <div>
      <div class="rsyi-system-name">
        <i class="fa fa-institution"></i>
        <?php echo esc_html( RSYI_Sys_Settings::get( 'institute_name', 'معهد البحر الأحمر للسياحة البحرية' ) ); ?>
      </div>
      <div class="rsyi-breadcrumb">
        <?php esc_html_e( 'لوحة التحكم الموحدة', 'rsyi-system' ); ?>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <span class="text-white-50" id="rsyi-current-date" style="font-size:13px;"></span>
      <a class="btn btn-sm btn-outline-light" href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-system-settings' ) ); ?>">
        <i class="fa fa-cog"></i>
      </a>
    </div>
  </div>

  <!-- System Status Banners -->
  <?php foreach ( RSYI_Sys_Dependencies::REQUIRED as $key => $info ) : ?>
    <?php if ( ! $dep_status[ $key ] ) : ?>
      <div class="alert alert-warning rsyi-alert">
        <i class="fa fa-exclamation-triangle ml-2"></i>
        <?php printf(
            /* translators: %s: system name */
            esc_html__( 'الإضافة "%s" غير مفعّلة — بعض الميزات لن تظهر.', 'rsyi-system' ),
            esc_html( $info['label'] )
        ); ?>
      </div>
    <?php endif; ?>
  <?php endforeach; ?>

  <!-- ── HR KPIs ──────────────────────────────────────────────────────── -->
  <p class="rsyi-section-title">
    <span><i class="fa fa-briefcase ml-2 text-primary"></i><?php esc_html_e( 'نظام الموارد البشرية', 'rsyi-system' ); ?></span>
    <span class="badge badge-primary badge-sys"><?php esc_html_e( 'HR', 'rsyi-system' ); ?></span>
  </p>
  <div class="row mb-4">
    <?php
    $hr_cards = [
        [ 'bg-primary',   'fa-users',              'إجمالي الموظفين',   $kpi['hr']['employees'],  'rsyi-hr-employees'  ],
        [ 'bg-success',   'fa-calendar-check-o',   'إجازات معلقة',      $kpi['hr']['leaves'],     'rsyi-hr-leaves'     ],
        [ 'bg-warning',   'fa-clock-o',            'غائبون اليوم',      $kpi['hr']['absent'],     'rsyi-hr-attendance' ],
        [ 'bg-danger',    'fa-exclamation-triangle','مخالفات هذا الشهر', $kpi['hr']['violations'], 'rsyi-hr-violations' ],
    ];
    foreach ( $hr_cards as [ $bg, $icon, $label, $val, $slug ] ) :
    ?>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card rsyi-stat-card text-white <?php echo esc_attr( $bg ); ?>">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-label"><?php echo esc_html( $label ); ?></div>
              <div class="stat-value"><?php echo esc_html( $val ); ?></div>
            </div>
            <i class="fa <?php echo esc_attr( $icon ); ?> stat-icon"></i>
          </div>
        </div>
        <div class="card-footer">
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>">
            <?php esc_html_e( 'عرض التفاصيل', 'rsyi-system' ); ?> &larr;
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ── Warehouse KPIs ───────────────────────────────────────────────── -->
  <p class="rsyi-section-title">
    <span><i class="fa fa-archive ml-2 text-success"></i><?php esc_html_e( 'نظام المخازن', 'rsyi-system' ); ?></span>
    <span class="badge badge-success badge-sys"><?php esc_html_e( 'WH', 'rsyi-system' ); ?></span>
  </p>
  <div class="row mb-4">
    <?php
    $wh_cards = [
        [ '#2ecc71', 'fa-cubes',        'إجمالي الأصناف',        $kpi['wh']['products'],   'rsyi-wh-products'         ],
        [ '#16a085', 'fa-shopping-cart','طلبات شراء معلقة',      $kpi['wh']['purchases'],  'rsyi-wh-purchase-requests'],
        [ '#27ae60', 'fa-minus-circle', 'أوامر صرف اليوم',       $kpi['wh']['withdrawals'],'rsyi-wh-withdrawal-orders'],
        [ '#1abc9c', 'fa-warning',      'أصناف منخفضة المخزون',  $kpi['wh']['low_stock'],  'rsyi-wh-reports'          ],
    ];
    foreach ( $wh_cards as [ $color, $icon, $label, $val, $slug ] ) :
    ?>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card rsyi-stat-card text-white" style="background:<?php echo esc_attr( $color ); ?>">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-label"><?php echo esc_html( $label ); ?></div>
              <div class="stat-value"><?php echo esc_html( $val ); ?></div>
            </div>
            <i class="fa <?php echo esc_attr( $icon ); ?> stat-icon"></i>
          </div>
        </div>
        <div class="card-footer">
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>">
            <?php esc_html_e( 'عرض التفاصيل', 'rsyi-system' ); ?> &larr;
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ── Student Affairs KPIs ─────────────────────────────────────────── -->
  <p class="rsyi-section-title">
    <span><i class="fa fa-graduation-cap ml-2 text-warning"></i><?php esc_html_e( 'شئون الطلاب', 'rsyi-system' ); ?></span>
    <span class="badge badge-warning badge-sys"><?php esc_html_e( 'SA', 'rsyi-system' ); ?></span>
  </p>
  <div class="row mb-4">
    <?php
    $sa_cards = [
        [ '#e67e22', 'fa-graduation-cap',     'إجمالي الطلاب',          $kpi['sa']['students'],  'rsyi-sa-students'  ],
        [ '#d35400', 'fa-file-text-o',        'مستندات بانتظار الموافقة',$kpi['sa']['documents'], 'rsyi-sa-documents' ],
        [ '#e74c3c', 'fa-id-card-o',          'تصاريح معلقة',           $kpi['sa']['permits'],   'rsyi-sa-permits'   ],
        [ '#c0392b', 'fa-exclamation-circle', 'مخالفات سلوكية',         $kpi['sa']['behavior'],  'rsyi-sa-behavior'  ],
    ];
    foreach ( $sa_cards as [ $color, $icon, $label, $val, $slug ] ) :
    ?>
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card rsyi-stat-card text-white" style="background:<?php echo esc_attr( $color ); ?>">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-label"><?php echo esc_html( $label ); ?></div>
              <div class="stat-value"><?php echo esc_html( $val ); ?></div>
            </div>
            <i class="fa <?php echo esc_attr( $icon ); ?> stat-icon"></i>
          </div>
        </div>
        <div class="card-footer">
          <a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $slug ) ); ?>">
            <?php esc_html_e( 'عرض التفاصيل', 'rsyi-system' ); ?> &larr;
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- ── Charts + Quick Actions ───────────────────────────────────────── -->
  <div class="row mb-4">
    <div class="col-lg-4 col-md-6 mb-3">
      <div class="card rsyi-card h-100">
        <div class="card-header"><i class="fa fa-pie-chart ml-2"></i><?php esc_html_e( 'توزيع الأنظمة', 'rsyi-system' ); ?></div>
        <div class="card-body d-flex align-items-center justify-content-center">
          <div class="rsyi-chart-wrapper w-100"><canvas id="rsyi-pie-chart"></canvas></div>
        </div>
      </div>
    </div>
    <div class="col-lg-5 col-md-6 mb-3">
      <div class="card rsyi-card h-100">
        <div class="card-header"><i class="fa fa-bar-chart ml-2"></i><?php esc_html_e( 'الحضور هذا الأسبوع', 'rsyi-system' ); ?></div>
        <div class="card-body">
          <div class="rsyi-chart-wrapper"><canvas id="rsyi-bar-chart"></canvas></div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 mb-3">
      <div class="card rsyi-card h-100">
        <div class="card-header"><i class="fa fa-bolt ml-2 text-warning"></i><?php esc_html_e( 'وصول سريع', 'rsyi-system' ); ?></div>
        <div class="card-body">
          <a class="rsyi-quick-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-hr-employees&action=new' ) ); ?>">
            <i class="fa fa-user-plus text-primary"></i><?php esc_html_e( 'إضافة موظف', 'rsyi-system' ); ?>
          </a>
          <a class="rsyi-quick-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-sa-students&action=new' ) ); ?>">
            <i class="fa fa-graduation-cap text-warning"></i><?php esc_html_e( 'تسجيل طالب', 'rsyi-system' ); ?>
          </a>
          <a class="rsyi-quick-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-wh-add-orders&action=new' ) ); ?>">
            <i class="fa fa-plus text-success"></i><?php esc_html_e( 'أمر إضافة مخزون', 'rsyi-system' ); ?>
          </a>
          <a class="rsyi-quick-btn" href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-wh-withdrawal-orders&action=new' ) ); ?>">
            <i class="fa fa-minus text-danger"></i><?php esc_html_e( 'أمر صرف مخزون', 'rsyi-system' ); ?>
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- ── Recent Audit Log ──────────────────────────────────────────────── -->
  <p class="rsyi-section-title">
    <span><i class="fa fa-history ml-2"></i><?php esc_html_e( 'آخر العمليات', 'rsyi-system' ); ?></span>
    <a href="<?php echo esc_url( admin_url( 'admin.php?page=rsyi-audit-log' ) ); ?>" class="btn btn-sm btn-outline-secondary">
      <?php esc_html_e( 'عرض الكل', 'rsyi-system' ); ?>
    </a>
  </p>
  <div class="card rsyi-card mb-4">
    <div class="card-body p-0">
      <?php if ( empty( $recent_logs ) ) : ?>
        <p class="text-muted p-3 mb-0"><?php esc_html_e( 'لا توجد عمليات مسجّلة بعد.', 'rsyi-system' ); ?></p>
      <?php else : ?>
      <table class="table rsyi-table mb-0">
        <thead>
          <tr>
            <th><?php esc_html_e( 'النظام', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'العملية', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'المستخدم', 'rsyi-system' ); ?></th>
            <th><?php esc_html_e( 'التاريخ', 'rsyi-system' ); ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( $recent_logs as $log ) :
              $user = get_userdata( $log->user_id );
              $badge_class = match( $log->system ) {
                  'hr'        => 'badge-hr',
                  'warehouse' => 'badge-wh',
                  'students'  => 'badge-sa',
                  default     => 'badge-secondary',
              };
          ?>
          <tr>
            <td><span class="badge badge-sys <?php echo esc_attr( $badge_class ); ?>"><?php echo esc_html( strtoupper( $log->system ) ); ?></span></td>
            <td><?php echo esc_html( $log->action ); ?></td>
            <td><?php echo $user ? esc_html( $user->display_name ) : '—'; ?></td>
            <td><?php echo esc_html( wp_date( 'Y/m/d H:i', strtotime( $log->created_at ) ) ); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>

</div><!-- .rsyi-wrap -->
