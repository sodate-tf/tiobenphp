[CmdletBinding()]
param(
    [ValidateSet('Bootstrap', 'Deploy')]
    [string]$Mode = 'Deploy',

    [string]$KeyPath = "$env:USERPROFILE\Downloads\iatioben_github_actions (1)",
    [string]$SshUser = 'a4d58b41',
    [string]$SshHost = 'sh00092.hostgator.com.br',
    [int]$SshPort = 2222,
    [string]$AppPath = '/home2/a4d58b41/public_html/laravel_app'
)

$ErrorActionPreference = 'Stop'

function Invoke-External {
    param(
        [string]$FilePath,
        [string[]]$Arguments,
        [string]$FailureMessage
    )

    & $FilePath @Arguments
    if ($LASTEXITCODE -ne 0) {
        throw "$FailureMessage (codigo $LASTEXITCODE)."
    }
}

if (-not (Test-Path -LiteralPath $KeyPath -PathType Leaf)) {
    throw "Chave privada nao encontrada em: $KeyPath"
}

Invoke-External git @('fetch', 'origin', 'main') 'Nao foi possivel atualizar as referencias do GitHub'
$localCommit = (& git rev-parse HEAD).Trim()
$remoteCommit = (& git rev-parse origin/main).Trim()
if ($localCommit -ne $remoteCommit) {
    throw "O commit local ($localCommit) nao e o mesmo que origin/main ($remoteCommit). Faca git push antes do deploy."
}

if ($Mode -eq 'Deploy') {
    Invoke-External npm @('run', 'build') 'Falha ao gerar os arquivos de producao do Vite'
}

$sshBase = @(
    '-p', $SshPort,
    '-o', 'IdentitiesOnly=yes',
    '-o', 'StrictHostKeyChecking=accept-new',
    '-i', $KeyPath,
    "$SshUser@$SshHost"
)

if ($AppPath.Contains("'")) { throw "AppPath nao pode conter aspas simples." }
$remoteApp = "'$AppPath'"

if ($Mode -eq 'Bootstrap') {
    $bootstrapCommand = @"
set -e
APP_PATH=$remoteApp
test -d "`$APP_PATH"
BACKUP_DIR="`$HOME/iatioben-backups/`$(date +%Y%m%d-%H%M%S)"
mkdir -p "`$BACKUP_DIR"
tar -C "`$APP_PATH" -czf "`$BACKUP_DIR/laravel_app-before-git.tar.gz" .
if [ -d "`$HOME/public_html/build" ]; then
  tar -C "`$HOME/public_html" -czf "`$BACKUP_DIR/public-build-before-git.tar.gz" build
fi
cd "`$APP_PATH"
if [ -d .git ]; then
  echo 'O Laravel ja possui .git. Use o modo Deploy.'
  exit 3
fi
git init
git remote add origin https://github.com/sodate-tf/tiobenphp.git
git fetch --depth=1 origin main
git reset --hard origin/main
COMPOSER_BIN="`$(command -v composer || true)"
if [ -z "`$COMPOSER_BIN" ] && [ -x /opt/cpanel/composer/bin/composer ]; then COMPOSER_BIN=/opt/cpanel/composer/bin/composer; fi
[ -n "`$COMPOSER_BIN" ] || { echo 'Composer nao encontrado. Verifique /opt/cpanel/composer/bin/composer no Jailed Shell.'; exit 127; }
"`$COMPOSER_BIN" install --no-dev --prefer-dist --no-interaction --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan view:cache
echo "Bootstrap concluido. Backup: `$(basename "`$BACKUP_DIR")"
"@

    Invoke-External ssh ($sshBase + @($bootstrapCommand)) 'Bootstrap na HostGator falhou'
    Write-Host 'Bootstrap concluido. Execute agora: .\scripts\deploy-hostgator.ps1 -Mode Deploy' -ForegroundColor Green
    exit 0
}

$prepareCommand = @"
set -e
APP_PATH=$remoteApp
test -d "`$APP_PATH/.git" || { echo 'Laravel ainda nao foi inicializado no Git. Execute -Mode Bootstrap.'; exit 4; }
cd "`$APP_PATH"
git fetch --prune origin main
git checkout main
git reset --hard origin/main
COMPOSER_BIN="`$(command -v composer || true)"
if [ -z "`$COMPOSER_BIN" ] && [ -x /opt/cpanel/composer/bin/composer ]; then COMPOSER_BIN=/opt/cpanel/composer/bin/composer; fi
[ -n "`$COMPOSER_BIN" ] || { echo 'Composer nao encontrado. Verifique /opt/cpanel/composer/bin/composer no Jailed Shell.'; exit 127; }
"`$COMPOSER_BIN" install --no-dev --prefer-dist --no-interaction --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
"@
Invoke-External ssh ($sshBase + @($prepareCommand)) 'Atualizacao do Laravel na HostGator falhou'

Invoke-External scp @('-P', $SshPort, '-o', 'IdentitiesOnly=yes', '-o', 'StrictHostKeyChecking=accept-new', '-i', $KeyPath, '-r', 'public/build', "$SshUser@$SshHost`:$AppPath/public/") 'Upload dos arquivos Vite falhou'

$finalizeCommand = @"
set -e
APP_PATH=$remoteApp
PUBLIC_ROOT="`$HOME/public_html"
mkdir -p "`$PUBLIC_ROOT/build"
rsync -a --delete "`$APP_PATH/public/build/" "`$PUBLIC_ROOT/build/"
if [ -f "`$APP_PATH/public/app-ads.txt" ]; then cp "`$APP_PATH/public/app-ads.txt" "`$PUBLIC_ROOT/app-ads.txt"; fi
if [ -d "`$APP_PATH/public/images/liturgia" ]; then mkdir -p "`$PUBLIC_ROOT/images/liturgia"; rsync -a "`$APP_PATH/public/images/liturgia/" "`$PUBLIC_ROOT/images/liturgia/"; fi
cd "`$APP_PATH"
if php artisan list --raw | grep -qx 'responsecache:clear'; then php artisan responsecache:clear; fi
php artisan view:cache
echo 'Deploy concluido.'
"@
Invoke-External ssh ($sshBase + @($finalizeCommand)) 'Finalizacao do deploy na HostGator falhou'

Write-Host "Deploy concluido para o commit $localCommit" -ForegroundColor Green