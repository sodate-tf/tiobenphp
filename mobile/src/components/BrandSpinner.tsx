import { useEffect, useRef } from 'react';
import { Animated, Easing, Image, StyleSheet, View } from 'react-native';
import { colors } from '../theme/colors';

const tioBenIcon = require('../../assets/tio-ben-icon.png');

type Props = {
  size?: number;
};

export function BrandSpinner({ size = 72 }: Props) {
  const spinValue = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    const animation = Animated.loop(
      Animated.timing(spinValue, {
        toValue: 1,
        duration: 1250,
        easing: Easing.linear,
        useNativeDriver: true
      })
    );

    animation.start();

    return () => {
      animation.stop();
      spinValue.setValue(0);
    };
  }, [spinValue]);

  const rotate = spinValue.interpolate({
    inputRange: [0, 1],
    outputRange: ['0deg', '360deg']
  });

  return (
    <View style={[styles.shell, { width: size, height: size, borderRadius: size / 2 }]}>
      <Animated.View
        style={[
          styles.ring,
          {
            width: size,
            height: size,
            borderRadius: size / 2,
            transform: [{ rotate }]
          }
        ]}
      />
      <View
        style={[
          styles.center,
          {
            width: size * 0.68,
            height: size * 0.68,
            borderRadius: size * 0.22
          }
        ]}
      >
        <Image source={tioBenIcon} style={styles.logo} resizeMode="contain" />
      </View>
    </View>
  );
}

const styles = StyleSheet.create({
  shell: {
    alignItems: 'center',
    justifyContent: 'center'
  },
  ring: {
    position: 'absolute',
    borderWidth: 4,
    borderColor: colors.amber100,
    borderTopColor: colors.amber800,
    borderRightColor: colors.amber400
  },
  center: {
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.white,
    borderWidth: 1,
    borderColor: colors.amber200,
    shadowColor: colors.amber900,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 10,
    elevation: 2
  },
  logo: {
    width: '88%',
    height: '88%'
  }
});
