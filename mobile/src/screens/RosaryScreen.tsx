import { useEffect, useMemo, useState } from 'react';
import { StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { api } from '../api/client';
import { AdBanner } from '../components/AdBanner';
import type { RosaryPayload } from '../types/app';
import { colors, radius } from '../theme/colors';
import { LoadingState, ErrorState } from '../components/StateViews';

const sets = [
  { key: 'gozosos', label: 'Gozosos' },
  { key: 'luminosos', label: 'Luminosos' },
  { key: 'dolorosos', label: 'Dolorosos' },
  { key: 'gloriosos', label: 'Gloriosos' }
];

const prayers = [
  'Sinal da Cruz, Credo e oferecimento',
  'Pai-Nosso',
  'Ave-Maria',
  'Glória ao Pai e oração de Fátima'
];

export function RosaryScreen({ showAds = false }: { showAds?: boolean }) {
  const [payload, setPayload] = useState<RosaryPayload | null>(null);
  const [selectedSet, setSelectedSet] = useState<string>('gozosos');
  const [currentMystery, setCurrentMystery] = useState(0);
  const [bead, setBead] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  async function load(set?: string) {
    try {
      setLoading(true);
      setError(null);
      const next = set ? await api.rosary(set) : await api.rosaryToday();
      setPayload(next);
      setSelectedSet(next.key);
      setCurrentMystery(0);
      setBead(0);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Não foi possível carregar o terço.');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
  }, []);

  const mystery = payload?.items[currentMystery] ?? null;
  const progress = useMemo(() => {
    const total = 5 * 10;
    const current = currentMystery * 10 + bead;
    return Math.round((current / total) * 100);
  }, [currentMystery, bead]);

  function next() {
    if (bead < 10) {
      setBead((value) => value + 1);
      return;
    }

    if (currentMystery < 4) {
      setCurrentMystery((value) => value + 1);
      setBead(0);
    }
  }

  function previous() {
    if (bead > 0) {
      setBead((value) => value - 1);
      return;
    }

    if (currentMystery > 0) {
      setCurrentMystery((value) => value - 1);
      setBead(10);
    }
  }

  if (loading && !payload) {
    return <LoadingState label="Preparando o Terço..." />;
  }

  if (error && !payload) {
    return <ErrorState message={error} onRetry={() => load()} />;
  }

  if (!payload || !mystery) {
    return null;
  }

  const prayer = bead === 0 ? prayers[1] : bead === 10 ? prayers[3] : prayers[2];

  return (
    <View>
      <View style={styles.hero}>
        <Text style={styles.kicker}>Santo Terço</Text>
        <Text style={styles.title}>Reze o Santo Terço passo a passo</Text>
        <Text style={styles.lead}>Mistérios do dia, progresso por dezenas e uma experiência pensada para o celular.</Text>
      </View>

      <View style={styles.setRow}>
        {sets.map((item) => {
          const active = item.key === selectedSet;
          return (
            <TouchableOpacity
              key={item.key}
              style={[styles.setButton, active && styles.activeSet]}
              onPress={() => load(item.key)}
            >
              <Text style={[styles.setText, active && styles.activeSetText]}>{item.label}</Text>
            </TouchableOpacity>
          );
        })}
      </View>

      {showAds ? <AdBanner placement="rosaryFeed" /> : null}

      <View style={styles.card}>
        <View style={styles.rowBetween}>
          <View>
            <Text style={styles.kicker}>{payload.label}</Text>
            <Text style={styles.days}>{payload.days}</Text>
          </View>
          <View style={styles.progressBadge}>
            <Text style={styles.progressText}>{progress}%</Text>
          </View>
        </View>

        <View style={styles.progressTrack}>
          <View style={[styles.progressFill, { width: `${progress}%` }]} />
        </View>

        <Text style={styles.mysteryIndex}>{currentMystery + 1}º mistério</Text>
        <Text style={styles.mysteryTitle}>{mystery.title}</Text>
        <Text style={styles.reference}>{mystery.reference}</Text>
        <Text style={styles.theme}>Tema: {payload.theme}</Text>

        <View style={styles.prayerCard}>
          <Ionicons name="radio-button-on" size={18} color={colors.amber800} />
          <View style={{ flex: 1 }}>
            <Text style={styles.prayerTitle}>{prayer}</Text>
            <Text style={styles.prayerHint}>
              {bead === 0 ? 'Inicie a dezena com calma.' : bead === 10 ? 'Finalize a dezena e contemple o mistério.' : `Ave-Maria ${bead}/10`}
            </Text>
          </View>
        </View>

        <View style={styles.controls}>
          <TouchableOpacity style={styles.controlButton} onPress={previous}>
            <Ionicons name="chevron-back" size={18} color={colors.slate900} />
            <Text style={styles.controlText}>Voltar</Text>
          </TouchableOpacity>
          <TouchableOpacity style={[styles.controlButton, styles.primaryControl]} onPress={next}>
            <Text style={styles.primaryControlText}>Avançar</Text>
            <Ionicons name="chevron-forward" size={18} color={colors.white} />
          </TouchableOpacity>
        </View>
      </View>

      <Text style={styles.sectionTitle}>Mistérios</Text>
      {payload.items.map((item, index) => (
        <TouchableOpacity
          key={item.title}
          style={[styles.mysteryRow, index === currentMystery && styles.activeMysteryRow]}
          onPress={() => {
            setCurrentMystery(index);
            setBead(0);
          }}
        >
          <Text style={styles.mysteryNumber}>{index + 1}</Text>
          <View style={{ flex: 1 }}>
            <Text style={styles.mysteryRowTitle}>{item.title}</Text>
            <Text style={styles.mysteryRowRef}>{item.reference}</Text>
          </View>
        </TouchableOpacity>
      ))}
    </View>
  );
}

const styles = StyleSheet.create({
  hero: {
    marginHorizontal: -18,
    marginTop: -18,
    padding: 18,
    paddingTop: 26,
    paddingBottom: 24,
    backgroundColor: colors.amber100
  },
  kicker: {
    color: colors.amber700,
    fontSize: 11,
    fontWeight: '900',
    textTransform: 'uppercase'
  },
  title: {
    marginTop: 6,
    color: colors.amber900,
    fontSize: 27,
    lineHeight: 33,
    fontWeight: '900'
  },
  lead: {
    marginTop: 9,
    color: colors.slate700,
    lineHeight: 22
  },
  setRow: {
    marginTop: 16,
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8
  },
  setButton: {
    borderWidth: 1,
    borderColor: colors.slate200,
    borderRadius: 999,
    backgroundColor: colors.white,
    paddingHorizontal: 12,
    paddingVertical: 9
  },
  activeSet: {
    backgroundColor: colors.amber800,
    borderColor: colors.amber800
  },
  setText: {
    color: colors.slate700,
    fontWeight: '900',
    fontSize: 13
  },
  activeSetText: {
    color: colors.white
  },
  card: {
    marginTop: 14,
    backgroundColor: colors.white,
    borderWidth: 1,
    borderColor: colors.slate200,
    borderRadius: radius.xl,
    padding: 16
  },
  rowBetween: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    gap: 14
  },
  days: {
    marginTop: 3,
    color: colors.slate900,
    fontWeight: '900',
    fontSize: 18
  },
  progressBadge: {
    width: 48,
    height: 48,
    borderRadius: 16,
    backgroundColor: colors.amber50,
    alignItems: 'center',
    justifyContent: 'center'
  },
  progressText: {
    color: colors.amber900,
    fontWeight: '900'
  },
  progressTrack: {
    marginTop: 16,
    height: 8,
    borderRadius: 999,
    backgroundColor: colors.slate100,
    overflow: 'hidden'
  },
  progressFill: {
    height: '100%',
    backgroundColor: colors.amber800
  },
  mysteryIndex: {
    marginTop: 18,
    color: colors.amber700,
    fontWeight: '900',
    fontSize: 12,
    textTransform: 'uppercase'
  },
  mysteryTitle: {
    marginTop: 4,
    color: colors.slate900,
    fontSize: 22,
    lineHeight: 28,
    fontWeight: '900'
  },
  reference: {
    marginTop: 6,
    color: colors.indigo700,
    fontWeight: '900'
  },
  theme: {
    marginTop: 8,
    color: colors.slate600,
    lineHeight: 20
  },
  prayerCard: {
    marginTop: 16,
    borderWidth: 1,
    borderColor: colors.amber200,
    backgroundColor: colors.amber50,
    borderRadius: radius.lg,
    padding: 14,
    flexDirection: 'row',
    gap: 10
  },
  prayerTitle: {
    color: colors.slate900,
    fontWeight: '900'
  },
  prayerHint: {
    color: colors.slate600,
    marginTop: 4
  },
  controls: {
    marginTop: 16,
    flexDirection: 'row',
    gap: 10
  },
  controlButton: {
    flex: 1,
    borderWidth: 1,
    borderColor: colors.slate200,
    borderRadius: radius.md,
    paddingVertical: 12,
    alignItems: 'center',
    justifyContent: 'center',
    flexDirection: 'row',
    gap: 6,
    backgroundColor: colors.white
  },
  primaryControl: {
    backgroundColor: colors.amber800,
    borderColor: colors.amber800
  },
  controlText: {
    color: colors.slate900,
    fontWeight: '900'
  },
  primaryControlText: {
    color: colors.white,
    fontWeight: '900'
  },
  sectionTitle: {
    marginTop: 20,
    marginBottom: 10,
    color: colors.slate900,
    fontSize: 20,
    fontWeight: '900'
  },
  mysteryRow: {
    flexDirection: 'row',
    gap: 12,
    alignItems: 'center',
    borderWidth: 1,
    borderColor: colors.slate200,
    backgroundColor: colors.white,
    borderRadius: radius.lg,
    padding: 13,
    marginBottom: 10
  },
  activeMysteryRow: {
    borderColor: colors.amber200,
    backgroundColor: colors.amber50
  },
  mysteryNumber: {
    width: 34,
    height: 34,
    borderRadius: 12,
    backgroundColor: colors.amber100,
    textAlign: 'center',
    textAlignVertical: 'center',
    color: colors.amber900,
    fontWeight: '900'
  },
  mysteryRowTitle: {
    color: colors.slate900,
    fontWeight: '900'
  },
  mysteryRowRef: {
    marginTop: 3,
    color: colors.slate500,
    fontSize: 12,
    fontWeight: '800'
  }
});
