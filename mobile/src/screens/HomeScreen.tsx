import { Image, Linking, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import type { AppScreen, HomePayload, PostCard as PostCardType } from '../types/app';
import { AdBanner } from '../components/AdBanner';
import { colors, radius } from '../theme/colors';
import { LoadingState, ErrorState } from '../components/StateViews';
import { PostCard } from '../components/PostCard';
import { siteBaseUrl } from '../api/client';

type Props = {
  data: HomePayload | null;
  loading: boolean;
  error: string | null;
  showAds: boolean;
  onRetry: () => void;
  onNavigate: (screen: AppScreen) => void;
  onOpenPost: (post: PostCardType) => void;
};

export function HomeScreen({ data, loading, error, showAds, onRetry, onNavigate, onOpenPost }: Props) {
  if (loading && !data) {
    return <LoadingState label="Carregando IA Tio Ben..." />;
  }

  if (error && !data) {
    return <ErrorState message={error} onRetry={onRetry} />;
  }

  if (!data) {
    return null;
  }

  return (
    <View>
      <View style={styles.hero}>
        <View style={styles.heroImageWrap}>
          <Image source={{ uri: data.brand.heroImageUrl }} style={styles.heroImage} resizeMode="contain" />
        </View>
        <Text style={styles.brand}>{data.brand.name}</Text>
        <Text style={styles.title}>{data.hero.title}</Text>
        <Text style={styles.description}>{data.hero.description}</Text>

        <View style={styles.actions}>
          {data.quickActions.map((action) => (
            <TouchableOpacity
              key={action.key}
              style={[styles.actionButton, action.key === 'liturgy' && styles.primaryAction]}
              onPress={() => onNavigate(action.screen)}
              activeOpacity={0.84}
            >
              <Text style={[styles.actionText, action.key === 'liturgy' && styles.primaryActionText]}>{action.label}</Text>
            </TouchableOpacity>
          ))}
        </View>
      </View>

      <View style={styles.todayCard}>
        <View style={styles.rowBetween}>
          <View style={{ flex: 1 }}>
            <Text style={styles.kicker}>Hoje</Text>
            <Text style={styles.sectionTitle}>{data.today.dateLabel}</Text>
          </View>
          <View style={styles.todayIcon}>
            <Ionicons name="calendar" size={20} color={colors.amber900} />
          </View>
        </View>

        <Text style={styles.todayText}>Terco de hoje: {data.today.rosarySet.label} ({data.today.rosarySet.days}).</Text>

        <TouchableOpacity style={styles.todayButton} onPress={() => onNavigate('liturgy')}>
          <Ionicons name="book-outline" size={16} color={colors.white} />
          <Text style={styles.todayButtonText}>Abrir liturgia de hoje</Text>
        </TouchableOpacity>
      </View>

      <Text style={styles.sectionTitle}>Acessos rapidos</Text>
      <View style={styles.hubGrid}>
        {data.hubs.map((hub) => (
          <TouchableOpacity key={hub.path} style={styles.hubCard} onPress={() => Linking.openURL(`${siteBaseUrl}${hub.path}`)}>
            <Text style={styles.hubTitle}>{hub.title}</Text>
            <Text style={styles.hubDesc}>{hub.description}</Text>
          </TouchableOpacity>
        ))}
      </View>

      {showAds ? <AdBanner placement="homeFeed" /> : null}

      <View style={styles.sectionHeader}>
        <Text style={styles.sectionTitle}>Ultimos posts</Text>
        <TouchableOpacity onPress={() => onNavigate('blog')}>
          <Text style={styles.linkText}>Ver todos</Text>
        </TouchableOpacity>
      </View>
      {data.latestPosts.map((post) => (
        <PostCard key={post.id} post={post} onPress={onOpenPost} />
      ))}
    </View>
  );
}

const styles = StyleSheet.create({
  hero: {
    marginHorizontal: -18,
    marginTop: -18,
    paddingHorizontal: 18,
    paddingTop: 16,
    paddingBottom: 24,
    backgroundColor: colors.amber200
  },
  heroImageWrap: {
    alignSelf: 'center',
    width: 168,
    height: 168
  },
  heroImage: {
    width: '100%',
    height: '100%'
  },
  brand: {
    textAlign: 'center',
    color: colors.amber900,
    fontSize: 13,
    fontWeight: '900',
    textTransform: 'uppercase'
  },
  title: {
    marginTop: 8,
    color: colors.amber900,
    textAlign: 'center',
    fontSize: 27,
    lineHeight: 33,
    fontWeight: '900'
  },
  description: {
    marginTop: 12,
    color: '#3f2f1b',
    textAlign: 'center',
    lineHeight: 22,
    fontSize: 15
  },
  actions: {
    marginTop: 18,
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'center',
    gap: 8
  },
  actionButton: {
    borderRadius: radius.md,
    borderWidth: 1,
    borderColor: 'rgba(120,53,15,0.18)',
    backgroundColor: 'rgba(255,255,255,0.78)',
    paddingHorizontal: 13,
    paddingVertical: 10
  },
  primaryAction: {
    backgroundColor: colors.amber800,
    borderColor: colors.amber800
  },
  actionText: {
    color: colors.amber900,
    fontWeight: '900',
    fontSize: 13
  },
  primaryActionText: {
    color: colors.white
  },
  todayCard: {
    marginTop: 18,
    marginBottom: 18,
    backgroundColor: colors.white,
    borderRadius: radius.xl,
    borderWidth: 1,
    borderColor: colors.slate200,
    padding: 16
  },
  rowBetween: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 14
  },
  kicker: {
    color: colors.amber700,
    fontSize: 11,
    fontWeight: '900',
    textTransform: 'uppercase'
  },
  sectionTitle: {
    color: colors.slate900,
    fontSize: 20,
    fontWeight: '900'
  },
  todayIcon: {
    width: 42,
    height: 42,
    borderRadius: 14,
    backgroundColor: colors.amber100,
    alignItems: 'center',
    justifyContent: 'center'
  },
  todayText: {
    marginTop: 10,
    color: colors.slate600,
    lineHeight: 21
  },
  todayButton: {
    marginTop: 14,
    alignSelf: 'flex-start',
    borderRadius: radius.md,
    backgroundColor: colors.amber800,
    paddingHorizontal: 14,
    paddingVertical: 10,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 7
  },
  todayButtonText: {
    color: colors.white,
    fontWeight: '900'
  },
  hubGrid: {
    marginTop: 12,
    marginBottom: 18,
    gap: 10
  },
  hubCard: {
    borderWidth: 1,
    borderColor: colors.slate200,
    backgroundColor: colors.white,
    borderRadius: radius.lg,
    padding: 14
  },
  hubTitle: {
    color: colors.slate900,
    fontWeight: '900',
    fontSize: 15
  },
  hubDesc: {
    color: colors.slate600,
    marginTop: 5,
    lineHeight: 19
  },
  sectionHeader: {
    marginTop: 6,
    marginBottom: 12,
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center'
  },
  linkText: {
    color: colors.indigo700,
    fontWeight: '900'
  }
});