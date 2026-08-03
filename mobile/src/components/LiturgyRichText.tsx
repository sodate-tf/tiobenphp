import { useMemo } from 'react';
import { StyleSheet, Text, View } from 'react-native';
import { colors } from '../theme/colors';

type Token =
  | { type: 'text'; value: string }
  | { type: 'verse'; value: string }
  | { type: 'break' };

type Props = {
  html: string | null | undefined;
  fallbackText: string;
  fontSize?: number;
  lineHeight?: number;
};

export function LiturgyRichText({ html, fallbackText, fontSize = 18, lineHeight = 34 }: Props) {
  const paragraphs = useMemo(() => parseLiturgyHtml(html, fallbackText), [html, fallbackText]);

  return (
    <View style={styles.wrapper}>
      {paragraphs.map((paragraph, paragraphIndex) => (
        <Text key={paragraphIndex} style={[styles.paragraph, { fontSize, lineHeight }]}>
          {paragraph.map((token, tokenIndex) => {
            if (token.type === 'verse') {
              return (
                <Text key={tokenIndex} style={styles.verseBadge}>
                  {token.value}
                </Text>
              );
            }

            if (token.type === 'break') {
              return <Text key={tokenIndex}>{'\n'}</Text>;
            }

            return <Text key={tokenIndex}>{token.value}</Text>;
          })}
        </Text>
      ))}
    </View>
  );
}

function parseLiturgyHtml(html: string | null | undefined, fallbackText: string): Token[][] {
  const source = html?.trim() ? html : escapeToHtml(fallbackText);
  const paragraphMatches = [...source.matchAll(/<p>([\s\S]*?)<\/p>/gi)];

  if (!paragraphMatches.length) {
    return [tokenizeInline(source.replace(/<br\s*\/?>/gi, '\n'))];
  }

  return paragraphMatches.map((match) => tokenizeInline(match[1] ?? '')).filter((tokens) => tokens.length > 0);
}

function tokenizeInline(input: string): Token[] {
  const prepared = input.replace(/<br\s*\/?>/gi, '__LIT_BREAK__');
  const tokens: Token[] = [];
  const verseRegex = /<sup[^>]*>(.*?)<\/sup>/gi;
  let lastIndex = 0;
  let match: RegExpExecArray | null;

  while ((match = verseRegex.exec(prepared))) {
    const before = prepared.slice(lastIndex, match.index);
    pushText(tokens, before);
    tokens.push({ type: 'verse', value: decodeHtmlEntities(match[1] ?? '').trim() });
    lastIndex = match.index + match[0].length;
  }

  pushText(tokens, prepared.slice(lastIndex));

  return tokens;
}

function pushText(tokens: Token[], raw: string) {
  const decoded = decodeHtmlEntities(raw);
  const parts = decoded.split('__LIT_BREAK__');

  parts.forEach((part, index) => {
    if (part) {
      tokens.push({ type: 'text', value: part });
    }

    if (index < parts.length - 1) {
      tokens.push({ type: 'break' });
    }
  });
}

function decodeHtmlEntities(text: string): string {
  return text
    .replace(/&nbsp;/gi, ' ')
    .replace(/&amp;/gi, '&')
    .replace(/&quot;/gi, '"')
    .replace(/&#039;/gi, "'")
    .replace(/&lt;/gi, '<')
    .replace(/&gt;/gi, '>');
}

function escapeToHtml(text: string): string {
  return `<p>${text.replace(/\n/g, '<br/>')}</p>`;
}

const styles = StyleSheet.create({
  wrapper: {
    marginTop: 18,
    gap: 18
  },
  paragraph: {
    color: colors.slate900
  },
  verseBadge: {
    color: colors.amber700,
    fontSize: 13,
    lineHeight: 18,
    borderWidth: 1,
    borderColor: colors.amber200,
    backgroundColor: colors.amber50,
    borderRadius: 6,
    paddingHorizontal: 4,
    marginHorizontal: 2,
    overflow: 'hidden'
  }
});
