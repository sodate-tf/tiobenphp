import { useRef } from 'react';
import { Platform, StyleSheet, Text, View } from 'react-native';
import { BannerAd, BannerAdSize, useForeground } from 'react-native-google-mobile-ads';
import type { BannerPlacement } from '../config/ads';
import { getBannerUnitId } from '../config/ads';
import { colors, radius } from '../theme/colors';

type Props = {
  placement: BannerPlacement;
};

export function AdBanner({ placement }: Props) {
  const bannerRef = useRef<BannerAd>(null);
  const unitId = getBannerUnitId(placement);

  useForeground(() => {
    if (Platform.OS === 'ios') {
      bannerRef.current?.load();
    }
  });

  if (!unitId) {
    return null;
  }

  return (
    <View style={styles.wrap}>
      <Text style={styles.label}>Patrocinado</Text>
      <View style={styles.card}>
        <BannerAd
          ref={bannerRef}
          unitId={unitId}
          size={BannerAdSize.ANCHORED_ADAPTIVE_BANNER}
          requestOptions={{
            keywords: ['catolico', 'oracao', 'liturgia', 'familia']
          }}
          onAdFailedToLoad={(error) => {
            if (__DEV__) {
              console.warn(`Banner ${placement} nao carregou.`, error);
            }
          }}
        />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: {
    marginTop: 18,
    marginBottom: 18
  },
  label: {
    marginBottom: 8,
    color: colors.slate500,
    fontSize: 11,
    fontWeight: '800',
    textTransform: 'uppercase',
    textAlign: 'center'
  },
  card: {
    alignItems: 'center',
    borderWidth: 1,
    borderColor: colors.slate200,
    borderRadius: radius.lg,
    backgroundColor: colors.white,
    paddingVertical: 10,
    overflow: 'hidden'
  }
});