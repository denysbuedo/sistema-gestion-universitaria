#!/usr/bin/env bash

set -euo pipefail

php --version
composer --version
psql --version
fop -version

fop_smoke_dir="$(mktemp -d)"
trap 'rm -rf "${fop_smoke_dir}"' EXIT

cat > "${fop_smoke_dir}/report.fo" <<'EOF'
<?xml version="1.0" encoding="UTF-8"?>
<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
  <fo:layout-master-set>
    <fo:simple-page-master master-name="page" page-height="29.7cm" page-width="21cm" margin="2cm">
      <fo:region-body/>
    </fo:simple-page-master>
  </fo:layout-master-set>
  <fo:page-sequence master-reference="page">
    <fo:flow flow-name="xsl-region-body">
      <fo:block>SGU - validacion real de Apache FOP</fo:block>
    </fo:flow>
  </fo:page-sequence>
</fo:root>
EOF

fop -fo "${fop_smoke_dir}/report.fo" -pdf "${fop_smoke_dir}/report.pdf"
test -s "${fop_smoke_dir}/report.pdf"
head -c 5 "${fop_smoke_dir}/report.pdf" | grep -q '^%PDF-'

composer check-platform-reqs
php bin/console lexik:jwt:generate-keypair --env=test --skip-if-exists
php bin/console doctrine:database:create --env=test --if-not-exists
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:migrations:execute --env=test --down --no-interaction 'DoctrineMigrations\Version20261010004147'
php bin/console doctrine:migrations:migrate --env=test --no-interaction
php bin/console doctrine:fixtures:load --env=test --no-interaction

php vendor/bin/phpunit --no-coverage --do-not-cache-result
php bin/console lint:container --env=test
php bin/console lint:yaml config
php bin/console lint:twig templates
php bin/console doctrine:schema:validate --env=test
audit_report="$(mktemp)"
composer audit --locked --format=json > "${audit_report}" || true
cat "${audit_report}"
php -r '$audit = json_decode(file_get_contents($argv[1]), true, 512, JSON_THROW_ON_ERROR); exit(empty($audit["advisories"]) ? 0 : 1);' "${audit_report}"
rm -f "${audit_report}"

php-fpm8.3 -t
nginx -t

echo "Validación Ubuntu completada."
