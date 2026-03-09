/**
 * RSYI Unified Management System — Admin JavaScript
 * النظام الإداري الموحد — الجافاسكربت
 */
/* global rsyiSys, jQuery */

(function($) {
    'use strict';

    // ── Init ─────────────────────────────────────────────────────────────────
    $(document).ready(function() {
        RSYI.notifications.init();
        RSYI.moduleToggles.init();
        RSYI.forms.init();
        RSYI.tables.init();
    });

    // ── Namespace ─────────────────────────────────────────────────────────────
    window.RSYI = window.RSYI || {};

    // ── Notifications ─────────────────────────────────────────────────────────
    RSYI.notifications = {
        init: function() {
            // Load notifications on dropdown open
            $('.rsyi-notif-dropdown').on('show.bs.dropdown', this.load.bind(this));
            // Mark single read
            $(document).on('click', '.rsyi-notif-item', this.markRead.bind(this));
            // Mark all read
            $(document).on('click', '.rsyi-mark-all-read', this.markAllRead.bind(this));
        },
        load: function() {
            var $list = $('#rsyi-notif-list');
            $.post(rsyiSys.ajaxUrl, {
                action: 'rsyi_sys_get_notifications',
                nonce:  rsyiSys.nonce
            }, function(res) {
                if (!res.success || !res.data.length) return;
                var html = '';
                $.each(res.data, function(i, n) {
                    var cls = n.is_read == 0 ? 'unread' : '';
                    html += '<div class="rsyi-notif-item ' + cls + '" data-id="' + n.id + '">' +
                        '<div class="rsyi-notif-title fw-semibold">' + $('<div>').text(n.title).html() + '</div>' +
                        '<div class="text-muted">' + $('<div>').text(n.message || '').html() + '</div>' +
                        '<div class="rsyi-notif-time text-muted" style="font-size:.7rem">' + n.created_at + '</div>' +
                        '</div>';
                });
                $list.html(html);
            });
        },
        markRead: function(e) {
            var $item = $(e.currentTarget);
            var id    = $item.data('id');
            if (!id) return;
            $.post(rsyiSys.ajaxUrl, {
                action: 'rsyi_sys_mark_notification_read',
                nonce:  rsyiSys.nonce,
                id:     id
            });
            $item.removeClass('unread');
            // Update badge
            var badge = $('.rsyi-badge-dot');
            var count = parseInt(badge.text(), 10) - 1;
            count > 0 ? badge.text(count) : badge.remove();
        },
        markAllRead: function(e) {
            e.preventDefault();
            $('.rsyi-notif-item.unread').each(function() {
                $(this).trigger('click');
            });
        }
    };

    // ── Module Toggles ────────────────────────────────────────────────────────
    RSYI.moduleToggles = {
        init: function() {
            $(document).on('click', '.rsyi-toggle-switch[data-module]', this.toggle.bind(this));
        },
        toggle: function(e) {
            var $btn    = $(e.currentTarget);
            var module  = $btn.data('module');
            var enabled = !$btn.hasClass('on');

            $btn.toggleClass('on', enabled);

            $.post(rsyiSys.ajaxUrl, {
                action:  'rsyi_toggle_module',
                nonce:   rsyiSys.nonce,
                module:  module,
                enabled: enabled ? 1 : 0
            }, function(res) {
                if (res.success) {
                    RSYI.toast(enabled ? rsyiSys.i18n.module_enabled : rsyiSys.i18n.module_disabled, 'success');
                    // Reload to reflect menu changes after short delay
                    setTimeout(function() { location.reload(); }, 1000);
                }
            });
        }
    };

    // ── Forms ─────────────────────────────────────────────────────────────────
    RSYI.forms = {
        init: function() {
            // Confirm delete
            $(document).on('click', '.rsyi-confirm-delete', function(e) {
                if (!confirm(rsyiSys.i18n.confirm_delete)) {
                    e.preventDefault();
                    return false;
                }
            });

            // AJAX save settings
            $(document).on('submit', '#rsyi-settings-form', function(e) {
                e.preventDefault();
                var $form = $(this);
                var $btn  = $form.find('[type=submit]');
                $btn.prop('disabled', true).text(rsyiSys.i18n.saving);

                $.post(rsyiSys.ajaxUrl, $form.serialize() + '&action=rsyi_save_settings&nonce=' + rsyiSys.nonce, function(res) {
                    $btn.prop('disabled', false).text(rsyiSys.i18n.saved);
                    if (res.success) {
                        RSYI.toast(rsyiSys.i18n.saved, 'success');
                    } else {
                        RSYI.toast(rsyiSys.i18n.error, 'error');
                    }
                    setTimeout(function() { $btn.text('حفظ الإعدادات | Save Settings'); }, 2000);
                }).fail(function() {
                    $btn.prop('disabled', false);
                    RSYI.toast(rsyiSys.i18n.error, 'error');
                });
            });
        }
    };

    // ── Tables ────────────────────────────────────────────────────────────────
    RSYI.tables = {
        init: function() {
            // Live search
            $(document).on('input', '.rsyi-table-search', function() {
                var term = $(this).val().toLowerCase();
                var $table = $($(this).data('target') || '.rsyi-table');
                $table.find('tbody tr').each(function() {
                    var text = $(this).text().toLowerCase();
                    $(this).toggle(text.indexOf(term) > -1);
                });
            });
        }
    };

    // ── Toast notifications ───────────────────────────────────────────────────
    RSYI.toast = function(msg, type) {
        type = type || 'info';
        var color = { success: '#16a34a', error: '#dc2626', info: '#1d4ed8', warning: '#d97706' };
        var $t = $('<div>').css({
            position:     'fixed',
            bottom:       '24px',
            left:         '24px',
            background:   color[type] || color.info,
            color:        '#fff',
            padding:      '12px 20px',
            borderRadius: '8px',
            fontFamily:   "'Cairo', sans-serif",
            fontSize:     '.87rem',
            fontWeight:   '600',
            boxShadow:    '0 4px 12px rgba(0,0,0,.2)',
            zIndex:       99999,
            transition:   'opacity .3s',
            direction:    'rtl',
            maxWidth:     '320px'
        }).text(msg).appendTo('body');

        setTimeout(function() { $t.css('opacity', 0); setTimeout(function() { $t.remove(); }, 300); }, 3000);
    };

    // ── Charts helper ─────────────────────────────────────────────────────────
    RSYI.charts = {
        /**
         * إنشاء رسم بياني دوناتي | Create donut chart
         */
        donut: function(canvasId, labels, data, colors) {
            var ctx = document.getElementById(canvasId);
            if (!ctx || typeof Chart === 'undefined') return;
            return new Chart(ctx, {
                type: 'doughnut',
                data: { labels: labels, datasets: [{ data: data, backgroundColor: colors, borderWidth: 2 }] },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } }, cutout: '65%' }
            });
        },
        /**
         * رسم بياني أعمدة | Bar chart
         */
        bar: function(canvasId, labels, datasets) {
            var ctx = document.getElementById(canvasId);
            if (!ctx || typeof Chart === 'undefined') return;
            return new Chart(ctx, {
                type: 'bar',
                data: { labels: labels, datasets: datasets },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        }
    };

})(jQuery);
