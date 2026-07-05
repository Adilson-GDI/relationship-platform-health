# Architecture

Este repositorio esta organizado como uma base de plataforma e framework para produtos de saude e bem-estar.

## Visao geral

- `app/`: aplicativo Flutter offline-first, inicialmente FitCheck.
- `platform/`: backend Laravel, API e painel administrativo.
- `packages/`: pacotes compartilhados futuros do framework.
- `docs/`: documentacao tecnica e decisoes.
- `tools/`: scripts e automacoes.

## Principios

- Offline-first no aplicativo.
- Plataforma administrativa centralizada.
- Separacao gradual de codigo reutilizavel em pacotes.
- Mudancas estruturais sem reescrever regras de negocio existentes.

## Evolucao planejada

Os pacotes compartilhados poderao ser criados quando houver necessidade real de reutilizacao entre aplicativos, evitando extrair abstracoes cedo demais.
