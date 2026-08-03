import { useEffect, useRef, useState } from 'react';
import { Image, KeyboardAvoidingView, Platform, ScrollView, StyleSheet, Text, TextInput, TouchableOpacity, View } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { askTioBen } from '../api/client';
import { logAnalyticsEvent } from '../lib/analytics';
import { preloadInterstitialAd, showInterstitialAdIfReady } from '../lib/mobileAds';
import { colors, radius } from '../theme/colors';

const tioBenIcon = require('../../assets/tio-ben-icon.png');

type Message = {
  role: 'user' | 'assistant';
  content: string;
};

export function ChatScreen() {
  const [messages, setMessages] = useState<Message[]>([]);
  const [input, setInput] = useState('');
  const [sending, setSending] = useState(false);
  const scrollRef = useRef<ScrollView>(null);
  const questionCountRef = useRef(0);

  useEffect(() => {
    void preloadInterstitialAd('chatQuestions');
  }, []);

  async function submit() {
    const pergunta = input.trim();
    if (!pergunta || sending) return;

    void logAnalyticsEvent('chat_question_sent', {
      question_length: pergunta.length,
      previous_messages_count: messages.length
    });

    setInput('');
    setSending(true);
    const nextMessages: Message[] = [...messages, { role: 'user', content: pergunta }];
    const nextQuestionCount = questionCountRef.current + 1;
    const shouldShowInterstitial = nextQuestionCount % 3 === 0;
    questionCountRef.current = nextQuestionCount;
    setMessages(nextMessages);

    try {
      const resposta = await askTioBen(pergunta, nextMessages.slice(-6));

      if (shouldShowInterstitial) {
        const shown = await showInterstitialAdIfReady('chatQuestions');
        void logAnalyticsEvent('chat_interstitial_attempted', {
          question_count: nextQuestionCount,
          shown: shown ? 1 : 0
        });
      }

      setMessages([...nextMessages, { role: 'assistant', content: resposta }]);
      void logAnalyticsEvent('chat_answer_received', {
        answer_length: resposta.length,
        question_count: nextQuestionCount
      });
    } catch (err) {
      setMessages([
        ...nextMessages,
        {
          role: 'assistant',
          content: err instanceof Error ? err.message : 'Nao consegui responder agora. Tente novamente.'
        }
      ]);
      void logAnalyticsEvent('chat_answer_failed', {
        previous_messages_count: nextMessages.length,
        question_count: nextQuestionCount
      });
    } finally {
      setSending(false);
      setTimeout(() => scrollRef.current?.scrollToEnd({ animated: true }), 80);
    }
  }

  return (
    <KeyboardAvoidingView behavior={Platform.OS === 'ios' ? 'padding' : undefined} style={styles.wrap}>
      <View style={styles.intro}>
        <View style={styles.avatar}>
          <Image source={tioBenIcon} style={styles.avatarImage} resizeMode="contain" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.title}>Pergunte ao IA Tio Ben</Text>
          <Text style={styles.lead}>Tire duvidas sobre fe catolica, Evangelho, oracao e vida espiritual.</Text>
        </View>
      </View>

      <ScrollView
        ref={scrollRef}
        style={styles.messages}
        contentContainerStyle={styles.messagesContent}
        onContentSizeChange={() => scrollRef.current?.scrollToEnd({ animated: true })}
      >
        {messages.length === 0 ? (
          <View style={styles.emptyCard}>
            <Text style={styles.emptyTitle}>Comece com uma pergunta</Text>
            <Text style={styles.emptyText}>Exemplo: "Como posso rezar melhor com o Evangelho de hoje?"</Text>
          </View>
        ) : null}

        {messages.map((message, index) => {
          const user = message.role === 'user';
          return (
            <View key={`${message.role}-${index}`} style={[styles.bubbleRow, user && styles.bubbleRowUser]}>
              <View style={[styles.bubble, user ? styles.userBubble : styles.assistantBubble]}>
                <Text style={[styles.bubbleText, user && styles.userBubbleText]}>{message.content}</Text>
              </View>
            </View>
          );
        })}

        {sending ? (
          <View style={styles.bubbleRow}>
            <View style={styles.assistantBubble}>
              <Text style={styles.bubbleText}>Pesquisando...</Text>
            </View>
          </View>
        ) : null}
      </ScrollView>

      <View style={styles.inputWrap}>
        <TextInput
          style={styles.input}
          value={input}
          onChangeText={setInput}
          placeholder="Digite sua pergunta ao Tio Ben..."
          placeholderTextColor={colors.slate500}
          multiline
        />
        <TouchableOpacity style={[styles.sendButton, (!input.trim() || sending) && styles.sendButtonDisabled]} onPress={submit}>
          <Ionicons name="send" size={18} color={colors.white} />
        </TouchableOpacity>
      </View>
    </KeyboardAvoidingView>
  );
}

const styles = StyleSheet.create({
  wrap: {
    flex: 1
  },
  intro: {
    flexDirection: 'row',
    gap: 12,
    borderWidth: 1,
    borderColor: colors.amber200,
    backgroundColor: colors.amber50,
    borderRadius: radius.xl,
    padding: 14
  },
  avatar: {
    width: 54,
    height: 54,
    borderRadius: 16,
    alignItems: 'center',
    justifyContent: 'center',
    overflow: 'hidden',
    backgroundColor: colors.white,
    borderWidth: 1,
    borderColor: colors.amber200
  },
  avatarImage: {
    width: 52,
    height: 52
  },
  title: {
    color: colors.amber900,
    fontSize: 18,
    fontWeight: '900'
  },
  lead: {
    marginTop: 4,
    color: colors.slate700,
    lineHeight: 19
  },
  messages: {
    flex: 1,
    marginTop: 14
  },
  messagesContent: {
    paddingBottom: 12
  },
  emptyCard: {
    borderWidth: 1,
    borderColor: colors.slate200,
    backgroundColor: colors.white,
    borderRadius: radius.lg,
    padding: 16
  },
  emptyTitle: {
    color: colors.slate900,
    fontWeight: '900',
    fontSize: 16
  },
  emptyText: {
    marginTop: 6,
    color: colors.slate600,
    lineHeight: 20
  },
  bubbleRow: {
    flexDirection: 'row',
    justifyContent: 'flex-start',
    marginBottom: 10
  },
  bubbleRowUser: {
    justifyContent: 'flex-end'
  },
  bubble: {
    maxWidth: '84%',
    borderRadius: 18,
    padding: 14
  },
  assistantBubble: {
    backgroundColor: '#fffaf1',
    borderWidth: 1,
    borderColor: colors.amber100,
    borderBottomLeftRadius: 4
  },
  userBubble: {
    backgroundColor: colors.amber800,
    borderBottomRightRadius: 4
  },
  bubbleText: {
    color: colors.slate900,
    lineHeight: 22
  },
  userBubbleText: {
    color: colors.white
  },
  inputWrap: {
    flexDirection: 'row',
    gap: 8,
    alignItems: 'flex-end',
    borderWidth: 1,
    borderColor: colors.slate200,
    backgroundColor: colors.white,
    borderRadius: radius.lg,
    padding: 8
  },
  input: {
    flex: 1,
    minHeight: 44,
    maxHeight: 120,
    color: colors.slate900,
    fontSize: 15,
    paddingHorizontal: 8,
    paddingVertical: 8
  },
  sendButton: {
    width: 44,
    height: 44,
    borderRadius: 14,
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: colors.amber800
  },
  sendButtonDisabled: {
    opacity: 0.45
  }
});