# AdMob no app mobile

Este projeto foi preparado para uma monetizacao leve e sem poluir as telas principais de devocao.

## O que ja ficou implementado

- SDK do AdMob integrado via `react-native-google-mobile-ads`.
- Inicializacao unica do SDK no boot do app.
- Consentimento inicial com `AdsConsent`.
- Banner leve em quatro areas de navegacao:
  - Home, entre atalhos e posts.
  - Lista do blog, depois dos primeiros posts.
  - Liturgia, entre os controles iniciais e o bloco principal de leitura.
  - Terco, entre a escolha dos misterios e o card principal da dezena.
- Single post usa um native ad discreto dentro do artigo, depois dos primeiros blocos de leitura.
- Chat usa intersticial controlado, apenas antes da resposta da IA a cada 3 perguntas.
- Build de release do Android agora falha se `ADMOB_ANDROID_APP_ID` nao estiver definido.
- `app-ads.txt` real publicado em `public/app-ads.txt` com o publisher da conta atual.
- App ID do AdMob confirmado: `ca-app-pub-8819996017476509~4824622274`.
- Unit IDs reais configurados no projeto: Home `8922850622`, Blog `4178589169`, Liturgia `4934257314`, Terco `7609768959`, Intersticial Chat `4431694431`, Single `2357442272`.

## Variaveis de ambiente

Copie `mobile/.env.example` para o seu ambiente local e configure:

- `ADMOB_ANDROID_APP_ID`
- `EXPO_PUBLIC_ENABLE_ADS`
- `EXPO_PUBLIC_ADMOB_FORCE_TEST_IDS`
- `EXPO_PUBLIC_ADMOB_ANDROID_BANNER_HOME_UNIT_ID`
- `EXPO_PUBLIC_ADMOB_ANDROID_BANNER_BLOG_UNIT_ID`
- `EXPO_PUBLIC_ADMOB_ANDROID_BANNER_LITURGY_UNIT_ID`
- `EXPO_PUBLIC_ADMOB_ANDROID_BANNER_ROSARY_UNIT_ID`
- `EXPO_PUBLIC_ADMOB_ANDROID_INTERSTITIAL_CHAT_UNIT_ID`
- `EXPO_PUBLIC_ADMOB_ANDROID_NATIVE_SINGLE_POST_UNIT_ID`

## Passo a passo recomendado

1. O app Android no AdMob ja foi criado.
2. O app ja pode operar com estas unidades:
   - Banner Home
   - Banner Blog
   - Banner Liturgia
   - Banner Terco
   - Native Single Post
   - Intersticial Chat
3. Os `Unit IDs` reais dessas areas ja podem ser usados em producao.
4. Gere a build de producao com:
   - `EXPO_PUBLIC_ENABLE_ADS=true`
   - `EXPO_PUBLIC_ADMOB_FORCE_TEST_IDS=false`
5. Garanta que o site do desenvolvedor no Google Play aponta para o mesmo dominio onde o arquivo esta publicado.
6. Confirme que o arquivo abre em `https://www.iatioben.com.br/app-ads.txt`.
7. No painel do AdMob, clique em verificar atualizacoes do `app-ads.txt`.

## Estrategia de monetizacao sugerida

Fase 1:

- Manter banners apenas uma vez por tela, sempre fora do momento central da leitura ou oracao.
- Nao usar interstitial em abertura do app.
- No chat, usar intersticial apenas no ritmo de 1 exibicao a cada 3 perguntas, sempre antes da resposta.
- Manter detalhe do artigo sem anuncio.

Fase 2:

- Se quiser aumentar receita sem sujar, adicione anuncio recompensado em alguma acao opcional, nunca obrigatoria.
- Evite interstitial em fluxo espiritual ou leitura longa.

## app-ads.txt

Conteudo atual publicado:

`google.com, pub-8819996017476509, DIRECT, f08c47fec0942fa0`

Esse arquivo pertence ao dominio do site, nao ao APK.