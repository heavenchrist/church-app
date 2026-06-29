#!/bin/bash
set -euo pipefail

# ==============================================================
#  deploy.sh — Church App deployment script (cPanel shared host)
# ==============================================================
#
#  LOCAL WORKFLOW (run on your machine):
#      git add .
#      git commit -m "..."
#      git push origin main
#      ssh user@host 'bash -s' < deploy.sh
#
# ==============================================================

# ── Variables — edit these to match your server ───────────────
APP_DIR="/home/user/domains/example.com/public_html"
PHP_BIN="/opt/cpanel/ea-php82/root/usr/bin/php"
NODE_BIN="/opt/cpanel/ea-nodejs20/bin/node"
NPM_BIN="/opt/cpanel/ea-nodejs20/bin/npm"
BRANCH="main"
USER="user"
GROUP="nobody"

# ── Colors ────────────────────────────────────────────────────
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

info()  { echo -e "${CYAN}[INFO]${NC}  $1"; }
ok()    { echo -e "${GREEN}[OK]${NC}    $1"; }
warn()  { echo -e "${YELLOW}[WARN]${NC}  $1"; }
fail()  { echo -e "${RED}[FAIL]${NC}  $1"; exit 1; }

# ── Pre-flight checks ────────────────────────────────────────
info "Starting deployment for branch '${BRANCH}' …"

if [ ! -d "$APP_DIR" ]; then
    fail "Directory ${APP_DIR} does not exist"
fi

cd "$APP_DIR"

if ! command -v $PHP_BIN &> /dev/null; then
    fail "PHP binary not found at ${PHP_BIN}"
fi

# ── 1. Maintenance mode ──────────────────────────────────────
info "Putting application into maintenance mode …"
$PHP_BIN artisan down --retry=60

# ── 2. Pull latest code ──────────────────────────────────────
info "Pulling latest code from ${BRANCH} …"
git fetch origin
git checkout "$BRANCH"
git reset --hard "origin/${BRANCH}"
ok "Code updated to $(git log --oneline -1)"

# ── 3. Composer dependencies ─────────────────────────────────
info "Installing Composer dependencies (no dev) …"
$PHP_BIN /usr/local/bin/composer install --no-dev --optimize-autoloader --no-interaction
ok "Composer dependencies installed"

# ── 4. NPM assets ────────────────────────────────────────────
if [ -f "package.json" ]; then
    info "Building frontend assets …"
    $NODE_BIN $NPM_BIN ci --no-audit --no-fund 2>/dev/null || $NODE_BIN $NPM_BIN install --no-audit --no-fund
    $NODE_BIN $NPM_BIN run build
    ok "Frontend assets built"
fi

# ── 5. Environment ──────────────────────────────────────────
if [ ! -f ".env" ]; then
    info "No .env found — copying from .env.example …"
    cp .env.example .env
    $PHP_BIN artisan key:generate --force
    warn "!! .env was created from .env.example — review and update it !!"
fi

# ── 6. Laravel migrations ───────────────────────────────────
info "Running database migrations …"
$PHP_BIN artisan migrate --force
ok "Migrations up to date"

# ── 7. Shield permissions ───────────────────────────────────
info "Clearing Spatie permission cache …"
$PHP_BIN artisan permission:cache-reset
ok "Permission cache cleared"

info "Regenerating Shield permissions …"
$PHP_BIN artisan shield:generate --all --no-interaction --minimal 2>&1 || true
ok "Shield permissions synced"

# ── 8. Storage link ─────────────────────────────────────────
info "Ensuring storage:link …"
$PHP_BIN artisan storage:link --force 2>/dev/null || true
ok "Storage link present"

# ── 9. Cache ────────────────────────────────────────────────
info "Caching config, routes, views, events …"
$PHP_BIN artisan optimize
ok "Application cached"

# ── 9. Permissions ──────────────────────────────────────────
info "Setting ownership and permissions …"
chown -R "$USER:$GROUP" storage bootstrap/cache public
chmod -R 755 storage bootstrap/cache public
find storage -type f -exec chmod 644 {} \;
find bootstrap/cache -type f -exec chmod 644 {} \;
ok "Permissions set"

# ── 10. Queue worker restart ────────────────────────────────
info "Restarting queue worker …"
QUEUE_PID=$(pgrep -f "artisan queue:work" || true)
if [ -n "$QUEUE_PID" ]; then
    kill "$QUEUE_PID" 2>/dev/null || true
    sleep 2
    ok "Old queue worker (PID ${QUEUE_PID}) stopped"
fi
nohup $PHP_BIN artisan queue:work --sleep=3 --tries=3 --max-time=3600 > storage/logs/queue.log 2>&1 &
ok "Queue worker restarted (PID $!)"

# ── 11. Cron reminder ───────────────────────────────────────
warn "─────────────────────────────────────────────────────────"
warn "  CRON SETUP REQUIRED (one-time)"
warn "  In cPanel → Cron Jobs, add:"
warn ""
warn "  * * * * * ${PHP_BIN} ${APP_DIR}/artisan schedule:run >> /dev/null 2>&1"
warn ""
warn "  This runs the Laravel scheduler every minute."
warn "─────────────────────────────────────────────────────────"

# ── 12. SSL reminder ────────────────────────────────────────
warn "─────────────────────────────────────────────────────────"
warn "  SSL is handled by cPanel AutoSSL — ensure it's enabled"
warn "  in cPanel → SSL/TLS → Manage AutoSSL."
warn "─────────────────────────────────────────────────────────"

# ── 13. Take app out of maintenance mode ─────────────────────
info "Bringing application back up …"
$PHP_BIN artisan up
ok "Application is live"

# ── Summary ──────────────────────────────────────────────────
echo ""
echo -e "${GREEN}====== Deployment complete ======${NC}"
echo -e "  Branch:    ${BRANCH}"
echo -e "  Commit:    $(git log --oneline -1)"
echo -e "  PHP:       $($PHP_BIN -v | head -1)"
echo -e "  Time:      $(date '+%Y-%m-%d %H:%M:%S')"
echo -e "${GREEN}=================================${NC}"
