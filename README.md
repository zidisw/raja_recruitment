# Raja Recruitment

Laravel/Livewire recruitment system for PT. Roda Jaya Sakti.

## Dokumentasi

- [Developer Guide](DEVELOPER_GUIDE.md)
- [Deployment Runbook](docs/DEPLOYMENT_RUNBOOK.md)
- [GitHub Actions to Hostinger Semi-Auto Deployment Guide](docs/GITHUB_ACTIONS_HOSTINGER_DEPLOYMENT.md)

## Quick Checks

Jalankan sebelum push atau deploy:

```bash
composer audit --no-interaction
php artisan test
npm audit --audit-level=high
npm run build
```

Format check:

```bash
vendor/bin/pint --test
```

Di Windows:

```powershell
vendor\bin\pint.bat --test
```

## Production Deploy

Deploy production dilakukan dari GitHub Actions:

```text
Actions -> Deploy Production (Hostinger SSH) -> Run workflow
```

Lihat detail lengkap di [Deployment Runbook](docs/DEPLOYMENT_RUNBOOK.md).
