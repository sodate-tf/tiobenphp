# Firebase Analytics

## O que ja esta pronto

- Dependencias `@react-native-firebase/app` e `@react-native-firebase/analytics`
- Plugin do Firebase no `app.json`
- `google-services` configurado no Gradle raiz
- Aplicacao condicional do plugin `com.google.gms.google-services` no app Android
- Falha explicita na release se analytics estiver ligado e `google-services.json` faltar
- Helper central em `src/lib/analytics.ts`
- Eventos basicos de navegacao, carregamento da home, notificacoes, blog e chat

## O que voce precisa fornecer

1. Criar o app Android `br.com.iatioben.app` no Firebase.
2. Baixar `google-services.json`.
3. Salvar em `mobile/google-services.json`.

## Ativacao na proxima versao

1. Garanta `EXPO_PUBLIC_ENABLE_ANALYTICS=true`.
2. Gere uma nova build Android.
3. Valide os eventos no DebugView do Firebase Analytics.

## Observacoes

- O arquivo `google-services.json` nao deve ir para o git.
- Sem o arquivo e sem a flag, a camada de analytics fica em no-op.
- Com a flag ligada e sem o arquivo, a build de release falha de forma intencional.