# IA Tio Ben Mobile

App React Native/Expo separado do Laravel. O site continua sendo o CMS, SEO e backend; o app consome as APIs em `/api/app/*`.

## Rodar localmente

```bash
cd mobile
npm install
npm run start
```

Para testar contra outro backend:

```bash
EXPO_PUBLIC_API_BASE_URL=https://www.iatioben.com.br/api/app npm run start
EXPO_PUBLIC_SITE_URL=https://www.iatioben.com.br npm run start
```

## Android

```bash
cd mobile
npm run android
```

Se o Gradle mostrar `SDK location not found`, crie `android/local.properties` com:

```properties
sdk.dir=C:\\Users\\Windows\\AppData\\Local\\Android\\Sdk
```

Nesta maquina esse arquivo ja foi criado depois do primeiro `expo run:android`. Para rodar em modo debug, mantenha o Metro aberto; se o app abrir e voltar para o launcher, rode:

```bash
npm run start
```

Depois pressione `a` no terminal do Expo, ou abra novamente o app no emulador.

## Build de producao com AdMob + Firebase

Antes da proxima release, confirme todos estes itens:

1. `mobile/google-services.json` presente.
2. Variavel `ADMOB_ANDROID_APP_ID=ca-app-pub-8819996017476509~4824622274` configurada no ambiente da build.
3. Unit IDs reais de Home, Blog, Liturgia, Terco, intersticial do Chat e native ad do single post configurados.
4. `EXPO_PUBLIC_ENABLE_ADS=true`.
5. `EXPO_PUBLIC_ADMOB_FORCE_TEST_IDS=false`.
6. `EXPO_PUBLIC_ENABLE_ANALYTICS=true`.
7. `app-ads.txt` publicado no dominio correto do site do desenvolvedor.

A build de release agora falha se:

- `ADMOB_ANDROID_APP_ID` estiver ausente.
- `EXPO_PUBLIC_ENABLE_ANALYTICS=true` e `mobile/google-services.json` nao existir.

Para gerar build de loja:

```bash
npx eas build -p android --profile production
```

## Analytics

A estrutura do Firebase Analytics ja foi adicionada ao app.

Eventos preparados:

- `app_boot`
- `home_loaded`
- `home_load_failed`
- `notification_opened`
- `blog_post_opened`
- `chat_question_sent`
- `chat_answer_received`
- `chat_answer_failed`
- `chat_interstitial_attempted`
- `screen_view` das telas principais

## iOS futuro

O mesmo projeto serve para iOS. Quando chegar a hora:

```bash
npx eas build -p ios
```

## Build local assinado

Para gerar um `release` local com a sua chave:

1. Copie `android/key.properties.example` para `android/key.properties`.
2. Preencha `storeFile`, `storePassword`, `keyAlias` e `keyPassword`.
3. Coloque a keystore no caminho indicado, por exemplo `android/app/release.keystore`.
4. Confirme a SHA1 da keystore:

```bash
keytool -list -v -keystore android/app/release.keystore
```

5. Gere o build local:

```bash
cd mobile/android
./gradlew bundleRelease
```

Ou, no Windows:

```powershell
cd mobile\android
.\gradlew.bat bundleRelease
```

Saida esperada:

```text
android/app/build/outputs/bundle/release/app-release.aab
```

Se quiser APK:

```powershell
.\gradlew.bat assembleRelease
```

Se `android/key.properties` nao existir, o Gradle agora falha com uma mensagem explicita em vez de assinar com a chave de debug.