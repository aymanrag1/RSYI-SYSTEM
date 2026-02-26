#!/bin/bash
# =============================================================
#  RSYI Dashboard — Automated Server Setup Script
#  سكريبت الإعداد التلقائي للسيرفر
# =============================================================
#
#  كيفية الاستخدام (Usage):
#  -------------------------
#  1. اتصل بالسيرفر: ssh root@your-server-ip
#  2. شغّل الأمر التالي بالكامل:
#
#     bash <(curl -sL https://raw.githubusercontent.com/aymanrag1/RSYI-SYSTEM/claude/clone-bootstrap-dashboard-JME2s/setup-server.sh)
#
#  أو حمّل الملف ثم شغّله:
#     wget https://raw.githubusercontent.com/aymanrag1/RSYI-SYSTEM/claude/clone-bootstrap-dashboard-JME2s/setup-server.sh
#     chmod +x setup-server.sh
#     ./setup-server.sh
#
# =============================================================

set -e  # أوقف عند أي خطأ

# ── الألوان ───────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
BOLD='\033[1m'
NC='\033[0m'

# ── دوال مساعدة ───────────────────────────────────────────
step()    { echo -e "\n${BLUE}${BOLD}[ $(date +%H:%M:%S) ] ▶  $1${NC}"; }
ok()      { echo -e "${GREEN}✔  $1${NC}"; }
warn()    { echo -e "${YELLOW}⚠  $1${NC}"; }
error()   { echo -e "${RED}✘  $1${NC}"; exit 1; }
info()    { echo -e "${CYAN}ℹ  $1${NC}"; }
divider() { echo -e "${BOLD}─────────────────────────────────────────${NC}"; }

# ── شاشة الترحيب ──────────────────────────────────────────
clear
echo -e "${BOLD}${BLUE}"
echo "  ██████╗ ███████╗██╗   ██╗██╗"
echo "  ██╔══██╗██╔════╝╚██╗ ██╔╝██║"
echo "  ██████╔╝███████╗ ╚████╔╝ ██║"
echo "  ██╔══██╗╚════██║  ╚██╔╝  ██║"
echo "  ██║  ██║███████║   ██║   ██║"
echo "  ╚═╝  ╚═╝╚══════╝   ╚═╝   ╚═╝"
echo -e "${NC}"
echo -e "${BOLD}       RSYI Dashboard — Server Setup${NC}"
echo -e "       إعداد السيرفر التلقائي\n"
divider

# ── التحقق من صلاحيات root ────────────────────────────────
if [[ $EUID -ne 0 ]]; then
    error "يجب تشغيل السكريبت كـ root. استخدم: sudo bash setup-server.sh"
fi

# ── التحقق من نوع نظام التشغيل ────────────────────────────
if ! command -v apt-get &>/dev/null; then
    error "هذا السكريبت يعمل على Ubuntu/Debian فقط"
fi

# ── جمع المعلومات من المستخدم ─────────────────────────────
echo -e "${BOLD}قبل البدء — أدخل بعض المعلومات:${NC}\n"

# الدومين
read -rp "$(echo -e "${CYAN}🌐 اسم الدومين (مثال: rsyinstitute.com):${NC} ")" DOMAIN
DOMAIN=${DOMAIN:-rsyinstitute.com}

# كلمة مرور MySQL
echo ""
read -rsp "$(echo -e "${CYAN}🔒 كلمة مرور قاعدة البيانات (اضغط Enter للتوليد التلقائي):${NC} ")" DB_PASS
echo ""
if [[ -z "$DB_PASS" ]]; then
    DB_PASS=$(openssl rand -base64 16 | tr -d '/+=' | head -c 16)
    info "تم توليد كلمة مرور تلقائية: ${BOLD}${DB_PASS}${NC}"
fi

DB_NAME="rsyi_db"
DB_USER="rsyi_user"
APP_DIR="/var/www/rsyi"
PHP_VER="8.2"

echo ""
divider
echo -e "${BOLD}الإعدادات المختارة:${NC}"
echo -e "  الدومين:              ${GREEN}${DOMAIN}${NC}"
echo -e "  مجلد التثبيت:        ${GREEN}${APP_DIR}${NC}"
echo -e "  قاعدة البيانات:      ${GREEN}${DB_NAME}${NC}"
echo -e "  مستخدم قاعدة البيانات: ${GREEN}${DB_USER}${NC}"
echo -e "  إصدار PHP:            ${GREEN}${PHP_VER}${NC}"
divider

read -rp "$(echo -e "\n${YELLOW}▶ هل تريد البدء؟ (اضغط Enter للمتابعة أو Ctrl+C للإلغاء)${NC}")" _
echo ""

# ══════════════════════════════════════════════════════════
# الخطوة 1 — تحديث النظام
# ══════════════════════════════════════════════════════════
step "الخطوة 1/7 — تحديث النظام"
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get upgrade -y -qq
ok "تم تحديث النظام"

# ══════════════════════════════════════════════════════════
# الخطوة 2 — تثبيت PHP + الإضافات
# ══════════════════════════════════════════════════════════
step "الخطوة 2/7 — تثبيت PHP ${PHP_VER} وإضافاته"

apt-get install -y -qq software-properties-common curl git unzip

# إضافة مستودع PHP
if ! grep -q "ondrej/php" /etc/apt/sources.list.d/*.list 2>/dev/null; then
    add-apt-repository ppa:ondrej/php -y -qq
    apt-get update -qq
fi

apt-get install -y -qq \
    php${PHP_VER} \
    php${PHP_VER}-fpm \
    php${PHP_VER}-mysql \
    php${PHP_VER}-mbstring \
    php${PHP_VER}-xml \
    php${PHP_VER}-curl \
    php${PHP_VER}-zip \
    php${PHP_VER}-bcmath \
    php${PHP_VER}-tokenizer \
    php${PHP_VER}-fileinfo \
    php${PHP_VER}-openssl \
    php${PHP_VER}-intl

ok "تم تثبيت PHP ${PHP_VER}"
info "إصدار PHP: $(php -r 'echo PHP_VERSION;')"

# ══════════════════════════════════════════════════════════
# الخطوة 3 — تثبيت Nginx
# ══════════════════════════════════════════════════════════
step "الخطوة 3/7 — تثبيت Nginx"
apt-get install -y -qq nginx
systemctl enable nginx
systemctl start nginx
ok "تم تثبيت Nginx وتشغيله"

# ══════════════════════════════════════════════════════════
# الخطوة 4 — تثبيت MySQL وإنشاء قاعدة البيانات
# ══════════════════════════════════════════════════════════
step "الخطوة 4/7 — تثبيت MySQL وإنشاء قاعدة البيانات"

apt-get install -y -qq mysql-server
systemctl enable mysql
systemctl start mysql

# إنشاء قاعدة البيانات والمستخدم
mysql -u root <<MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

ok "تم إنشاء قاعدة البيانات: ${DB_NAME}"
ok "تم إنشاء المستخدم: ${DB_USER}"

# ══════════════════════════════════════════════════════════
# الخطوة 5 — تثبيت Composer ورفع الكود
# ══════════════════════════════════════════════════════════
step "الخطوة 5/7 — تثبيت Composer ورفع الكود"

# Composer
if ! command -v composer &>/dev/null; then
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer --quiet
    ok "تم تثبيت Composer"
else
    ok "Composer موجود بالفعل"
fi

# Clone الكود
if [[ -d "${APP_DIR}" ]]; then
    warn "المجلد ${APP_DIR} موجود بالفعل — سيتم التحديث"
    cd "${APP_DIR}"
    git pull origin claude/clone-bootstrap-dashboard-JME2s -q
else
    git clone --branch claude/clone-bootstrap-dashboard-JME2s \
        https://github.com/aymanrag1/RSYI-SYSTEM.git "${APP_DIR}" -q
    cd "${APP_DIR}"
fi
ok "تم رفع الكود"

# تثبيت مكتبات Laravel
composer install --no-dev --optimize-autoloader --quiet
ok "تم تثبيت مكتبات Laravel"

# ══════════════════════════════════════════════════════════
# الخطوة 6 — إعداد الصلاحيات
# ══════════════════════════════════════════════════════════
step "الخطوة 6/7 — إعداد الصلاحيات"

chown -R www-data:www-data "${APP_DIR}"
chmod -R 755 "${APP_DIR}"
chmod -R 775 "${APP_DIR}/storage"
chmod -R 775 "${APP_DIR}/bootstrap/cache"

ok "تم ضبط الصلاحيات"

# ══════════════════════════════════════════════════════════
# الخطوة 7 — إعداد Nginx
# ══════════════════════════════════════════════════════════
step "الخطوة 7/7 — إعداد Nginx"

cat > /etc/nginx/sites-available/rsyi <<NGINX_CONF
server {
    listen 80;
    server_name ${DOMAIN} www.${DOMAIN};
    root ${APP_DIR}/public;
    index index.php index.html;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:/var/run/php/php${PHP_VER}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
NGINX_CONF

# إزالة الإعداد الافتراضي وتفعيل الإعداد الجديد
rm -f /etc/nginx/sites-enabled/default
ln -sf /etc/nginx/sites-available/rsyi /etc/nginx/sites-enabled/rsyi

# فحص صحة إعداد Nginx
nginx -t
systemctl reload nginx
ok "تم إعداد Nginx"

# ══════════════════════════════════════════════════════════
# حفظ بيانات قاعدة البيانات للمعالج
# ══════════════════════════════════════════════════════════
mkdir -p "${APP_DIR}/storage"
cat > "${APP_DIR}/storage/.setup_db_info" <<DB_INFO
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=${DB_NAME}
DB_USER=${DB_USER}
DB_PASS=${DB_PASS}
DB_INFO

chown www-data:www-data "${APP_DIR}/storage/.setup_db_info"
chmod 600 "${APP_DIR}/storage/.setup_db_info"

# ══════════════════════════════════════════════════════════
# ✅ اكتمل التثبيت
# ══════════════════════════════════════════════════════════
echo ""
divider
echo -e "${GREEN}${BOLD}"
echo "   ✅  اكتمل إعداد السيرفر بنجاح!"
echo -e "${NC}"
divider
echo ""
echo -e "${BOLD}الخطوة التالية — افتح المتصفح وادخل على:${NC}"
echo ""
echo -e "   ${GREEN}${BOLD}http://${DOMAIN}${NC}"
echo ""
echo -e "${CYAN}ستظهر لك صفحة معالج التنصيب تلقائياً."
echo -e "أدخل البيانات التالية في خطوة قاعدة البيانات:${NC}"
echo ""
echo -e "   Host:      ${BOLD}127.0.0.1${NC}"
echo -e "   Port:      ${BOLD}3306${NC}"
echo -e "   Database:  ${BOLD}${DB_NAME}${NC}"
echo -e "   Username:  ${BOLD}${DB_USER}${NC}"
echo -e "   Password:  ${BOLD}${DB_PASS}${NC}"
echo ""

# حفظ البيانات في ملف نصي واضح على السيرفر
cat > /root/rsyi-credentials.txt <<CREDS
==============================================
 RSYI Dashboard — بيانات التثبيت
==============================================
 الموقع:              http://${DOMAIN}
 مجلد التثبيت:       ${APP_DIR}

 قاعدة البيانات:
   Host:      127.0.0.1
   Port:      3306
   Database:  ${DB_NAME}
   Username:  ${DB_USER}
   Password:  ${DB_PASS}
==============================================
 احتفظ بهذا الملف في مكان آمن!
 cat /root/rsyi-credentials.txt
==============================================
CREDS
chmod 600 /root/rsyi-credentials.txt

echo -e "${YELLOW}${BOLD}💾 تم حفظ هذه البيانات في: /root/rsyi-credentials.txt${NC}"
echo ""
divider
echo ""
