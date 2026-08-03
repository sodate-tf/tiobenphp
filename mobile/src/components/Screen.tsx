import type { ReactNode } from 'react';
import { Platform, ScrollView, StatusBar, StyleSheet, View } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';
import { colors } from '../theme/colors';
import { BrandHeader } from './BrandHeader';

type Props = {
  children: ReactNode;
  scroll?: boolean;
};

export function Screen({ children, scroll = true }: Props) {
  const insets = useSafeAreaInsets();
  const bottomSpacing = 96 + Math.max(insets.bottom, 10);

  if (!scroll) {
    return (
      <SafeAreaView style={styles.safe}>
        <BrandHeader />
        <View style={[styles.body, styles.staticContent, { paddingBottom: bottomSpacing }]}>{children}</View>
      </SafeAreaView>
    );
  }

  return (
    <SafeAreaView style={styles.safe}>
      <BrandHeader />
      <ScrollView style={styles.body} contentContainerStyle={[styles.content, { paddingBottom: bottomSpacing + 12 }]} showsVerticalScrollIndicator={false}>
        {children}
      </ScrollView>
    </SafeAreaView>
  );
}

const styles = StyleSheet.create({
  safe: {
    flex: 1,
    paddingTop: Platform.OS === 'android' ? StatusBar.currentHeight ?? 0 : 0,
    backgroundColor: colors.white
  },
  body: {
    flex: 1,
    backgroundColor: colors.slate50
  },
  content: {
    padding: 18
  },
  staticContent: {
    padding: 18
  }
});
