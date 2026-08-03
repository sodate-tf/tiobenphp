export type AppScreen = 'home' | 'liturgy' | 'blog' | 'rosary' | 'chat';

export type QuickAction = {
  key: string;
  label: string;
  screen: AppScreen;
  path: string;
};

export type PostCard = {
  id: string;
  title: string;
  slug: string;
  description: string;
  category: string;
  coverImageUrl: string | null;
  publishedAt: string | null;
  webUrl: string;
};

export type HomePayload = {
  brand: {
    name: string;
    subtitle: string;
    logoUrl: string;
    heroImageUrl: string;
  };
  hero: {
    title: string;
    description: string;
  };
  quickActions: QuickAction[];
  hubs: Array<{
    title: string;
    description: string;
    path: string;
  }>;
  today: {
    dateSlug: string;
    dateLabel: string;
    rosarySet: RosaryPayload;
  };
  latestPosts: PostCard[];
};

export type LiturgyTab = {
  id: string;
  label: string;
  kind: 'reading' | 'psalm' | 'gospel' | 'extra' | 'reflection';
  reference: string | null;
  title: string | null;
  text: string;
  html: string | null;
  refrain: string | null;
  sourceLabel?: string | null;
  sourceUrl?: string | null;
};

export type LiturgyReflection = {
  title: string;
  label: string;
  content: string;
  sourceLabel: string;
  sourceUrl: string;
};

export type LiturgyPayload = {
  dateSlug: string;
  dateISO: string;
  dateLabel: string;
  dateHuman: string;
  weekday: string;
  celebration: string;
  color: string;
  summary: string;
  reflection: LiturgyReflection | null;
  reflectionStatus: 'available' | 'today_only' | 'unavailable';
  reflectionDebug?: {
    status: 'ok' | 'http_error' | 'not_found' | 'today_only';
    available: boolean;
    sourceUrl: string;
    httpStatus: number | null;
    fetchedAt: string | null;
    contentPreview: string | null;
    note: string | null;
  };
  navigation: {
    previous: string;
    today: string;
    next: string;
  };
  tabs: LiturgyTab[];
};

export type RosaryPayload = {
  key: string;
  label: string;
  days: string;
  theme: string;
  items: Array<{
    title: string;
    reference: string;
  }>;
};

export type PostPayload = {
  post: PostCard & {
    content: string;
    keywords: string;
  };
};
