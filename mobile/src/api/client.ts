import type { HomePayload, LiturgyPayload, PostCard, PostPayload, RosaryPayload } from '../types/app';

const apiBaseUrl =
  process.env.EXPO_PUBLIC_API_BASE_URL?.replace(/\/$/, '') ||
  'https://www.iatioben.com.br/api/app';

export const siteBaseUrl =
  process.env.EXPO_PUBLIC_SITE_URL?.replace(/\/$/, '') ||
  'https://www.iatioben.com.br';

async function requestJson<T>(path: string): Promise<T> {
  const response = await fetch(`${apiBaseUrl}${path}`, {
    headers: {
      Accept: 'application/json'
    }
  });

  const payload = await response.json().catch(() => null);

  if (!response.ok) {
    const message = payload?.message || `Falha na API (${response.status})`;
    throw new Error(message);
  }

  return payload as T;
}

export const api = {
  home: () => requestJson<HomePayload>('/home'),
  posts: (query = '') => requestJson<{ items: PostCard[] }>(`/posts${query ? `?q=${encodeURIComponent(query)}` : ''}`),
  post: (slug: string) => requestJson<PostPayload>(`/posts/${encodeURIComponent(slug)}`),
  liturgyToday: () => requestJson<LiturgyPayload>('/liturgy/today'),
  liturgy: (dateSlug: string) => requestJson<LiturgyPayload>(`/liturgy/${encodeURIComponent(dateSlug)}`),
  rosaryToday: () => requestJson<RosaryPayload>('/rosary/today'),
  rosary: (set: string) => requestJson<RosaryPayload>(`/rosary/${encodeURIComponent(set)}`)
};

export async function askTioBen(pergunta: string, history: Array<{ role: string; content: string }>) {
  const response = await fetch(`${siteBaseUrl}/api/perguntar`, {
    method: 'POST',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ pergunta, history, lang: 'pt' })
  });

  const payload = await response.json().catch(() => null);

  if (!response.ok) {
    throw new Error(payload?.error || payload?.message || `Falha na pergunta (${response.status})`);
  }

  return String(payload?.resposta || 'Não consegui responder agora. Tente novamente.');
}
