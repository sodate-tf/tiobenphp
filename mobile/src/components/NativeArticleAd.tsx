import { useEffect, useState } from 'react';
import { ActivityIndicator, Image, StyleSheet, Text, View } from 'react-native';
import {
  NativeAd,
  NativeAdChoicesPlacement,
  NativeAsset,
  NativeAssetType,
  NativeAdView,
  NativeMediaAspectRatio,
  NativeMediaView
} from 'react-native-google-mobile-ads';
import { getNativeUnitId } from '../config/ads';
import { colors, radius } from '../theme/colors';

export function NativeArticleAd() {
  const [nativeAd, setNativeAd] = useState<NativeAd | null>(null);
  const [loading, setLoading] = useState(true);
  const unitId = getNativeUnitId('singlePost');

  useEffect(() => {
    let active = true;
    let currentAd: NativeAd | null = null;

    async function load() {
      if (!unitId) {
        setLoading(false);
        return;
      }

      setLoading(true);

      try {
        const ad = await NativeAd.createForAdRequest(unitId, {
          aspectRatio: NativeMediaAspectRatio.LANDSCAPE,
          adChoicesPlacement: NativeAdChoicesPlacement.TOP_RIGHT,
          startVideoMuted: true,
          keywords: ['catolico', 'oracao', 'liturgia', 'familia']
        });

        if (!active) {
          ad.destroy();
          return;
        }

        currentAd = ad;
        setNativeAd(ad);
      } catch (error) {
        if (__DEV__) {
          console.warn('Native single post nao carregou.', error);
        }
      } finally {
        if (active) {
          setLoading(false);
        }
      }
    }

    void load();

    return () => {
      active = false;
      currentAd?.destroy();
    };
  }, [unitId]);

  if (!unitId) {
    return null;
  }

  if (loading && !nativeAd) {
    return (
      <View style={styles.loadingCard}>
        <ActivityIndicator color={colors.amber800} />
      </View>
    );
  }

  if (!nativeAd) {
    return null;
  }

  return (
    <View style={styles.wrap}>
      <Text style={styles.label}>Patrocinado</Text>
      <NativeAdView nativeAd={nativeAd} style={styles.card}>
        <View style={styles.header}>
          <NativeAsset assetType={NativeAssetType.ICON}>
            <Image source={{ uri: nativeAd.icon?.url ?? undefined }} style={styles.icon} resizeMode="cover" />
          </NativeAsset>
          <View style={{ flex: 1 }}>
            <NativeAsset assetType={NativeAssetType.HEADLINE}>
              <Text style={styles.title}>{nativeAd.headline}</Text>
            </NativeAsset>
            <NativeAsset assetType={NativeAssetType.ADVERTISER}>
              <Text style={styles.meta}>{nativeAd.advertiser ?? 'Conteudo patrocinado'}</Text>
            </NativeAsset>
          </View>
        </View>

        {nativeAd.mediaContent ? <NativeMediaView style={styles.media} resizeMode="cover" /> : null}

        <NativeAsset assetType={NativeAssetType.BODY}>
          <Text style={styles.body}>{nativeAd.body}</Text>
        </NativeAsset>

        <NativeAsset assetType={NativeAssetType.CALL_TO_ACTION}>
          <View style={styles.ctaButton}>
            <Text style={styles.ctaText}>{nativeAd.callToAction}</Text>
          </View>
        </NativeAsset>
      </NativeAdView>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: {
    marginTop: 20,
    marginBottom: 22
  },
  label: {
    marginBottom: 8,
    color: colors.slate500,
    fontSize: 11,
    fontWeight: '800',
    textTransform: 'uppercase'
  },
  loadingCard: {
    marginTop: 20,
    marginBottom: 22,
    borderWidth: 1,
    borderColor: colors.slate200,
    borderRadius: radius.lg,
    backgroundColor: colors.white,
    paddingVertical: 24,
    alignItems: 'center',
    justifyContent: 'center'
  },
  card: {
    borderWidth: 1,
    borderColor: colors.slate200,
    borderRadius: radius.lg,
    backgroundColor: colors.white,
    padding: 14,
    gap: 12
  },
  header: {
    flexDirection: 'row',
    gap: 12,
    alignItems: 'center'
  },
  icon: {
    width: 52,
    height: 52,
    borderRadius: 14,
    backgroundColor: colors.slate100
  },
  title: {
    color: colors.slate900,
    fontSize: 16,
    fontWeight: '900'
  },
  meta: {
    marginTop: 4,
    color: colors.slate600,
    fontSize: 13
  },
  media: {
    width: '100%',
    height: 180,
    borderRadius: radius.md,
    overflow: 'hidden'
  },
  body: {
    color: colors.slate700,
    lineHeight: 21
  },
  ctaButton: {
    alignSelf: 'flex-start',
    borderRadius: radius.md,
    backgroundColor: colors.amber800,
    paddingHorizontal: 14,
    paddingVertical: 10
  },
  ctaText: {
    color: colors.white,
    fontWeight: '900'
  }
});
