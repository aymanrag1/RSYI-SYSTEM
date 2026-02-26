/**
 * RSYI Unified System — Admin JavaScript
 */
/* global rsyiSys, jQuery, Chart */

(function ($) {
    'use strict';

    // ── Init ──────────────────────────────────────────────────────────────────

    $(document).ready(function () {
        initSelect2();
        initDismissibleAlerts();
        initNotifications();
        initConfirmDelete();
        initCharts();
        initDateDisplay();
    });

    // ── Select2 ───────────────────────────────────────────────────────────────

    function initSelect2() {
        if ($.fn.select2) {
            $('.rsyi-select2').select2({
                dir: 'rtl',
                width: '100%',
                language: {
                    noResults: function () { return 'لا توجد نتائج'; },
                    searching:  function () { return 'جارٍ البحث...'; },
                }
            });
        }
    }

    // ── Alerts ────────────────────────────────────────────────────────────────

    function initDismissibleAlerts() {
        $(document).on('click', '.rsyi-alert .close', function () {
            $(this).closest('.rsyi-alert').fadeOut(200, function () { $(this).remove(); });
        });
    }

    // ── Notifications ─────────────────────────────────────────────────────────

    function initNotifications() {
        var $badge = $('#rsyi-notif-count');

        if (rsyiSys.unread > 0) {
            $badge.text(rsyiSys.unread).show();
        }

        // Mark single notification read
        $(document).on('click', '.rsyi-notif-item', function () {
            var id = $(this).data('id');
            if (!$(this).hasClass('read')) {
                $.post(rsyiSys.ajaxUrl, {
                    action: 'rsyi_sys_mark_notification_read',
                    nonce:  rsyiSys.nonce,
                    id:     id
                });
                $(this).addClass('read');
            }
        });
    }

    // ── Confirm Delete ────────────────────────────────────────────────────────

    function initConfirmDelete() {
        $(document).on('click', '.rsyi-confirm-delete', function (e) {
            if (!window.confirm(rsyiSys.i18n.confirm_delete)) {
                e.preventDefault();
            }
        });
    }

    // ── Charts ────────────────────────────────────────────────────────────────

    function initCharts() {
        // Dashboard doughnut chart
        var pieCtx = document.getElementById('rsyi-pie-chart');
        if (pieCtx && typeof Chart !== 'undefined') {
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['موظفون', 'طلاب', 'مخزون'],
                    datasets: [{
                        data: [142, 215, 324],
                        backgroundColor: ['#007bff', '#fd7e14', '#28a745'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: { position: 'bottom', labels: { fontFamily: 'Segoe UI', fontSize: 12 } },
                    cutoutPercentage: 65
                }
            });
        }

        // Attendance bar chart
        var barCtx = document.getElementById('rsyi-bar-chart');
        if (barCtx && typeof Chart !== 'undefined') {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس'],
                    datasets: [
                        {
                            label: 'حاضر',
                            data: [135, 138, 130, 140, 137, 142],
                            backgroundColor: '#007bff'
                        },
                        {
                            label: 'غائب',
                            data: [7, 4, 12, 2, 5, 0],
                            backgroundColor: '#dc3545'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        yAxes: [{ ticks: { beginAtZero: true } }]
                    },
                    legend: { position: 'bottom', labels: { fontFamily: 'Segoe UI', fontSize: 12 } }
                }
            });
        }
    }

    // ── Date display ──────────────────────────────────────────────────────────

    function initDateDisplay() {
        var el = document.getElementById('rsyi-current-date');
        if (el) {
            el.textContent = new Date().toLocaleDateString('ar-EG', {
                weekday: 'long',
                year:    'numeric',
                month:   'long',
                day:     'numeric'
            });
        }
    }

    // ── AJAX form submit ──────────────────────────────────────────────────────

    window.rsyiAjaxSubmit = function (formId, action, callback) {
        var $form = $('#' + formId);
        var $btn  = $form.find('[type="submit"]');
        var data  = $form.serializeArray();

        data.push({ name: 'action', value: action });
        data.push({ name: 'nonce',  value: rsyiSys.nonce });

        $btn.prop('disabled', true).text(rsyiSys.i18n.saving);

        $.post(rsyiSys.ajaxUrl, data)
            .done(function (res) {
                if (res.success) {
                    rsyiShowAlert('success', rsyiSys.i18n.saved);
                    if (typeof callback === 'function') callback(res.data);
                } else {
                    rsyiShowAlert('danger', res.data || rsyiSys.i18n.error);
                }
            })
            .fail(function () {
                rsyiShowAlert('danger', rsyiSys.i18n.error);
            })
            .always(function () {
                $btn.prop('disabled', false).text(rsyiSys.i18n.saving.replace('...', ''));
            });
    };

    // ── Alert helper ──────────────────────────────────────────────────────────

    window.rsyiShowAlert = function (type, msg) {
        var $alert = $('<div class="alert alert-' + type + ' rsyi-alert alert-dismissible">' +
            msg + '<button type="button" class="close"><span>&times;</span></button></div>');
        $('.rsyi-wrap').prepend($alert);
        setTimeout(function () { $alert.fadeOut(400, function () { $alert.remove(); }); }, 4000);
    };

})(jQuery);
