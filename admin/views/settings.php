<?php
/**
 * RSYI Settings Page — صفحة الإعدادات
 */
defined( 'ABSPATH' ) || exit;

// Save handler
if ( isset($_POST['rsyi_save_settings'], $_POST['_wpnonce']) && wp_verify_nonce( sanitize_key($_POST['_wpnonce']), 'rsyi_settings' ) ) {
    $opts = get_option('rsyi_sys_options', RSYI_Sys_Settings::DEFAULTS);

    $opts['institute_name']       = sanitize_text_field($_POST['institute_name'] ?? '');
    $opts['institute_name_en']    = sanitize_text_field($_POST['institute_name_en'] ?? '');
    $opts['institute_logo_url']   = esc_url_raw($_POST['institute_logo_url'] ?? '');
    $opts['admin_email']          = sanitize_email($_POST['admin_email'] ?? '');
    $opts['default_language']     = in_array($_POST['default_language']??'ar',['ar','en'],'ar') ? sanitize_key($_POST['default_language']) : 'ar';
    $opts['items_per_page']       = (string) absint($_POST['items_per_page'] ?? 25);
    $opts['hr_enabled']           = empty($_POST['hr_enabled'])        ? '0' : '1';
    $opts['students_enabled']     = empty($_POST['students_enabled'])   ? '0' : '1';
    $opts['warehouse_enabled']    = empty($_POST['warehouse_enabled'])  ? '0' : '1';
    $opts['audit_log_enabled']    = empty($_POST['audit_log_enabled'])  ? '0' : '1';
    $opts['notifications_enabled']= empty($_POST['notifications_enabled']) ? '0' : '1';

    update_option('rsyi_sys_options', $opts);
    RSYI_Sys_Settings::init();

    echo '<div class="rsyi-alert rsyi-alert-success mb-3"><i class="fa-solid fa-circle-check"></i> تم حفظ الإعدادات بنجاح | Settings saved successfully</div>';
}

$opts = get_option('rsyi_sys_options', RSYI_Sys_Settings::DEFAULTS);
?>

<div class="rsyi-page-header">
    <div class="rsyi-page-title">
        الإعدادات
        <small>System Settings — configure modules and preferences</small>
    </div>
</div>

<form method="post" action="" style="max-width:820px">
    <?php wp_nonce_field('rsyi_settings'); ?>
    <input type="hidden" name="rsyi_save_settings" value="1">

    <!-- ── معلومات المعهد | Institute Info ──────────────────────────────── -->
    <div class="rsyi-card mb-3">
        <div class="rsyi-settings-section-title">
            <i class="fa-solid fa-building me-2"></i>
            معلومات المعهد <small>| Institute Information</small>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <div class="rsyi-form-group">
                    <label class="rsyi-label">اسم المعهد (عربي) <small>| Arabic Name</small></label>
                    <input type="text" name="institute_name" class="rsyi-input"
                           value="<?php echo esc_attr($opts['institute_name']??''); ?>" dir="rtl">
                </div>
            </div>
            <div class="col-md-6">
                <div class="rsyi-form-group">
                    <label class="rsyi-label">اسم المعهد (إنجليزي) <small>| English Name</small></label>
                    <input type="text" name="institute_name_en" class="rsyi-input"
                           value="<?php echo esc_attr($opts['institute_name_en']??''); ?>" dir="ltr">
                </div>
            </div>
            <div class="col-md-6">
                <div class="rsyi-form-group">
                    <label class="rsyi-label">رابط شعار المعهد <small>| Logo URL</small></label>
                    <input type="url" name="institute_logo_url" class="rsyi-input"
                           value="<?php echo esc_attr($opts['institute_logo_url']??''); ?>" dir="ltr"
                           placeholder="https://...">
                </div>
            </div>
            <div class="col-md-6">
                <div class="rsyi-form-group">
                    <label class="rsyi-label">البريد الإلكتروني للمدير <small>| Admin Email</small></label>
                    <input type="email" name="admin_email" class="rsyi-input"
                           value="<?php echo esc_attr($opts['admin_email']??''); ?>" dir="ltr">
                </div>
            </div>
        </div>
    </div>

    <!-- ── تفعيل الوحدات | Module Activation ────────────────────────────── -->
    <div class="rsyi-card mb-3">
        <div class="rsyi-settings-section-title">
            <i class="fa-solid fa-puzzle-piece me-2"></i>
            تفعيل الوحدات <small>| Module Activation</small>
        </div>
        <p class="rsyi-text-muted mb-3" style="font-size:.85rem">
            قم بتفعيل أو تعطيل كل وحدة حسب احتياجاتك. الوحدات المعطّلة لن تظهر في القائمة ولن تُحمَّل بياناتها.
            <br><small>Enable or disable each module. Disabled modules won't appear in the menu. Existing data is preserved.</small>
        </p>

        <?php
        $modules_cfg = [
            'hr' => [
                'ar'   => 'نظام الموارد البشرية',
                'en'   => 'Human Resources System',
                'icon' => 'fa-users-gear',
                'desc_ar' => 'إدارة الموظفين، الإجازات، الحضور، العمل الإضافي، المخالفات',
                'desc_en' => 'Employees, leaves, attendance, overtime, violations management',
            ],
            'students' => [
                'ar'   => 'نظام شئون الطلاب',
                'en'   => 'Student Affairs System',
                'icon' => 'fa-user-graduate',
                'desc_ar' => 'قيد الطلاب، المستندات، التصاريح، السلوك، الدفعات',
                'desc_en' => 'Student enrollment, documents, permits, behavior, cohorts',
            ],
            'warehouse' => [
                'ar'   => 'نظام المخازن',
                'en'   => 'Warehouse Management System',
                'icon' => 'fa-warehouse',
                'desc_ar' => 'إدارة الأصناف، الموردين، أوامر الصرف والإضافة، التقارير',
                'desc_en' => 'Products, suppliers, add/withdraw orders, reports',
            ],
        ];
        foreach($modules_cfg as $key => $cfg):
            $enabled = ($opts[$key.'_enabled']??'1') === '1';
        ?>
        <div class="rsyi-module-card <?php echo $enabled?'enabled':'disabled'; ?> mb-3">
            <div class="d-flex align-items-center gap-3">
                <span class="rsyi-stat-icon <?php echo $enabled?'green':'gray'; ?>" style="width:44px;height:44px;font-size:1.1rem">
                    <i class="fa-solid <?php echo esc_attr($cfg['icon']); ?>"></i>
                </span>
                <div>
                    <div class="fw-bold rsyi-text-ar"><?php echo esc_html($cfg['ar']); ?>
                        <small class="rsyi-text-muted fw-normal ms-1">| <?php echo esc_html($cfg['en']); ?></small>
                    </div>
                    <div style="font-size:.78rem;color:#6b7280"><?php echo esc_html($cfg['desc_ar']); ?></div>
                    <div style="font-size:.72rem;color:#9ca3af"><?php echo esc_html($cfg['desc_en']); ?></div>
                </div>
            </div>
            <label class="d-flex align-items-center gap-2 mb-0 cursor-pointer">
                <input type="checkbox" name="<?php echo esc_attr($key.'_enabled'); ?>" value="1"
                       <?php checked($enabled); ?> class="d-none rsyi-module-checkbox" data-module="<?php echo esc_attr($key); ?>">
                <div class="rsyi-toggle-switch <?php echo $enabled?'on':''; ?>" data-module="<?php echo esc_attr($key); ?>" onclick="this.previousElementSibling.click();this.classList.toggle('on')"></div>
                <span style="font-size:.82rem"><?php echo $enabled?'مفعّل | Active':'معطّل | Disabled'; ?></span>
            </label>
        </div>
        <?php endforeach; ?>

        <!-- Accounting — Coming soon -->
        <div class="rsyi-module-card disabled">
            <div class="d-flex align-items-center gap-3">
                <span class="rsyi-stat-icon gray" style="width:44px;height:44px;font-size:1.1rem">
                    <i class="fa-solid fa-calculator"></i>
                </span>
                <div>
                    <div class="fw-bold rsyi-text-ar">نظام الحسابات
                        <small class="rsyi-text-muted fw-normal ms-1">| Accounting System</small>
                    </div>
                    <div style="font-size:.78rem;color:#6b7280">الرواتب، المدفوعات، الرسوم، المخازن تحت قسم الحسابات</div>
                    <div style="font-size:.72rem;color:#9ca3af">Payroll, payments, fees, warehouse under accounting</div>
                </div>
            </div>
            <span class="rsyi-badge rsyi-badge-warning">قيد الإنشاء | Coming Soon</span>
        </div>
    </div>

    <!-- ── الإعدادات العامة | General Settings ──────────────────────────── -->
    <div class="rsyi-card mb-3">
        <div class="rsyi-settings-section-title">
            <i class="fa-solid fa-sliders me-2"></i>
            الإعدادات العامة <small>| General Settings</small>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="rsyi-form-group">
                    <label class="rsyi-label">اللغة الافتراضية <small>| Default Language</small></label>
                    <select name="default_language" class="rsyi-select">
                        <option value="ar" <?php selected($opts['default_language']??'ar','ar'); ?>>العربية | Arabic</option>
                        <option value="en" <?php selected($opts['default_language']??'ar','en'); ?>>English | الإنجليزية</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="rsyi-form-group">
                    <label class="rsyi-label">عدد العناصر في الصفحة <small>| Items per page</small></label>
                    <input type="number" name="items_per_page" class="rsyi-input" min="10" max="200"
                           value="<?php echo esc_attr($opts['items_per_page']??25); ?>">
                </div>
            </div>
            <div class="col-md-4">
                <div class="rsyi-form-group">
                    <label class="rsyi-label">&#160;</label>
                    <div class="d-flex flex-column gap-2 mt-1">
                        <label class="d-flex align-items-center gap-2 mb-0" style="font-size:.85rem;cursor:pointer">
                            <input type="checkbox" name="audit_log_enabled" value="1" <?php checked($opts['audit_log_enabled']??'1','1'); ?>>
                            سجل العمليات مفعّل <small>| Audit log enabled</small>
                        </label>
                        <label class="d-flex align-items-center gap-2 mb-0" style="font-size:.85rem;cursor:pointer">
                            <input type="checkbox" name="notifications_enabled" value="1" <?php checked($opts['notifications_enabled']??'1','1'); ?>>
                            الإشعارات مفعّلة <small>| Notifications enabled</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-3">
        <button type="submit" class="rsyi-btn rsyi-btn-primary rsyi-btn-lg">
            <i class="fa-solid fa-floppy-disk"></i>
            حفظ الإعدادات | Save Settings
        </button>
        <a href="<?php echo esc_url(admin_url('admin.php?page=rsyi-system')); ?>" class="rsyi-btn rsyi-btn-ghost rsyi-btn-lg">
            <i class="fa-solid fa-arrow-right"></i>
            رجوع | Back
        </a>
    </div>
</form>
