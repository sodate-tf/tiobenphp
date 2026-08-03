import { useEffect, useMemo, useRef, useState } from 'react';
import { Alert, Linking, Modal, Pressable, ScrollView, StyleSheet, Text, TouchableOpacity, View } from 'react-native';
import type { GestureResponderEvent } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import * as Speech from 'expo-speech';
import type { Voice } from 'expo-speech';
import { api } from '../api/client';
import { AdBanner } from '../components/AdBanner';
import { ErrorState, LoadingState } from '../components/StateViews';
import { LiturgyRichText } from '../components/LiturgyRichText';
import {
  disableLiturgyRemindersAsync,
  ensureLiturgyReminderPermissionsAsync,
  formatReminderTime,
  getLiturgyReminderStateAsync,
  scheduleLiturgyRemindersAsync,
  topUpLiturgyRemindersIfNeededAsync,
  type LiturgyReminderState,
} from '../lib/liturgyReminders';
import type { LiturgyPayload, LiturgyTab } from '../types/app';
import { colors, radius } from '../theme/colors';

const reminderPresets = [
  { label: '06:30', hour: 6, minute: 30 },
  { label: '07:00', hour: 7, minute: 0 },
  { label: '12:00', hour: 12, minute: 0 },
  { label: '18:30', hour: 18, minute: 30 },
  { label: '21:00', hour: 21, minute: 0 },
];

const speedOptions = [
  { label: '0.9x', value: 0.9 },
  { label: '1.0x', value: 1 },
  { label: '1.1x', value: 1.1 },
  { label: '1.25x', value: 1.25 },
];

const barByKind: Record<LiturgyTab['kind'], string> = {
  reading: colors.indigo600,
  psalm: colors.emerald500,
  gospel: colors.rose500,
  extra: colors.sky500,
  reflection: colors.amber400,
};

const defaultReminderState: LiturgyReminderState = {
  enabled: false,
  hour: 7,
  minute: 0,
  pendingCount: 0,
};

type PlayerMode = 'idle' | 'playing' | 'paused';

type ReadingProgress = {
  tabIndex: number;
  charIndex: number;
};

const initialProgress: ReadingProgress = { tabIndex: 0, charIndex: 0 };

export function LiturgyScreen({ showAds = false }: { showAds?: boolean }) {
  const [data, setData] = useState<LiturgyPayload | null>(null);
  const [activeTabId, setActiveTabId] = useState<string | null>(null);
  const [fontStep, setFontStep] = useState(0);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [reminderState, setReminderState] = useState<LiturgyReminderState>(defaultReminderState);
  const [reminderLoading, setReminderLoading] = useState(true);
  const [reminderSaving, setReminderSaving] = useState(false);
  const [reminderModalVisible, setReminderModalVisible] = useState(false);
  const [selectedHour, setSelectedHour] = useState(defaultReminderState.hour);
  const [selectedMinute, setSelectedMinute] = useState(defaultReminderState.minute);
  const [voiceModalVisible, setVoiceModalVisible] = useState(false);
  const [availableVoices, setAvailableVoices] = useState<Voice[]>([]);
  const [voicesLoading, setVoicesLoading] = useState(true);
  const [selectedVoiceId, setSelectedVoiceId] = useState<string | null>(null);
  const [selectedRate, setSelectedRate] = useState(1);
  const [playerMode, setPlayerMode] = useState<PlayerMode>('idle');
  const [readingProgress, setReadingProgress] = useState<ReadingProgress>(initialProgress);

  const sessionIdRef = useRef(0);
  const pauseRequestedRef = useRef(false);
  const speechTabsRef = useRef<LiturgyTab[]>([]);
  const selectedVoiceIdRef = useRef<string | null>(null);
  const selectedRateRef = useRef(1);
  const readingProgressRef = useRef<ReadingProgress>(initialProgress);

  const speechTabs = useMemo(() => {
    if (!data) return [] as LiturgyTab[];
    return [...data.tabs]
      .filter((tab) => tab.kind === 'reading' || tab.kind === 'psalm' || tab.kind === 'gospel' || tab.kind === 'reflection')
      .sort(compareSpeechOrder);
  }, [data]);

  const activeTab = useMemo(() => {
    if (!data) return null;
    return data.tabs.find((tab) => tab.id === activeTabId) ?? data.tabs[0] ?? null;
  }, [data, activeTabId]);

  const activeSpeechIndex = useMemo(() => {
    if (!activeTab) return -1;
    return speechTabs.findIndex((tab) => tab.id === activeTab.id);
  }, [activeTab, speechTabs]);

  const selectedVoice = useMemo(
    () => availableVoices.find((voice) => voice.identifier === selectedVoiceId) ?? null,
    [availableVoices, selectedVoiceId],
  );

  useEffect(() => {
    speechTabsRef.current = speechTabs;
  }, [speechTabs]);

  useEffect(() => {
    selectedVoiceIdRef.current = selectedVoiceId;
  }, [selectedVoiceId]);

  useEffect(() => {
    selectedRateRef.current = selectedRate;
  }, [selectedRate]);

  useEffect(() => {
    readingProgressRef.current = readingProgress;
  }, [readingProgress]);

  useEffect(() => {
    void load();
    void loadReminderState();
    void loadVoices();

    return () => {
      Speech.stop();
    };
  }, []);

  async function load(dateSlug?: string) {
    try {
      await stopSpeechPlayback(true);
      setLoading(true);
      setError(null);
      const payload = dateSlug ? await api.liturgy(dateSlug) : await api.liturgyToday();
      setData(payload);
      setActiveTabId(payload.tabs[0]?.id ?? null);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Não foi possível carregar a liturgia.');
    } finally {
      setLoading(false);
    }
  }

  async function loadReminderState() {
    try {
      setReminderLoading(true);
      const next = await topUpLiturgyRemindersIfNeededAsync();
      setReminderState(next);
      setSelectedHour(next.hour);
      setSelectedMinute(next.minute);
    } catch {
      const next = await getLiturgyReminderStateAsync();
      setReminderState(next);
      setSelectedHour(next.hour);
      setSelectedMinute(next.minute);
    } finally {
      setReminderLoading(false);
    }
  }

  async function loadVoices() {
    try {
      setVoicesLoading(true);
      const voices = await Speech.getAvailableVoicesAsync();
      const prioritized = prioritizeVoices(voices);
      setAvailableVoices(prioritized);
      if (!selectedVoiceIdRef.current && prioritized[0]) {
        setSelectedVoiceId(prioritized[0].identifier);
      }
    } catch {
      setAvailableVoices([]);
    } finally {
      setVoicesLoading(false);
    }
  }

  function openReminderModal() {
    setSelectedHour(reminderState.hour);
    setSelectedMinute(reminderState.minute);
    setReminderModalVisible(true);
  }

  async function saveReminder() {
    try {
      setReminderSaving(true);
      const granted = await ensureLiturgyReminderPermissionsAsync();
      if (!granted) {
        Alert.alert('Notificações desativadas', 'Ative as notificações do app para receber o lembrete diário da liturgia.');
        return;
      }
      const next = await scheduleLiturgyRemindersAsync(selectedHour, selectedMinute);
      setReminderState(next);
      setReminderModalVisible(false);
    } catch (err) {
      Alert.alert('Não consegui ativar o lembrete', err instanceof Error ? err.message : 'Tente novamente em instantes.');
    } finally {
      setReminderSaving(false);
    }
  }

  async function disableReminder() {
    try {
      setReminderSaving(true);
      const next = await disableLiturgyRemindersAsync();
      setReminderState(next);
      setReminderModalVisible(false);
    } catch (err) {
      Alert.alert('Não consegui desativar', err instanceof Error ? err.message : 'Tente novamente em instantes.');
    } finally {
      setReminderSaving(false);
    }
  }

  async function pauseSpeechPlayback() {
    pauseRequestedRef.current = true;
    sessionIdRef.current += 1;
    setPlayerMode('paused');
    await Speech.stop();
  }

  async function stopSpeechPlayback(resetProgress = false) {
    pauseRequestedRef.current = false;
    sessionIdRef.current += 1;
    await Speech.stop();
    if (resetProgress) {
      readingProgressRef.current = initialProgress;
      setReadingProgress(initialProgress);
      setPlayerMode('idle');
    }
  }

  async function startSpeechPlayback(tabIndex: number, offset: number) {
    const tabs = speechTabsRef.current;
    const targetTab = tabs[tabIndex];
    if (!targetTab) {
      setPlayerMode('idle');
      setReadingProgress(initialProgress);
      return;
    }

    await Speech.stop();

    const sessionId = sessionIdRef.current + 1;
    sessionIdRef.current = sessionId;
    pauseRequestedRef.current = false;

    const fullText = buildSpeechText(targetTab);
    const safeOffset = Math.max(0, Math.min(offset, Math.max(fullText.length - 1, 0)));
    const spokenText = fullText.slice(safeOffset).trim();

    if (!spokenText) {
      if (tabIndex + 1 < tabs.length) {
        await startSpeechPlayback(tabIndex + 1, 0);
      } else {
        setPlayerMode('idle');
        setReadingProgress(initialProgress);
      }
      return;
    }

    setActiveTabId(targetTab.id);
    setPlayerMode('playing');
    setReadingProgress({ tabIndex, charIndex: safeOffset });

    Speech.speak(spokenText, {
      language: selectedVoice?.language ?? 'pt-BR',
      voice: selectedVoiceIdRef.current ?? undefined,
      rate: selectedRateRef.current,
      pitch: 1,
      onBoundary: (event: { charIndex: number }) => {
        if (sessionId !== sessionIdRef.current) return;
        const nextProgress = { tabIndex, charIndex: safeOffset + event.charIndex };
        readingProgressRef.current = nextProgress;
        setReadingProgress(nextProgress);
      },
      onDone: () => {
        if (sessionId !== sessionIdRef.current || pauseRequestedRef.current) return;
        if (tabIndex + 1 < tabs.length) {
          void startSpeechPlayback(tabIndex + 1, 0);
          return;
        }
        setPlayerMode('idle');
        readingProgressRef.current = initialProgress;
        setReadingProgress(initialProgress);
      },
      onStopped: () => {
        if (sessionId !== sessionIdRef.current || pauseRequestedRef.current) return;
        setPlayerMode('idle');
      },
      onError: () => {
        if (sessionId !== sessionIdRef.current) return;
        setPlayerMode('idle');
        Alert.alert('Leitura indisponível', 'Não consegui reproduzir a leitura agora.');
      },
    });
  }

  async function handlePrimaryPlayerAction() {
    if (!speechTabs.length) {
      Alert.alert('Sem leitura disponível', 'Não encontrei leituras suficientes para iniciar o modo leitura.');
      return;
    }

    if (playerMode === 'playing') {
      await pauseSpeechPlayback();
      return;
    }

    if (playerMode === 'paused') {
      Alert.alert('Retomar a leitura', 'Como você quer voltar para a liturgia?', [
        { text: 'Continuar de onde parou', onPress: () => void startSpeechPlayback(readingProgressRef.current.tabIndex, readingProgressRef.current.charIndex) },
        { text: 'Recomeçar esta leitura', onPress: () => void startSpeechPlayback(readingProgressRef.current.tabIndex, 0) },
        { text: 'Voltar ao início', onPress: () => void startSpeechPlayback(0, 0) },
        { text: 'Cancelar', style: 'cancel' },
      ]);
      return;
    }

    await startSpeechPlayback(activeSpeechIndex >= 0 ? activeSpeechIndex : 0, 0);
  }

  async function handleTabPress(tab: LiturgyTab) {
    setActiveTabId(tab.id);
    const nextIndex = speechTabsRef.current.findIndex((item) => item.id === tab.id);
    if (nextIndex < 0) return;

    const nextProgress = { tabIndex: nextIndex, charIndex: 0 };
    readingProgressRef.current = nextProgress;
    setReadingProgress(nextProgress);

    if (playerMode === 'playing') {
      await startSpeechPlayback(nextIndex, 0);
    }
  }

  if (loading && !data) return <LoadingState label="Carregando liturgia..." />;
  if (error && !data) return <ErrorState message={error} onRetry={() => void load()} />;
  if (!data) return null;

  const active = activeTab ?? data.tabs[0] ?? null;
  const bodySize = 18 + fontStep;
  const reminderTimeLabel = formatReminderTime(reminderState.hour, reminderState.minute);
  const playerTab = speechTabs[readingProgress.tabIndex] ?? speechTabs[0] ?? null;
  const playerSummary = playerMode === 'playing'
    ? `Lendo agora: ${playerTab?.label ?? 'Liturgia da Palavra'}`
    : playerMode === 'paused'
      ? `Pausado em ${playerTab?.label ?? 'uma leitura'}.`
      : 'Toque em play para ouvir a partir da aba ativa e seguir em sequência até o fim.';

  return (
    <View>
      <View style={styles.heroCard}>
        <View style={styles.heroTopLine}>
          <Text style={styles.kicker}>IA Tio Ben · Liturgia</Text>
          <TouchableOpacity style={styles.iconButton} onPress={openReminderModal}>
            <Ionicons name={reminderState.enabled ? 'notifications' : 'notifications-outline'} size={17} color={colors.white} />
          </TouchableOpacity>
        </View>
        <Text style={styles.title}>Liturgia diária de {data.dateHuman}</Text>
        <View style={styles.chips}>
          {data.celebration ? <Text style={styles.chip}>{data.celebration}</Text> : null}
          {data.color ? <Text style={[styles.chip, styles.chipAccent]}>Cor: {data.color}</Text> : null}
        </View>
        <Text style={styles.summary}>{data.summary}</Text>

        <TouchableOpacity style={[styles.reminderCard, reminderState.enabled && styles.reminderCardActive]} onPress={openReminderModal}>
          <View style={[styles.reminderBadge, reminderState.enabled && styles.reminderBadgeActive]}>
            <Ionicons name={reminderState.enabled ? 'notifications' : 'notifications-outline'} size={18} color={reminderState.enabled ? colors.white : colors.amber900} />
          </View>
          <View style={{ flex: 1, gap: 6 }}>
            <View style={styles.rowBetween}>
              <Text style={styles.reminderTitle}>Lembrete da liturgia</Text>
              <Text style={[styles.statusPill, reminderState.enabled && styles.statusPillActive]}>{reminderState.enabled ? 'Ativo' : 'Ativar'}</Text>
            </View>
            <Text style={styles.reminderText}>
              {reminderLoading
                ? 'Conferindo seu lembrete...'
                : reminderState.enabled
                  ? `Todos os dias às ${reminderTimeLabel}, com uma chamada diferente para lembrar você da liturgia.`
                  : 'Escolha um horário e receba todos os dias um lembrete para ler a liturgia de hoje.'}
            </Text>
          </View>
        </TouchableOpacity>
      </View>

      <View style={styles.navRow}>
        <NavButton label="Ontem" onPress={() => void load(data.navigation.previous)} />
        <NavButton label="Hoje" primary onPress={() => void load(data.navigation.today)} />
        <NavButton label="Amanhã" onPress={() => void load(data.navigation.next)} />
      </View>

      <View style={styles.toolbar}>
        <ToolButton label="A-" onPress={() => setFontStep((value) => Math.max(value - 1, -2))} />
        <ToolButton label="Padrão" onPress={() => setFontStep(0)} />
        <ToolButton label="A+" onPress={() => setFontStep((value) => Math.min(value + 1, 6))} />
      </View>

      <View style={styles.playerCard}>
        <View style={styles.playerHeader}>
          <View style={styles.playerBadge}><Ionicons name="headset" size={18} color={colors.amber900} /></View>
          <View style={{ flex: 1 }}>
            <Text style={styles.playerTitle}>Modo leitura</Text>
            <Text style={styles.playerLead}>{playerSummary}</Text>
          </View>
        </View>
        {speechTabs.length ? <Text style={styles.playerMeta}>{playerTab ? `${readingProgress.tabIndex + 1} de ${speechTabs.length} · ${playerTab.label}` : `${speechTabs.length} leituras`}</Text> : null}
        <View style={styles.playerActions}>
          <TouchableOpacity style={[styles.primaryButton, playerMode === 'playing' && styles.primaryButtonAlt]} onPress={() => void handlePrimaryPlayerAction()}>
            <Ionicons name={playerMode === 'playing' ? 'pause' : 'play'} size={18} color={playerMode === 'playing' ? colors.slate900 : colors.white} />
            <Text style={[styles.primaryButtonText, playerMode === 'playing' && styles.primaryButtonTextAlt]}>{playerMode === 'playing' ? 'Pausar' : playerMode === 'paused' ? 'Retomar' : 'Ouvir liturgia'}</Text>
          </TouchableOpacity>
          <TouchableOpacity style={styles.secondaryButton} onPress={() => setVoiceModalVisible(true)}>
            <Ionicons name="person-circle-outline" size={18} color={colors.slate900} />
            <Text style={styles.secondaryButtonText} numberOfLines={1}>{voicesLoading ? 'Carregando voz...' : selectedVoice ? selectedVoice.name : 'Voz do aparelho'}</Text>
          </TouchableOpacity>
        </View>
        <Text style={styles.sectionTitle}>Velocidade</Text>
        <View style={styles.wrapRow}>
          {speedOptions.map((option) => {
            const isActive = option.value === selectedRate;
            return (
              <TouchableOpacity key={option.label} style={[styles.pill, isActive && styles.pillActive]} onPress={() => setSelectedRate(option.value)}>
                <Text style={[styles.pillText, isActive && styles.pillTextActive]}>{option.label}</Text>
              </TouchableOpacity>
            );
          })}
        </View>
      </View>

      {showAds ? <AdBanner placement="liturgyFeed" /> : null}

      <View style={styles.wrapRow}>
        {data.tabs.map((tab) => {
          const isActive = active?.id === tab.id;
          return (
            <TouchableOpacity key={tab.id} style={[styles.tabButton, isActive && styles.tabButtonActive]} onPress={() => void handleTabPress(tab)}>
              <Text style={[styles.tabText, isActive && styles.tabTextActive]}>{tab.label}</Text>
            </TouchableOpacity>
          );
        })}
      </View>

      {active ? (
        <View style={styles.readingCard}>
          <View style={[styles.readingBar, { backgroundColor: barByKind[active.kind] }]} />
          <View style={styles.readingBody}>
            <Text style={[styles.readingLabel, active.kind === 'gospel' && styles.readingLabelGospel]}>{getCardLabel(active)}</Text>
            {active.reference ? <Text style={styles.reference}>{active.reference}</Text> : null}
            {active.kind === 'psalm' && active.refrain ? (
              <View style={styles.refrainCard}>
                <Text style={styles.refrainLabel}>Refrão</Text>
                <Text style={styles.refrainText}>{active.refrain}</Text>
              </View>
            ) : null}
            {active.title ? <Text style={styles.readingTitle}>{active.title}</Text> : null}
            <LiturgyRichText html={active.html} fallbackText={active.text} fontSize={bodySize} lineHeight={bodySize * 1.85} />
            {(active.kind === 'reading' || active.kind === 'gospel') ? (
              <View style={styles.acclamationBlock}>
                <Text style={[styles.acclamationLead, active.kind === 'gospel' && styles.acclamationLeadGospel]}>{getAcclamationLead(active.kind)}</Text>
                <Text style={styles.acclamationResponse}>{getAcclamationResponse(active.kind)}</Text>
              </View>
            ) : null}
            {active.kind === 'reflection' && active.sourceUrl ? (
              <TouchableOpacity style={styles.sourceButton} onPress={() => void Linking.openURL(active.sourceUrl!)}>
                <Ionicons name="open-outline" size={16} color={colors.amber800} />
                <Text style={styles.sourceButtonText}>{active.sourceLabel ?? 'Vatican News - Palavra do Dia'}</Text>
              </TouchableOpacity>
            ) : null}
          </View>
        </View>
      ) : <ErrorState message="Nenhuma leitura encontrada para esta data." />}

      <Modal transparent animationType="fade" visible={reminderModalVisible} onRequestClose={() => setReminderModalVisible(false)}>
        <Pressable style={styles.modalOverlay} onPress={() => setReminderModalVisible(false)}>
          <Pressable style={styles.modalCard} onPress={(event: GestureResponderEvent) => event.stopPropagation()}>
            <Text style={styles.modalTitle}>Lembrete da liturgia</Text>
            <Text style={styles.modalText}>Essa notificação existe para te lembrar todos os dias de ler a liturgia. Escolha o horário que faz mais sentido para você.</Text>
            <Text style={styles.sectionTitle}>Sugestões rápidas</Text>
            <View style={styles.wrapRow}>
              {reminderPresets.map((preset) => {
                const isActive = preset.hour === selectedHour && preset.minute === selectedMinute;
                return (
                  <TouchableOpacity key={preset.label} style={[styles.pill, isActive && styles.pillActive]} onPress={() => { setSelectedHour(preset.hour); setSelectedMinute(preset.minute); }}>
                    <Text style={[styles.pillText, isActive && styles.pillTextActive]}>{preset.label}</Text>
                  </TouchableOpacity>
                );
              })}
            </View>
            <View style={styles.timeRow}>
              <TimeStepper label="Hora" value={selectedHour} onDecrease={() => setSelectedHour((value) => (value - 1 + 24) % 24)} onIncrease={() => setSelectedHour((value) => (value + 1) % 24)} />
              <TimeStepper label="Min" value={selectedMinute} onDecrease={() => setSelectedMinute((value) => (value - 5 + 60) % 60)} onIncrease={() => setSelectedMinute((value) => (value + 5) % 60)} />
            </View>
            <Text style={styles.selectedTime}>Lembrete diário às {formatReminderTime(selectedHour, selectedMinute)}</Text>
            <View style={styles.modalActions}>
              {reminderState.enabled ? <ToolButton label="Desativar" onPress={() => void disableReminder()} /> : null}
              <TouchableOpacity style={styles.primaryButton} onPress={() => void saveReminder()} disabled={reminderSaving}><Text style={styles.primaryButtonText}>{reminderSaving ? 'Salvando...' : 'Salvar lembrete'}</Text></TouchableOpacity>
            </View>
          </Pressable>
        </Pressable>
      </Modal>

      <Modal transparent animationType="fade" visible={voiceModalVisible} onRequestClose={() => setVoiceModalVisible(false)}>
        <Pressable style={styles.modalOverlay} onPress={() => setVoiceModalVisible(false)}>
          <Pressable style={styles.modalCard} onPress={(event: GestureResponderEvent) => event.stopPropagation()}>
            <Text style={styles.modalTitle}>Escolha quem vai ler</Text>
            <Text style={styles.modalText}>As vozes abaixo são as que o próprio aparelho oferece para leitura em voz alta.</Text>
            <ScrollView style={{ maxHeight: 320 }} contentContainerStyle={{ gap: 10 }}>
              {availableVoices.map((voice) => {
                const isActive = voice.identifier === selectedVoiceId;
                return (
                  <TouchableOpacity key={voice.identifier} style={[styles.voiceButton, isActive && styles.voiceButtonActive]} onPress={() => { setSelectedVoiceId(voice.identifier); setVoiceModalVisible(false); }}>
                    <View style={{ flex: 1 }}>
                      <Text style={[styles.voiceTitle, isActive && styles.voiceTitleActive]}>{voice.name}</Text>
                      <Text style={[styles.voiceMeta, isActive && styles.voiceMetaActive]}>{voice.language}</Text>
                    </View>
                    {isActive ? <Ionicons name="checkmark-circle" size={20} color={colors.amber800} /> : null}
                  </TouchableOpacity>
                );
              })}
              {!availableVoices.length && !voicesLoading ? <Text style={styles.modalText}>Não encontrei vozes disponíveis neste aparelho.</Text> : null}
            </ScrollView>
          </Pressable>
        </Pressable>
      </Modal>
    </View>
  );
}

function NavButton({ label, primary, onPress }: { label: string; primary?: boolean; onPress: () => void }) {
  return (
    <TouchableOpacity style={[styles.navButton, primary && styles.navButtonPrimary]} onPress={onPress}>
      <Text style={[styles.navButtonText, primary && styles.navButtonTextPrimary]}>{label}</Text>
    </TouchableOpacity>
  );
}

function ToolButton({ label, onPress }: { label: string; onPress: () => void }) {
  return <TouchableOpacity style={styles.toolButton} onPress={onPress}><Text style={styles.toolButtonText}>{label}</Text></TouchableOpacity>;
}

function TimeStepper({ label, value, onDecrease, onIncrease }: { label: string; value: number; onDecrease: () => void; onIncrease: () => void }) {
  return (
    <View style={styles.timeCard}>
      <Text style={styles.timeLabel}>{label}</Text>
      <TouchableOpacity style={styles.arrowButton} onPress={onIncrease}><Ionicons name="chevron-up" size={18} color={colors.slate900} /></TouchableOpacity>
      <Text style={styles.timeValue}>{String(value).padStart(2, '0')}</Text>
      <TouchableOpacity style={styles.arrowButton} onPress={onDecrease}><Ionicons name="chevron-down" size={18} color={colors.slate900} /></TouchableOpacity>
    </View>
  );
}

function prioritizeVoices(voices: Voice[]) {
  return [...voices].sort((left, right) => getVoiceScore(right) - getVoiceScore(left) || left.name.localeCompare(right.name));
}

function getVoiceScore(voice: Voice) {
  let score = 0;
  const language = voice.language.toLowerCase();
  const name = voice.name.toLowerCase();
  if (language.startsWith('pt-br')) score += 4;
  else if (language.startsWith('pt')) score += 3;
  if (name.includes('female') || name.includes('male') || name.includes('natural')) score += 1;
  return score;
}

function compareSpeechOrder(left: LiturgyTab, right: LiturgyTab) {
  return getSpeechOrder(left) - getSpeechOrder(right) || left.id.localeCompare(right.id);
}

function getSpeechOrder(tab: LiturgyTab) {
  if (tab.id.startsWith('primeiraLeitura')) return 0;
  if (tab.id.startsWith('salmo')) return 1;
  if (tab.id.startsWith('segundaLeitura')) return 2;
  if (tab.id.startsWith('evangelho')) return 3;
  if (tab.kind === 'reflection') return 4;
  return 5;
}

function getCardLabel(tab: LiturgyTab) {
  if (tab.kind === 'psalm') return 'Salmo Responsorial';
  if (tab.kind === 'gospel') return 'Evangelho';
  if (tab.kind === 'reflection') return 'Reflexão da Palavra';
  return tab.label.toUpperCase();
}

function getAcclamationLead(kind: LiturgyTab['kind']) {
  return kind === 'gospel' ? 'Palavra da Salvação.' : 'Palavra do Senhor.';
}

function getAcclamationResponse(kind: LiturgyTab['kind']) {
  return kind === 'gospel' ? 'Glória a vós, Senhor.' : 'Graças a Deus.';
}

function buildSpeechText(tab: LiturgyTab) {
  const text = [
    getSpokenLabel(tab),
    narrateReference(tab.reference),
    stripVerseNumbersForSpeech(tab.title ?? ''),
    getSpeechBody(tab),
    getSpeechEnding(tab.kind),
  ].filter(Boolean);

  return text.join('. ').replace(/\s+/g, ' ').trim();
}

function getSpokenLabel(tab: LiturgyTab) {
  if (tab.kind === 'gospel') return 'Evangelho';
  if (tab.kind === 'psalm') return 'Salmo responsorial';
  if (tab.kind === 'reflection') return 'Reflexão da Palavra. As palavras dos Papas';
  return tab.label.replace(/^1(?:ª|a)?/i, 'Primeira').replace(/^2(?:ª|a)?/i, 'Segunda');
}

function getSpeechEnding(kind: LiturgyTab['kind']) {
  if (kind === 'gospel') return 'Palavra da Salvação. Glória a vós, Senhor.';
  if (kind === 'reading') return 'Palavra do Senhor. Graças a Deus.';
  if (kind === 'reflection') return 'Fonte: Vatican News, Palavra do Dia.';
  return '';
}

function getSpeechBody(tab: LiturgyTab) {
  const fromHtml = htmlToSpeech(tab.html);
  return stripVerseNumbersForSpeech((fromHtml || tab.text).replace(/\s+/g, ' ').trim());
}

function htmlToSpeech(html: string | null) {
  if (!html?.trim()) return '';
  return decodeHtmlEntities(
    html
      .replace(/<sup[^>]*>.*?<\/sup>/gisu, ' ')
      .replace(/<br\s*\/?>/gi, '\n')
      .replace(/<\/p>/gi, '\n\n')
      .replace(/<[^>]+>/g, ' '),
  )
    .replace(/[ \t]+/g, ' ')
    .replace(/ *\n */g, '\n')
    .replace(/\n{3,}/g, '\n\n')
    .trim();
}

function decodeHtmlEntities(text: string) {
  return text
    .replace(/&nbsp;/gi, ' ')
    .replace(/&amp;/gi, '&')
    .replace(/&quot;/gi, '"')
    .replace(/&#039;/gi, "'")
    .replace(/&lt;/gi, '<')
    .replace(/&gt;/gi, '>')
    .replace(/&#(\d+);/g, (_, code) => String.fromCharCode(Number(code)))
    .replace(/&#x([0-9a-f]+);/gi, (_, code) => String.fromCharCode(parseInt(code, 16)));
}

function stripVerseNumbersForSpeech(text: string) {
  return text
    .replace(/\r/g, '')
    .replace(/(^|\n)\s*\d{1,3}[a-z]?(?=[\p{L}“"'])/gimu, '$1')
    .replace(/([\s(\["'“])\d{1,3}[a-z]?(?=[\p{L}“"'])/gu, '$1')
    .replace(/([,.;:!?])\d{1,3}[a-z]?(?=[\p{L}“"'])/gu, '$1 ')
    .replace(/(^|\n)\s*\d{1,3}[a-z]?\s+(?=[\p{L}])/gimu, '$1')
    .replace(/\s+/g, ' ')
    .trim();
}

function narrateReference(reference: string | null) {
  if (!reference) return '';
  const normalized = reference.replace(/\s+/g, ' ').trim();
  const match = normalized.match(/^([1-3]?\s?[\p{L}]+)\s+(.+)$/u);
  if (!match) return normalized;

  const bookKey = normalizeBookKey(match[1]);
  const bookName = bibleBookNames[bookKey] ?? match[1].trim();
  const rest = match[2].trim();

  if (bookKey === 'sl') return narratePsalmReference(bookName, rest);
  if (singleChapterBooks.has(bookKey)) return `${bookName}, ${narrateVerseSequence(rest, false)}`;

  const parts = rest.split(';').map((part) => part.trim()).filter(Boolean).map(narrateChapterAndVerses).filter(Boolean);
  return [bookName, ...parts].join(', ');
}

function narratePsalmReference(bookName: string, rest: string) {
  const parts = rest.split(',');
  const chapter = parts[0]?.trim();
  const verses = parts.slice(1).join(',').trim();
  if (!chapter) return bookName;
  if (!verses) return `${bookName} ${chapter}`;
  return `${bookName} ${chapter}, ${narrateVerseSequence(verses, true)}`;
}

function narrateChapterAndVerses(part: string) {
  const match = part.match(/^(\d+)\s*,\s*(.+)$/);
  if (!match) return /^\d+$/.test(part) ? `capítulo ${part}` : part;
  return `capítulo ${match[1]}, ${narrateVerseSequence(match[2], true)}`;
}

function narrateVerseSequence(raw: string, includePrefix: boolean) {
  const chunks = raw.replace(/\s+/g, ' ').trim().split('.').map((chunk) => chunk.trim()).filter(Boolean).map(narrateVerseChunk);
  if (!chunks.length) return includePrefix ? 'versículos' : '';
  const joined = joinHumanList(chunks);
  if (!includePrefix) return joined;
  return chunks.length === 1 && !chunks[0].startsWith('do ') ? `versículo ${joined}` : `versículos ${joined}`;
}

function narrateVerseChunk(chunk: string) {
  const rangeMatch = chunk.match(/^(\d+)\s*-\s*(\d+)$/);
  return rangeMatch ? `do ${rangeMatch[1]} ao ${rangeMatch[2]}` : chunk;
}

function joinHumanList(items: string[]) {
  if (items.length <= 1) return items[0] ?? '';
  if (items.length === 2) return `${items[0]} e ${items[1]}`;
  return `${items.slice(0, -1).join(', ')} e ${items[items.length - 1]}`;
}

function normalizeBookKey(book: string) {
  return book.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/\./g, '').replace(/\s+/g, '');
}

const bibleBookNames: Record<string, string> = {
  gn: 'Gênesis', ex: 'Êxodo', lv: 'Levítico', nm: 'Números', dt: 'Deuteronômio', js: 'Josué', jz: 'Juízes', rt: 'Rute',
  '1sm': 'Primeiro livro de Samuel', '2sm': 'Segundo livro de Samuel', '1rs': 'Primeiro livro dos Reis', '2rs': 'Segundo livro dos Reis',
  '1cr': 'Primeiro livro das Crônicas', '2cr': 'Segundo livro das Crônicas', esd: 'Esdras', ne: 'Neemias', tb: 'Tobias', jdt: 'Judite', est: 'Ester',
  '1mc': 'Primeiro livro dos Macabeus', '2mc': 'Segundo livro dos Macabeus', jo: 'Jó', sl: 'Salmo', pr: 'Provérbios', ecl: 'Eclesiastes',
  ct: 'Cântico dos Cânticos', sb: 'Sabedoria', eclo: 'Eclesiástico', is: 'Isaías', jr: 'Jeremias', lm: 'Lamentações', br: 'Baruc', ez: 'Ezequiel',
  dn: 'Daniel', os: 'Oseias', jl: 'Joel', am: 'Amós', ab: 'Abdias', jn: 'Jonas', mq: 'Miqueias', na: 'Naum', hab: 'Habacuc', sf: 'Sofonias',
  ag: 'Ageu', zc: 'Zacarias', ml: 'Malaquias', mt: 'Mateus', mc: 'Marcos', lc: 'Lucas', joa: 'João', at: 'Atos dos Apóstolos', rm: 'Romanos',
  '1cor': 'Primeira carta aos Coríntios', '2cor': 'Segunda carta aos Coríntios', gl: 'Gálatas', ef: 'Efésios', fl: 'Filipenses', cl: 'Colossenses',
  '1ts': 'Primeira carta aos Tessalonicenses', '2ts': 'Segunda carta aos Tessalonicenses', '1tm': 'Primeira carta a Timóteo', '2tm': 'Segunda carta a Timóteo',
  tt: 'Tito', fm: 'Filemon', hb: 'Hebreus', tg: 'Tiago', '1pd': 'Primeira carta de Pedro', '2pd': 'Segunda carta de Pedro',
  '1jo': 'Primeira carta de João', '2jo': 'Segunda carta de João', '3jo': 'Terceira carta de João', jd: 'Judas', ap: 'Apocalipse',
};

const singleChapterBooks = new Set(['ab', 'fm', '2jo', '3jo', 'jd']);

const styles = StyleSheet.create({
  heroCard: { marginHorizontal: -18, marginTop: -18, padding: 18, paddingTop: 22, backgroundColor: colors.amber100 },
  heroTopLine: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', gap: 12 },
  iconButton: { width: 38, height: 38, borderRadius: 12, alignItems: 'center', justifyContent: 'center', backgroundColor: colors.amber800 },
  kicker: { color: colors.amber700, fontSize: 11, fontWeight: '900', textTransform: 'uppercase' },
  title: { marginTop: 8, color: colors.slate900, fontSize: 26, lineHeight: 32, fontWeight: '900' },
  chips: { marginTop: 12, flexDirection: 'row', flexWrap: 'wrap', gap: 8 },
  chip: { borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, borderRadius: 999, paddingHorizontal: 10, paddingVertical: 5, color: colors.slate700, fontSize: 12, fontWeight: '800' },
  chipAccent: { borderColor: colors.amber200, backgroundColor: colors.amber50, color: colors.amber800 },
  summary: { marginTop: 12, color: colors.slate700, lineHeight: 22 },
  reminderCard: { marginTop: 16, borderWidth: 1, borderColor: colors.amber200, backgroundColor: colors.white, borderRadius: radius.xl, padding: 14, flexDirection: 'row', gap: 12 },
  reminderCardActive: { borderColor: colors.amber400, backgroundColor: colors.amber50 },
  reminderBadge: { width: 44, height: 44, borderRadius: 14, alignItems: 'center', justifyContent: 'center', backgroundColor: colors.amber100 },
  reminderBadgeActive: { backgroundColor: colors.amber800 },
  rowBetween: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', gap: 10 },
  reminderTitle: { color: colors.slate900, fontSize: 16, fontWeight: '900' },
  statusPill: { color: colors.slate700, fontSize: 11, fontWeight: '900', borderWidth: 1, borderColor: colors.slate200, borderRadius: 999, paddingHorizontal: 10, paddingVertical: 4 },
  statusPillActive: { color: colors.amber900, borderColor: colors.amber200, backgroundColor: colors.amber100 },
  reminderText: { color: colors.slate600, lineHeight: 20 },
  navRow: { marginTop: 16, flexDirection: 'row', gap: 8 },
  navButton: { flex: 1, borderRadius: radius.md, borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, paddingVertical: 11, alignItems: 'center' },
  navButtonPrimary: { backgroundColor: colors.amber800, borderColor: colors.amber800 },
  navButtonText: { color: colors.slate900, fontWeight: '900' },
  navButtonTextPrimary: { color: colors.white },
  toolbar: { marginTop: 16, flexDirection: 'row', gap: 8 },
  toolButton: { borderRadius: radius.md, borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, paddingHorizontal: 12, paddingVertical: 9 },
  toolButtonText: { color: colors.slate900, fontWeight: '900' },
  playerCard: { marginTop: 16, borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, borderRadius: radius.xl, padding: 16, gap: 14 },
  playerHeader: { flexDirection: 'row', gap: 12, alignItems: 'flex-start' },
  playerBadge: { width: 44, height: 44, borderRadius: 14, backgroundColor: colors.amber100, alignItems: 'center', justifyContent: 'center' },
  playerTitle: { color: colors.slate900, fontSize: 18, fontWeight: '900' },
  playerLead: { marginTop: 4, color: colors.slate600, lineHeight: 20 },
  playerMeta: { color: colors.amber900, fontWeight: '900' },
  playerActions: { flexDirection: 'row', flexWrap: 'wrap', gap: 10 },
  primaryButton: { flexDirection: 'row', alignItems: 'center', gap: 7, borderRadius: radius.md, backgroundColor: colors.amber800, paddingHorizontal: 16, paddingVertical: 11 },
  primaryButtonAlt: { backgroundColor: colors.amber100 },
  primaryButtonText: { color: colors.white, fontWeight: '900' },
  primaryButtonTextAlt: { color: colors.slate900 },
  secondaryButton: { flex: 1, minWidth: 180, borderRadius: radius.md, borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, paddingHorizontal: 14, paddingVertical: 11, flexDirection: 'row', alignItems: 'center', gap: 8 },
  secondaryButtonText: { flex: 1, color: colors.slate900, fontWeight: '800' },
  sectionTitle: { color: colors.slate900, fontWeight: '900' },
  wrapRow: { marginTop: 16, flexDirection: 'row', flexWrap: 'wrap', gap: 8 },
  pill: { borderRadius: 999, borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, paddingHorizontal: 12, paddingVertical: 8 },
  pillActive: { backgroundColor: colors.slate900, borderColor: colors.slate900 },
  pillText: { color: colors.slate700, fontWeight: '900' },
  pillTextActive: { color: colors.white },
  tabButton: { borderRadius: 999, borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, paddingHorizontal: 13, paddingVertical: 9 },
  tabButtonActive: { backgroundColor: colors.amber800, borderColor: colors.amber800 },
  tabText: { color: colors.slate700, fontWeight: '900', fontSize: 13 },
  tabTextActive: { color: colors.white },
  readingCard: { marginTop: 16, overflow: 'hidden', borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, borderRadius: radius.md },
  readingBar: { height: 6 },
  readingBody: { padding: 18 },
  readingLabel: { color: colors.amber700, fontSize: 11, fontWeight: '900', textTransform: 'uppercase' },
  readingLabelGospel: { color: colors.rose500 },
  reference: { marginTop: 6, color: colors.slate900, fontSize: 18, fontWeight: '900' },
  readingTitle: { marginTop: 4, color: colors.slate600, fontWeight: '700' },
  refrainCard: { marginTop: 16, borderWidth: 1, borderColor: colors.amber100, backgroundColor: colors.amber50, borderRadius: radius.lg, padding: 14 },
  refrainLabel: { color: colors.amber800, fontSize: 11, fontWeight: '900', textTransform: 'uppercase' },
  refrainText: { marginTop: 6, color: colors.amber900, fontSize: 18, lineHeight: 26, fontWeight: '900' },
  acclamationBlock: { marginTop: 28, gap: 6 },
  acclamationLead: { color: colors.slate900, fontSize: 18, fontWeight: '900' },
  acclamationLeadGospel: { color: colors.rose500 },
  acclamationResponse: { color: colors.slate700, fontSize: 18, lineHeight: 27 },
  sourceButton: { marginTop: 18, alignSelf: 'flex-start', flexDirection: 'row', alignItems: 'center', gap: 7, borderRadius: radius.md, borderWidth: 1, borderColor: colors.amber200, backgroundColor: colors.amber50, paddingHorizontal: 12, paddingVertical: 9 },
  sourceButtonText: { color: colors.amber800, fontWeight: '900' },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(15, 23, 42, 0.42)', padding: 18, justifyContent: 'center' },
  modalCard: { borderRadius: radius.xl, backgroundColor: colors.white, padding: 18, gap: 16 },
  modalTitle: { color: colors.slate900, fontSize: 20, fontWeight: '900' },
  modalText: { color: colors.slate600, lineHeight: 21 },
  timeRow: { flexDirection: 'row', gap: 12 },
  timeCard: { flex: 1, borderRadius: radius.lg, borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.slate50, padding: 14, alignItems: 'center', gap: 8 },
  timeLabel: { color: colors.slate500, fontSize: 12, fontWeight: '800', textTransform: 'uppercase' },
  arrowButton: { width: 36, height: 36, borderRadius: 12, borderWidth: 1, borderColor: colors.slate200, backgroundColor: colors.white, alignItems: 'center', justifyContent: 'center' },
  timeValue: { color: colors.slate900, fontSize: 26, fontWeight: '900' },
  selectedTime: { color: colors.amber900, fontWeight: '900' },
  modalActions: { flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'flex-end', gap: 10 },
  voiceButton: { flexDirection: 'row', alignItems: 'center', gap: 12, borderWidth: 1, borderColor: colors.slate200, borderRadius: radius.lg, padding: 14, backgroundColor: colors.white },
  voiceButtonActive: { borderColor: colors.amber400, backgroundColor: colors.amber50 },
  voiceTitle: { color: colors.slate900, fontWeight: '900' },
  voiceTitleActive: { color: colors.amber900 },
  voiceMeta: { marginTop: 3, color: colors.slate500, fontSize: 12, fontWeight: '700' },
  voiceMetaActive: { color: colors.amber700 },
});
