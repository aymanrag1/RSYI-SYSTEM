<?php
/**
 * Accounting — Coming Soon | الحسابات — قيد الإنشاء
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="rsyi-coming-soon">
    <div class="rsyi-soon-icon"><i class="fa-solid fa-calculator"></i></div>
    <h2>نظام الحسابات <span style="color:#94a3b8;font-size:.7em">| Accounting System</span></h2>
    <p>
        هذا القسم قيد الإنشاء حالياً وسيتضمن:<br>
        <strong>This module is under development and will include:</strong>
    </p>
    <div class="row g-2 mt-2" style="max-width:500px;text-align:right">
        <?php
        $features = [
            ['ar'=>'نظام الرواتب والمكافآت',    'en'=>'Payroll & Bonuses',        'icon'=>'fa-money-bill-wave'],
            ['ar'=>'رسوم الطلاب والمدفوعات',    'en'=>'Student Fees & Payments',  'icon'=>'fa-graduation-cap'],
            ['ar'=>'الفواتير وإدارة الموردين',  'en'=>'Invoices & Vendor Mgmt',   'icon'=>'fa-file-invoice'],
            ['ar'=>'المخازن تحت الحسابات',      'en'=>'Warehouse under Accounting','icon'=>'fa-warehouse'],
            ['ar'=>'التقارير المالية',           'en'=>'Financial Reports',         'icon'=>'fa-chart-line'],
            ['ar'=>'الميزانية والمصاريف',       'en'=>'Budget & Expenses',         'icon'=>'fa-scale-balanced'],
        ];
        foreach($features as $f): ?>
            <div class="col-6">
                <div class="d-flex align-items-center gap-2 p-2" style="background:#f8fafc;border-radius:8px;font-size:.83rem">
                    <i class="fa-solid <?php echo esc_attr($f['icon']); ?>" style="color:#1d4ed8;width:20px"></i>
                    <div>
                        <div class="rsyi-text-ar"><?php echo esc_html($f['ar']); ?></div>
                        <div class="rsyi-text-muted" style="font-size:.7rem"><?php echo esc_html($f['en']); ?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="rsyi-alert rsyi-alert-info mt-4" style="max-width:480px">
        <i class="fa-solid fa-circle-info"></i>
        المخازن متاحة الآن كقسم مستقل حتى اكتمال نظام الحسابات.
        <br><small>Warehouse is available as a standalone module until Accounting is complete.</small>
    </div>
</div>
