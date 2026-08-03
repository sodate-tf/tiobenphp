import mobileAds, { AdEventType, AdsConsent, InterstitialAd, MaxAdContentRating } from 'react-native-google-mobile-ads';
import { getInterstitialUnitId, shouldBootAdsSdk, type InterstitialPlacement } from '../config/ads';

let initializationPromise: Promise<boolean> | null = null;

type InterstitialState = {
  ad: InterstitialAd;
  isLoaded: boolean;
  isLoading: boolean;
  waiters: Array<(loaded: boolean) => void>;
};

const interstitialStates: Partial<Record<InterstitialPlacement, InterstitialState>> = {};

function getOrCreateInterstitialState(placement: InterstitialPlacement): InterstitialState | null {
  const existing = interstitialStates[placement];
  if (existing) {
    return existing;
  }

  const unitId = getInterstitialUnitId(placement);
  if (!unitId) {
    return null;
  }

  const ad = InterstitialAd.createForAdRequest(unitId, {
    keywords: ['catolico', 'oracao', 'liturgia', 'familia']
  });

  const state: InterstitialState = {
    ad,
    isLoaded: false,
    isLoading: false,
    waiters: []
  };

  ad.addAdEventListener(AdEventType.LOADED, () => {
    state.isLoaded = true;
    state.isLoading = false;
    state.waiters.splice(0).forEach((resolve) => resolve(true));
  });

  ad.addAdEventListener(AdEventType.ERROR, () => {
    state.isLoaded = false;
    state.isLoading = false;
    state.waiters.splice(0).forEach((resolve) => resolve(false));
  });

  ad.addAdEventListener(AdEventType.CLOSED, () => {
    state.isLoaded = false;
    state.isLoading = false;
    ad.load();
    state.isLoading = true;
  });

  interstitialStates[placement] = state;
  return state;
}

export async function preloadInterstitialAd(placement: InterstitialPlacement): Promise<boolean> {
  const state = getOrCreateInterstitialState(placement);
  if (!state) {
    return false;
  }

  if (state.isLoaded) {
    return true;
  }

  return new Promise((resolve) => {
    state.waiters.push(resolve);

    if (!state.isLoading) {
      state.isLoading = true;
      state.ad.load();
    }
  });
}

export async function showInterstitialAdIfReady(placement: InterstitialPlacement): Promise<boolean> {
  const state = getOrCreateInterstitialState(placement);
  if (!state?.isLoaded) {
    void preloadInterstitialAd(placement);
    return false;
  }

  try {
    await state.ad.show();
    return true;
  } catch (error) {
    if (__DEV__) {
      console.warn(`Interstitial ${placement} nao abriu.`, error);
    }
    void preloadInterstitialAd(placement);
    return false;
  }
}

export function initializeMobileAds(): Promise<boolean> {
  if (!shouldBootAdsSdk()) {
    return Promise.resolve(false);
  }

  if (initializationPromise) {
    return initializationPromise;
  }

  initializationPromise = (async () => {
    await mobileAds().setRequestConfiguration({
      maxAdContentRating: MaxAdContentRating.PG,
      testDeviceIdentifiers: __DEV__ ? ['EMULATOR'] : []
    });

    let canRequestAds = true;

    try {
      await AdsConsent.gatherConsent();
      const consentInfo = await AdsConsent.getConsentInfo();
      canRequestAds = consentInfo.canRequestAds;
    } catch (error) {
      if (__DEV__) {
        console.warn('Falha ao coletar consentimento do AdMob. O app vai usar o ultimo status conhecido.', error);
      }
    }

    if (!canRequestAds) {
      return false;
    }

    await mobileAds().initialize();
    void preloadInterstitialAd('chatQuestions');
    return true;
  })();

  return initializationPromise;
}