# Relationship Platform Health

Repositorio base da Relationship Platform Health, uma plataforma e framework para aplicativos de saude e bem-estar.

O projeto nasce com o FitCheck como aplicativo principal, mas a estrutura esta preparada para evoluir para outros produtos como PhysioCheck, MassageCheck, PilatesCheck, YogaCheck, NutriCheck e TherapyCheck.

## Estrutura

```text
relationship-platform-health/
├── app/          # Aplicativo Flutter principal, inicialmente FitCheck
├── platform/     # Backend, API e painel administrativo da plataforma
├── packages/     # Pacotes compartilhados futuros do framework
├── docs/         # Documentacao tecnica e decisoes do projeto
├── tools/        # Scripts utilitarios e automacoes
└── README.md
```

## Componentes

### app

Aplicativo Flutter offline-first. Inicialmente representa o FitCheck, com foco em profissionais de educacao fisica/personal trainer, e deve servir como base para novos aplicativos da area de saude e bem-estar.

### platform

Backend/API/painel administrativo da Relationship Platform Health. Centraliza recursos administrativos, cadastro de profissionais, aplicativos, versoes, avisos internos, flags de funcionalidades e integracoes futuras.

### packages

Espaco reservado para pacotes compartilhados do framework, como `health_core`, `health_ui`, `health_storage`, `health_notifications`, `health_api`, `health_maps` e `health_widgets`.

Nenhum codigo foi movido para pacotes compartilhados nesta etapa para evitar risco em regras de negocio existentes.

### docs

Documentacao tecnica, arquitetura, roadmap, API, banco de dados e decisoes do projeto.

### tools

Espaco para scripts utilitarios futuros, como backups, geracao de APK, geracao de icones, scripts SQL e utilitarios de build.

## Desenvolvimento

Aplicativo Flutter:

```bash
cd app
flutter pub get
flutter run
```

Backend/API:

```bash
cd platform
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Objetivo

Manter uma base escalavel, profissional e reutilizavel para produtos digitais de saude e bem-estar, preservando a evolucao do FitCheck e abrindo caminho para novos aplicativos da Relationship Platform Health.
