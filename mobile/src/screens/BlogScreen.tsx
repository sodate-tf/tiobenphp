import { Fragment, useEffect, useState } from 'react';
import { Image, Linking, Share, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { api } from '../api/client';
import type { PostCard as PostCardType, PostPayload } from '../types/app';
import { AdBanner } from '../components/AdBanner';
import { NativeArticleAd } from '../components/NativeArticleAd';
import { colors, radius } from '../theme/colors';
import { LoadingState, ErrorState } from '../components/StateViews';
import { PostCard } from '../components/PostCard';

type Props = {
  selectedPost: PostCardType | null;
  showAds: boolean;
  onOpenPost: (post: PostCardType) => void;
  onClosePost: () => void;
};

type ArticleBlock = {
  type: 'heading' | 'paragraph' | 'quote' | 'bullet';
  text: string;
};

export function BlogScreen({ selectedPost, showAds, onOpenPost, onClosePost }: Props) {
  const [items, setItems] = useState<PostCardType[]>([]);
  const [query, setQuery] = useState('');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  async function load(search = query) {
    try {
      setLoading(true);
      setError(null);
      const payload = await api.posts(search.trim());
      setItems(payload.items);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Nao foi possivel carregar os posts.');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load('');
  }, []);

  if (selectedPost) {
    return <PostDetail post={selectedPost} showAds={showAds} onBack={onClosePost} />;
  }

  return (
    <View>
      <Text style={styles.kicker}>IA Tio Ben</Text>
      <Text style={styles.title}>Blog do Tio Ben</Text>
      <Text style={styles.lead}>Reflexoes catolicas, liturgia diaria, santos, oracao e vida crista.</Text>

      <View style={styles.searchBox}>
        <TextInput
          style={styles.searchInput}
          value={query}
          onChangeText={setQuery}
          placeholder="Buscar no blog..."
          placeholderTextColor={colors.slate500}
          returnKeyType="search"
          onSubmitEditing={() => load(query)}
        />
        <TouchableOpacity style={styles.searchButton} onPress={() => load(query)}>
          <Ionicons name="search" size={18} color={colors.white} />
        </TouchableOpacity>
      </View>

      {loading && items.length === 0 ? <LoadingState label="Carregando posts..." /> : null}
      {error && items.length === 0 ? <ErrorState message={error} onRetry={() => load(query)} /> : null}

      {items.map((post, index) => {
        const shouldRenderBanner =
          showAds &&
          items.length > 0 &&
          (index === 2 || (items.length < 3 && index === items.length - 1));

        return (
          <Fragment key={post.id}>
            <PostCard post={post} onPress={onOpenPost} />
            {shouldRenderBanner ? <AdBanner placement="blogFeed" /> : null}
          </Fragment>
        );
      })}
    </View>
  );
}

function PostDetail({ post, showAds, onBack }: { post: PostCardType; showAds: boolean; onBack: () => void }) {
  const [payload, setPayload] = useState<PostPayload | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  async function load() {
    try {
      setLoading(true);
      setError(null);
      setPayload(await api.post(post.slug));
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Nao foi possivel carregar o post.');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
  }, [post.slug]);

  async function sharePost() {
    await Share.share({
      title: post.title,
      message: `${post.title}\n${post.webUrl}`
    });
  }

  const content = payload?.post.content ?? '';
  const articleBlocks = buildArticleBlocks(content);
  const nativeInsertIndex = articleBlocks.length >= 3 ? 2 : Math.max(articleBlocks.length - 1, 0);
  const publishedAt = formatPostDate(post.publishedAt);
  const readTime = estimateReadTime(content || post.description);
  const metaItems = [publishedAt, `${readTime} min de leitura`].filter(Boolean);

  return (
    <View>
      <TouchableOpacity style={styles.backButton} onPress={onBack}>
        <Ionicons name="arrow-back" size={18} color={colors.slate900} />
        <Text style={styles.backText}>Voltar ao blog</Text>
      </TouchableOpacity>

      <View style={styles.articleHero}>
        {post.coverImageUrl ? (
          <View style={styles.heroImageWrap}>
            <Image source={{ uri: post.coverImageUrl }} style={styles.detailImage} />
          </View>
        ) : null}

        <View style={styles.articleHeader}>
          <View style={styles.headerTopLine}>
            <Text style={styles.badge}>{post.category}</Text>
            {metaItems.length ? <Text style={styles.metaText}>{metaItems.join(' | ')}</Text> : null}
          </View>

          <Text style={styles.detailTitle}>{post.title}</Text>
          <Text style={styles.detailDesc}>{post.description}</Text>

          <View style={styles.detailActions}>
            <TouchableOpacity style={styles.primaryButton} onPress={sharePost}>
              <Ionicons name="share-social" size={16} color={colors.white} />
              <Text style={styles.primaryButtonText}>Compartilhar</Text>
            </TouchableOpacity>
            <TouchableOpacity style={styles.outlineButton} onPress={() => Linking.openURL(post.webUrl)}>
              <Ionicons name="open-outline" size={16} color={colors.amber800} />
              <Text style={styles.outlineButtonText}>Abrir no site</Text>
            </TouchableOpacity>
          </View>
        </View>
      </View>

      {loading ? <LoadingState label="Abrindo artigo..." /> : null}
      {error ? <ErrorState message={error} onRetry={load} /> : null}
      {payload ? (
        <View style={styles.articleSurface}>
          {articleBlocks.map((block, index) => (
            <Fragment key={`${block.type}-${index}`}>
              <ArticleBlockView block={block} />
              {showAds && index === nativeInsertIndex ? <NativeArticleAd /> : null}
            </Fragment>
          ))}

          <View style={styles.endCard}>
            <Text style={styles.endTitle}>Gostou da reflexao?</Text>
            <Text style={styles.endText}>Continue lendo no site para compartilhar, salvar ou abrir os links internos do artigo.</Text>
            <TouchableOpacity style={styles.endButton} onPress={() => Linking.openURL(post.webUrl)}>
              <Ionicons name="open-outline" size={17} color={colors.white} />
              <Text style={styles.endButtonText}>Continuar no site</Text>
            </TouchableOpacity>
          </View>
        </View>
      ) : null}
    </View>
  );
}

function ArticleBlockView({ block }: { block: ArticleBlock }) {
  if (block.type === 'heading') {
    return <Text style={styles.articleHeading}>{block.text}</Text>;
  }

  if (block.type === 'quote') {
    return <Text style={styles.articleQuote}>{block.text}</Text>;
  }

  if (block.type === 'bullet') {
    return (
      <View style={styles.bulletRow}>
        <View style={styles.bulletDot} />
        <Text style={styles.bulletText}>{block.text}</Text>
      </View>
    );
  }

  return <Text style={styles.content}>{block.text}</Text>;
}

function buildArticleBlocks(content: string): ArticleBlock[] {
  const normalized = content.replace(/\r\n/g, '\n').replace(/\r/g, '\n').trim();

  if (!normalized) {
    return [{ type: 'paragraph', text: 'Conteudo indisponivel no momento.' }];
  }

  const blocks: ArticleBlock[] = [];

  normalized
    .split(/\n{2,}/)
    .map((chunk) => chunk.trim())
    .filter(Boolean)
    .forEach((chunk) => {
      const lines = chunk
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean);

      if (lines.length > 1 && lines.every((line) => /^[-*]\s+/.test(line))) {
        lines.forEach((line) => blocks.push({ type: 'bullet', text: line.replace(/^[-*]\s+/, '') }));
        return;
      }

      if (/^#{1,3}\s+/.test(chunk)) {
        blocks.push({ type: 'heading', text: chunk.replace(/^#{1,3}\s+/, '') });
        return;
      }

      if (/^>\s+/.test(chunk)) {
        blocks.push({ type: 'quote', text: chunk.replace(/^>\s+/, '') });
        return;
      }

      blocks.push({ type: 'paragraph', text: lines.join('\n') });
    });

  return blocks;
}

function estimateReadTime(content: string): number {
  const words = content.trim().split(/\s+/).filter(Boolean).length;
  return Math.max(1, Math.ceil(words / 190));
}

function formatPostDate(value: string | null): string | null {
  if (!value) {
    return null;
  }

  const date = new Date(value);
  if (Number.isNaN(date.getTime())) {
    return null;
  }

  return new Intl.DateTimeFormat('pt-BR', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  }).format(date);
}

const styles = StyleSheet.create({
  kicker: {
    color: colors.amber700,
    fontSize: 11,
    fontWeight: '900',
    textTransform: 'uppercase'
  },
  title: {
    marginTop: 6,
    color: colors.slate900,
    fontSize: 28,
    fontWeight: '900'
  },
  lead: {
    marginTop: 9,
    color: colors.slate600,
    lineHeight: 22
  },
  searchBox: {
    marginTop: 16,
    marginBottom: 16,
    flexDirection: 'row',
    overflow: 'hidden',
    borderRadius: radius.lg,
    borderWidth: 1,
    borderColor: colors.slate200,
    backgroundColor: colors.white
  },
  searchInput: {
    flex: 1,
    paddingHorizontal: 14,
    paddingVertical: 12,
    color: colors.slate900,
    fontSize: 15
  },
  searchButton: {
    width: 50,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.slate900
  },
  backButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 8,
    alignSelf: 'flex-start',
    marginBottom: 12,
    borderWidth: 1,
    borderColor: colors.slate200,
    borderRadius: 999,
    backgroundColor: colors.white,
    paddingHorizontal: 12,
    paddingVertical: 9
  },
  backText: {
    color: colors.slate900,
    fontWeight: '900'
  },
  articleHero: {
    overflow: 'hidden',
    borderWidth: 1,
    borderColor: colors.slate200,
    backgroundColor: colors.white,
    borderRadius: radius.xl,
    marginBottom: 16
  },
  heroImageWrap: {
    backgroundColor: colors.slate100
  },
  detailImage: {
    width: '100%',
    height: 220
  },
  articleHeader: {
    padding: 18
  },
  headerTopLine: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    alignItems: 'center',
    gap: 8
  },
  badge: {
    alignSelf: 'flex-start',
    backgroundColor: colors.amber50,
    color: colors.amber800,
    borderRadius: 999,
    paddingHorizontal: 10,
    paddingVertical: 4,
    fontSize: 11,
    fontWeight: '900'
  },
  metaText: {
    color: colors.slate500,
    fontSize: 12,
    fontWeight: '700'
  },
  detailTitle: {
    marginTop: 12,
    color: colors.slate900,
    fontSize: 27,
    lineHeight: 33,
    fontWeight: '900'
  },
  detailDesc: {
    marginTop: 8,
    color: colors.slate600,
    lineHeight: 22
  },
  detailActions: {
    marginTop: 16,
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8
  },
  primaryButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    borderRadius: radius.md,
    paddingHorizontal: 13,
    paddingVertical: 10,
    backgroundColor: colors.slate900
  },
  primaryButtonText: {
    color: colors.white,
    fontWeight: '900'
  },
  outlineButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
    borderWidth: 1,
    borderColor: colors.amber200,
    borderRadius: radius.md,
    paddingHorizontal: 12,
    paddingVertical: 9,
    backgroundColor: colors.amber50
  },
  outlineButtonText: {
    color: colors.amber800,
    fontWeight: '900'
  },
  articleSurface: {
    borderWidth: 1,
    borderColor: colors.slate200,
    backgroundColor: colors.white,
    borderRadius: radius.xl,
    paddingHorizontal: 18,
    paddingTop: 20,
    paddingBottom: 18
  },
  articleHeading: {
    marginTop: 12,
    marginBottom: 8,
    color: colors.slate900,
    fontSize: 22,
    lineHeight: 28,
    fontWeight: '900'
  },
  articleQuote: {
    marginTop: 8,
    marginBottom: 14,
    borderLeftWidth: 3,
    borderLeftColor: colors.amber400,
    paddingLeft: 14,
    color: colors.slate700,
    fontSize: 18,
    lineHeight: 30,
    fontStyle: 'italic'
  },
  bulletRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: 10,
    marginBottom: 12
  },
  bulletDot: {
    width: 7,
    height: 7,
    borderRadius: 99,
    marginTop: 11,
    backgroundColor: colors.amber700
  },
  bulletText: {
    flex: 1,
    color: colors.slate700,
    fontSize: 18,
    lineHeight: 30
  },
  content: {
    color: colors.slate900,
    fontSize: 18,
    lineHeight: 31,
    marginBottom: 16
  },
  endCard: {
    marginTop: 8,
    borderRadius: radius.lg,
    backgroundColor: colors.slate900,
    padding: 16
  },
  endTitle: {
    color: colors.white,
    fontSize: 18,
    fontWeight: '900'
  },
  endText: {
    marginTop: 6,
    color: colors.slate200,
    lineHeight: 21
  },
  endButton: {
    marginTop: 12,
    alignSelf: 'flex-start',
    flexDirection: 'row',
    alignItems: 'center',
    gap: 7,
    borderRadius: radius.md,
    backgroundColor: colors.amber800,
    paddingHorizontal: 13,
    paddingVertical: 10
  },
  endButtonText: {
    color: colors.white,
    fontWeight: '900'
  }
});