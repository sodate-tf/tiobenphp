import { Image, StyleSheet, Text, View } from 'react-native';
import { colors } from '../theme/colors';

const tioBenIcon = require('../../assets/tio-ben-icon.png');

export function BrandHeader() {
  return (
    <View style={styles.header}>
      <View style={styles.logoWrap}>
        <Image source={tioBenIcon} style={styles.logo} resizeMode="contain" />
      </View>
      <View>
        <Text style={styles.name}>IA TIO BEN</Text>
        <Text style={styles.tagline}>Fe catolica para todos os dias</Text>
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  header: {
    minHeight: 64,
    paddingHorizontal: 18,
    paddingVertical: 10,
    flexDirection: 'row',
    alignItems: 'center',
    gap: 11,
    backgroundColor: colors.white,
    borderBottomWidth: 1,
    borderBottomColor: colors.amber100
  },
  logoWrap: {
    width: 44,
    height: 44,
    borderRadius: 15,
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
    backgroundColor: colors.amber50,
    borderWidth: 1,
    borderColor: colors.amber200
  },
  logo: {
    width: 42,
    height: 42
  },
  name: {
    color: colors.amber900,
    fontSize: 15,
    lineHeight: 18,
    fontWeight: '900',
    letterSpacing: 0.6
  },
  tagline: {
    marginTop: 2,
    color: colors.slate600,
    fontSize: 11.5,
    fontWeight: '600'
  }
});
