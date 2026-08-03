import { Image, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import type { PostCard as PostCardType } from '../types/app';
import { colors, radius } from '../theme/colors';

type Props = {
  post: PostCardType;
  onPress: (post: PostCardType) => void;
};

export function PostCard({ post, onPress }: Props) {
  return (
    <TouchableOpacity style={styles.card} activeOpacity={0.86} onPress={() => onPress(post)}>
      <View style={styles.cover}>
        {post.coverImageUrl ? <Image source={{ uri: post.coverImageUrl }} style={styles.image} /> : null}
      </View>
      <View style={styles.body}>
        <View style={styles.metaRow}>
          <Text style={styles.badge}>{post.category}</Text>
        </View>
        <Text style={styles.title} numberOfLines={2}>
          {post.title}
        </Text>
        <Text style={styles.description} numberOfLines={3}>
          {post.description || 'Leia a reflexão completa no Blog IA Tio Ben.'}
        </Text>
      </View>
    </TouchableOpacity>
  );
}

const styles = StyleSheet.create({
  card: {
    overflow: 'hidden',
    backgroundColor: colors.white,
    borderRadius: radius.xl,
    borderWidth: 1,
    borderColor: colors.slate200,
    marginBottom: 14
  },
  cover: {
    height: 156,
    backgroundColor: colors.slate100
  },
  image: {
    width: '100%',
    height: '100%'
  },
  body: {
    padding: 15
  },
  metaRow: {
    flexDirection: 'row',
    marginBottom: 8
  },
  badge: {
    backgroundColor: colors.amber50,
    color: colors.amber800,
    borderRadius: 999,
    paddingHorizontal: 10,
    paddingVertical: 4,
    fontSize: 11,
    fontWeight: '800'
  },
  title: {
    color: colors.slate900,
    fontSize: 17,
    lineHeight: 22,
    fontWeight: '900'
  },
  description: {
    color: colors.slate600,
    marginTop: 7,
    lineHeight: 20
  }
});
