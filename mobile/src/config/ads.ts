import { TestIds } from 'react-native-google-mobile-ads';

export type BannerPlacement = 'homeFeed' | 'blogFeed' | 'liturgyFeed' | 'rosaryFeed';
export type InterstitialPlacement = 'chatQuestions';
export type NativePlacement = 'singlePost';

function isTrue(value: string | undefined): boolean {
  return value === 'true';
}

const productionBannerUnits: Record<BannerPlacement, string | undefined> = {
  homeFeed: process.env.EXPO_PUBLIC_ADMOB_ANDROID_BANNER_HOME_UNIT_ID,
  blogFeed: process.env.EXPO_PUBLIC_ADMOB_ANDROID_BANNER_BLOG_UNIT_ID,
  liturgyFeed: process.env.EXPO_PUBLIC_ADMOB_ANDROID_BANNER_LITURGY_UNIT_ID,
  rosaryFeed: process.env.EXPO_PUBLIC_ADMOB_ANDROID_BANNER_ROSARY_UNIT_ID
};

const productionInterstitialUnits: Record<InterstitialPlacement, string | undefined> = {
  chatQuestions: process.env.EXPO_PUBLIC_ADMOB_ANDROID_INTERSTITIAL_CHAT_UNIT_ID
};

const productionNativeUnits: Record<NativePlacement, string | undefined> = {
  singlePost: process.env.EXPO_PUBLIC_ADMOB_ANDROID_NATIVE_SINGLE_POST_UNIT_ID
};

export const adsConfig = {
  enabled: __DEV__ || isTrue(process.env.EXPO_PUBLIC_ENABLE_ADS),
  useTestIds: __DEV__ || isTrue(process.env.EXPO_PUBLIC_ADMOB_FORCE_TEST_IDS)
};

export function getBannerUnitId(placement: BannerPlacement): string | null {
  if (!adsConfig.enabled) {
    return null;
  }

  if (adsConfig.useTestIds) {
    return TestIds.ADAPTIVE_BANNER;
  }

  const unitId = productionBannerUnits[placement]?.trim();
  return unitId ? unitId : null;
}

export function getInterstitialUnitId(placement: InterstitialPlacement): string | null {
  if (!adsConfig.enabled) {
    return null;
  }

  if (adsConfig.useTestIds) {
    return TestIds.INTERSTITIAL;
  }

  const unitId = productionInterstitialUnits[placement]?.trim();
  return unitId ? unitId : null;
}

export function getNativeUnitId(placement: NativePlacement): string | null {
  if (!adsConfig.enabled) {
    return null;
  }

  if (adsConfig.useTestIds) {
    return TestIds.NATIVE;
  }

  const unitId = productionNativeUnits[placement]?.trim();
  return unitId ? unitId : null;
}

export function shouldBootAdsSdk(): boolean {
  return adsConfig.enabled && (
    adsConfig.useTestIds ||
    Object.values(productionBannerUnits).some(Boolean) ||
    Object.values(productionInterstitialUnits).some(Boolean) ||
    Object.values(productionNativeUnits).some(Boolean)
  );
}