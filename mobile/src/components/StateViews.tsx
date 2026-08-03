import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { BrandSpinner } from './BrandSpinner';
import { colors, radius } from '../theme/colors';

export function LoadingState({ label = 'Carregando...' }: { label?: string }) {
  return (
    <View style={styles.state}>
      <BrandSpinner size={68} />
      <Text style={styles.stateText}>{label}</Text>
    </View>
  );
}

export function ErrorState({ message, onRetry }: { message: string; onRetry?: () => void }) {
  return (
    <View style={styles.state}>
      <Text style={styles.errorTitle}>Algo nao carregou</Text>
      <Text style={styles.stateText}>{message}</Text>
      {onRetry ? (
        <TouchableOpacity style={styles.retryButton} onPress={onRetry}>
          <Text style={styles.retryText}>Tentar novamente</Text>
        </TouchableOpacity>
      ) : null}
    </View>
  );
}

const styles = StyleSheet.create({
  state: {
    borderWidth: 1,
    borderColor: colors.slate200,
    backgroundColor: colors.white,
    borderRadius: radius.lg,
    padding: 22,
    alignItems: 'center',
    justifyContent: 'center',
    gap: 12,
    minHeight: 180
  },
  stateText: {
    color: colors.slate600,
    textAlign: 'center',
    lineHeight: 20
  },
  errorTitle: {
    color: colors.slate900,
    fontSize: 17,
    fontWeight: '800'
  },
  retryButton: {
    marginTop: 6,
    backgroundColor: colors.amber800,
    borderRadius: radius.md,
    paddingHorizontal: 16,
    paddingVertical: 10
  },
  retryText: {
    color: colors.white,
    fontWeight: '800'
  }
});
