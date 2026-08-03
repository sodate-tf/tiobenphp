import { Ionicons } from '@expo/vector-icons';
import { Image, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';
import type { AppScreen } from '../types/app';
import { colors } from '../theme/colors';

const tioBenIcon = require('../../assets/tio-ben-icon.png');

const items: Array<{ screen: AppScreen; label: string; icon: keyof typeof Ionicons.glyphMap }> = [
  { screen: 'home', label: 'Inicio', icon: 'home' },
  { screen: 'liturgy', label: 'Liturgia', icon: 'book' },
  { screen: 'blog', label: 'Blog', icon: 'newspaper' },
  { screen: 'rosary', label: 'Terco', icon: 'radio-button-on' },
  { screen: 'chat', label: 'IA', icon: 'chatbubble-ellipses' }
];

type Props = {
  current: AppScreen;
  onChange: (screen: AppScreen) => void;
};

export function BottomNav({ current, onChange }: Props) {
  const insets = useSafeAreaInsets();
  const bottomInset = Math.max(insets.bottom, 10);

  return (
    <View style={[styles.wrap, { paddingBottom: bottomInset + 8 }]}>
      {items.map((item) => {
        const active = current === item.screen;
        const isAi = item.screen === 'chat';

        return (
          <TouchableOpacity
            key={item.screen}
            style={[styles.item, isAi && styles.aiItem, active && styles.activeItem, active && isAi && styles.activeAiItem]}
            activeOpacity={0.8}
            onPress={() => onChange(item.screen)}
          >
            <View style={[styles.iconWrap, isAi && styles.aiIconWrap, active && styles.activeIconWrap, active && isAi && styles.activeAiIconWrap]}>
              {isAi ? (
                <Image source={tioBenIcon} style={styles.aiIcon} resizeMode="contain" />
              ) : (
                <Ionicons name={item.icon} size={18} color={active ? colors.white : colors.amber900} />
              )}
            </View>
            <Text numberOfLines={1} style={[styles.label, active && styles.activeLabel]}>
              {isAi ? 'Tio Ben IA' : item.label}
            </Text>
          </TouchableOpacity>
        );
      })}
    </View>
  );
}

const styles = StyleSheet.create({
  wrap: {
    position: 'absolute',
    left: 0,
    right: 0,
    bottom: 0,
    paddingHorizontal: 8,
    paddingTop: 8,
    backgroundColor: 'rgba(255,255,255,0.98)',
    borderTopWidth: 1,
    borderTopColor: colors.slate200,
    flexDirection: 'row',
    justifyContent: 'space-around'
  },
  item: {
    flex: 1,
    minHeight: 58,
    alignItems: 'center',
    justifyContent: 'center',
    gap: 4,
    borderRadius: 12
  },
  activeItem: {
    backgroundColor: colors.amber50
  },
  aiItem: {
    marginTop: -16
  },
  activeAiItem: {
    backgroundColor: colors.amber100
  },
  iconWrap: {
    width: 32,
    height: 32,
    borderRadius: 12,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.amber100
  },
  activeIconWrap: {
    backgroundColor: colors.amber800
  },
  aiIconWrap: {
    width: 46,
    height: 46,
    borderRadius: 16,
    overflow: 'hidden',
    backgroundColor: colors.white,
    borderWidth: 2,
    borderColor: colors.amber400,
    shadowColor: colors.amber900,
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.2,
    shadowRadius: 5,
    elevation: 5
  },
  activeAiIconWrap: {
    backgroundColor: colors.white,
    borderColor: colors.amber800
  },
  aiIcon: {
    width: 44,
    height: 44
  },
  label: {
    color: colors.amber900,
    fontSize: 10.5,
    fontWeight: '800'
  },
  activeLabel: {
    color: colors.slate900
  }
});
