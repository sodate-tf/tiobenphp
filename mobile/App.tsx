import { useEffect, useState } from 'react';
import { StatusBar } from 'expo-status-bar';
import { View } from 'react-native';
import * as Notifications from 'expo-notifications';
import { SafeAreaProvider } from 'react-native-safe-area-context';
import { api } from './src/api/client';
import { BottomNav } from './src/components/BottomNav';
import { Screen } from './src/components/Screen';
import { initializeAnalytics, logAnalyticsEvent, logScreenView } from './src/lib/analytics';
import { initializeLiturgyReminderChannelAsync } from './src/lib/liturgyReminders';
import { initializeMobileAds } from './src/lib/mobileAds';
import { BlogScreen } from './src/screens/BlogScreen';
import { ChatScreen } from './src/screens/ChatScreen';
import { HomeScreen } from './src/screens/HomeScreen';
import { LiturgyScreen } from './src/screens/LiturgyScreen';
import { RosaryScreen } from './src/screens/RosaryScreen';
import type { AppScreen, HomePayload, PostCard } from './src/types/app';

export default function App() {
  const [screen, setScreen] = useState<AppScreen>('home');
  const [home, setHome] = useState<HomePayload | null>(null);
  const [homeLoading, setHomeLoading] = useState(true);
  const [homeError, setHomeError] = useState<string | null>(null);
  const [selectedPost, setSelectedPost] = useState<PostCard | null>(null);
  const [adsReady, setAdsReady] = useState(false);

  async function loadHome() {
    try {
      setHomeLoading(true);
      setHomeError(null);
      const payload = await api.home();
      setHome(payload);
      void logAnalyticsEvent('home_loaded', {
        latest_posts_count: payload.latestPosts.length,
        hubs_count: payload.hubs.length
      });
    } catch (err) {
      const message = err instanceof Error ? err.message : 'Nao foi possivel carregar o inicio.';
      setHomeError(message);
      void logAnalyticsEvent('home_load_failed', {
        message_length: message.length
      });
    } finally {
      setHomeLoading(false);
    }
  }

  useEffect(() => {
    loadHome();
  }, []);

  useEffect(() => {
    void initializeAnalytics();
  }, []);

  useEffect(() => {
    void logScreenView(screen);
  }, [screen]);

  useEffect(() => {
    let active = true;

    initializeMobileAds()
      .then((ready) => {
        if (active) {
          setAdsReady(ready);
        }
      })
      .catch(() => {
        if (active) {
          setAdsReady(false);
        }
      });

    return () => {
      active = false;
    };
  }, []);

  useEffect(() => {
    let active = true;

    initializeLiturgyReminderChannelAsync();

    Notifications.getLastNotificationResponseAsync().then((response) => {
      if (!active) {
        return;
      }

      const route = response?.notification.request.content.data?.route;
      if (route === 'liturgy') {
        setSelectedPost(null);
        setScreen('liturgy');
        void logAnalyticsEvent('notification_opened', { route: 'liturgy' });
      }
    });

    const subscription = Notifications.addNotificationResponseReceivedListener((response) => {
      const route = response.notification.request.content.data?.route;
      if (route === 'liturgy') {
        setSelectedPost(null);
        setScreen('liturgy');
        void logAnalyticsEvent('notification_opened', { route: 'liturgy' });
      }
    });

    return () => {
      active = false;
      subscription.remove();
    };
  }, []);

  function openPost(post: PostCard) {
    setSelectedPost(post);
    setScreen('blog');
    void logAnalyticsEvent('blog_post_opened', {
      category: post.category,
      title_length: post.title.length
    });
  }

  function changeScreen(next: AppScreen) {
    if (next !== 'blog') {
      setSelectedPost(null);
    }
    setScreen(next);
  }

  return (
    <SafeAreaProvider>
      <View style={{ flex: 1 }}>
        <StatusBar style="dark" backgroundColor="#ffffff" translucent={false} />
        <Screen scroll={screen !== 'chat'}>
          {screen === 'home' ? (
            <HomeScreen
              data={home}
              loading={homeLoading}
              error={homeError}
              showAds={adsReady}
              onRetry={loadHome}
              onNavigate={changeScreen}
              onOpenPost={openPost}
            />
          ) : null}

          {screen === 'liturgy' ? <LiturgyScreen showAds={adsReady} /> : null}

          {screen === 'blog' ? (
            <BlogScreen
              selectedPost={selectedPost}
              showAds={adsReady}
              onOpenPost={openPost}
              onClosePost={() => setSelectedPost(null)}
            />
          ) : null}

          {screen === 'rosary' ? <RosaryScreen showAds={adsReady} /> : null}

          {screen === 'chat' ? <ChatScreen /> : null}
        </Screen>
        <BottomNav current={screen} onChange={changeScreen} />
      </View>
    </SafeAreaProvider>
  );
}