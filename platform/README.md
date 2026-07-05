# Relationship Platform Health

Plataforma administrativa Laravel para gerenciar aplicativos de profissionais da saude, bem-estar e atividades fisicas.

Ela centraliza cadastro de profissionais, dispositivos, versoes, avisos internos, flags de funcionalidades e notificacoes futuras. Os aplicativos continuam Offline First e usam esta plataforma para recursos administrativos e sincronizacoes.

## Aplicativos iniciais

- `fitcheck`
- `physiocheck`
- `massagecheck`
- `pilatescheck`
- `yogacheck`
- `nutricheck`
- `therapycheck`

## Setup

```bash
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Por padrao o projeto usa SQLite em `database/database.sqlite`.

## Endpoints

### Cadastro do profissional

`POST /api/v1/professionals/register`

```json
{
  "name": "Ana Silva",
  "email": "ana@example.com",
  "phone": "11999999999",
  "city": "Sao Paulo",
  "state": "SP",
  "profession": "Personal Trainer",
  "app_code": "fitcheck",
  "version": "1.0.0",
  "platform": "android",
  "device_id": "device-123",
  "push_token": "token-opcional"
}
```

### Bootstrap do aplicativo

`GET /api/v1/apps/{app_code}/bootstrap?platform=android`

Retorna identidade do aplicativo, versoes ativas, avisos internos ativos e flags de funcionalidades.

## Testes

```bash
php artisan test
```
