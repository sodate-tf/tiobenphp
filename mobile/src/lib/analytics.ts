import type { AppScreen } from '../types/app';

type EventParams = Record<string, string | number | undefined>;

type AnalyticsLike = {
  logEvent(name: string, params?: EventParams): Promise<void>;
  logScreenView(params: { screen_name: string; screen_class: string }): Promise<void>;
  setAnalyticsCollectionEnabled(enabled: boolean): Promise<void>;
};

const analyticsEnabled = process.env.EXPO_PUBLIC_ENABLE_ANALYTICS === 'true';
let analyticsPromise: Promise<AnalyticsLike | null> | null = null;

async function getAnalytics(): Promise<AnalyticsLike | null> {
  if (!analyticsEnabled) {
    return null;
  }

  if (!analyticsPromise) {
    analyticsPromise = (async () => {
      try {
        const analyticsModule = (await import('@react-native-firebase/analytics')).default;
        const instance = analyticsModule();
        await instance.setAnalyticsCollectionEnabled(true);
        return instance;
      } catch (error) {
        console.warn('Firebase Analytics nao foi inicializado.', error);
        return null;
      }
    })();
  }

  return analyticsPromise;
}

export async function initializeAnalytics(): Promise<boolean> {
  const analytics = await getAnalytics();
  if (!analytics) {
    return false;
  }

  await analytics.logEvent('app_boot');
  return true;
}

export async function logAnalyticsEvent(name: string, params?: EventParams): Promise<void> {
  const analytics = await getAnalytics();
  if (!analytics) {
    return;
  }

  try {
    await analytics.logEvent(name, params);
  } catch (error) {
    console.warn(`Falha ao registrar evento ${name}.`, error);
  }
}

export async function logScreenView(screen: AppScreen): Promise<void> {
  const analytics = await getAnalytics();
  if (!analytics) {
    return;
  }

  try {
    await analytics.logScreenView({
      screen_name: screen,
      screen_class: screen
    });
  } catch (error) {
    console.warn(`Falha ao registrar screen view ${screen}.`, error);
  }
}