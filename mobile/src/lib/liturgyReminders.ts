import { Platform } from 'react-native';
import * as Notifications from 'expo-notifications';
import { liturgyReminderMessages } from '../content/liturgyReminderMessages';
import { colors } from '../theme/colors';

const REMINDER_KIND = 'daily-liturgy-reminder';
const REMINDER_IDENTIFIER_PREFIX = 'liturgy-reminder-';
const REMINDER_CHANNEL_ID = 'daily-liturgy-reminders';
const REMINDER_HORIZON_DAYS = Platform.OS === 'ios' ? 60 : 180;
const REMINDER_TOP_UP_THRESHOLD = Platform.OS === 'ios' ? 18 : 45;

type ReminderData = {
  kind?: string;
  hour?: number;
  minute?: number;
  route?: string;
};

export type LiturgyReminderState = {
  enabled: boolean;
  hour: number;
  minute: number;
  pendingCount: number;
};

Notifications.setNotificationHandler({
  handleNotification: async () => ({
    shouldShowBanner: true,
    shouldShowList: true,
    shouldPlaySound: true,
    shouldSetBadge: false,
    priority: Notifications.AndroidNotificationPriority.HIGH
  })
});

export async function initializeLiturgyReminderChannelAsync() {
  if (Platform.OS !== 'android') {
    return;
  }

  await Notifications.setNotificationChannelAsync(REMINDER_CHANNEL_ID, {
    name: 'Liturgia diaria',
    importance: Notifications.AndroidImportance.HIGH,
    vibrationPattern: [0, 250, 130, 250],
    lightColor: colors.amber800,
    lockscreenVisibility: Notifications.AndroidNotificationVisibility.PUBLIC,
    sound: 'default'
  });
}

export async function ensureLiturgyReminderPermissionsAsync() {
  const current = await Notifications.getPermissionsAsync();
  if (current.granted) {
    return true;
  }

  const requested = await Notifications.requestPermissionsAsync({
    ios: {
      allowAlert: true,
      allowBadge: false,
      allowSound: true
    }
  });

  return requested.granted;
}

export async function getLiturgyReminderStateAsync(): Promise<LiturgyReminderState> {
  const requests = await getScheduledLiturgyReminderRequestsAsync();
  const first = requests[0];
  const data = (first?.content.data ?? {}) as ReminderData;

  return {
    enabled: requests.length > 0,
    hour: typeof data.hour === 'number' ? data.hour : 7,
    minute: typeof data.minute === 'number' ? data.minute : 0,
    pendingCount: requests.length
  };
}

export async function scheduleLiturgyRemindersAsync(hour: number, minute: number) {
  await initializeLiturgyReminderChannelAsync();
  await cancelLiturgyRemindersAsync();

  const firstDate = getFirstReminderDate(hour, minute);

  for (let dayOffset = 0; dayOffset < REMINDER_HORIZON_DAYS; dayOffset += 1) {
    const triggerDate = new Date(firstDate);
    triggerDate.setDate(firstDate.getDate() + dayOffset);

    const reminderIndex = dayOffset % liturgyReminderMessages.length;
    const identifier = `${REMINDER_IDENTIFIER_PREFIX}${toDateKey(triggerDate)}`;

    await Notifications.scheduleNotificationAsync({
      identifier,
      content: {
        title: 'Liturgia do dia',
        body: liturgyReminderMessages[reminderIndex],
        sound: true,
        color: colors.amber800,
        priority: Notifications.AndroidNotificationPriority.HIGH,
        data: {
          kind: REMINDER_KIND,
          hour,
          minute,
          route: 'liturgy'
        }
      },
      trigger: {
        type: Notifications.SchedulableTriggerInputTypes.DATE,
        date: triggerDate,
        channelId: REMINDER_CHANNEL_ID
      }
    });
  }

  return getLiturgyReminderStateAsync();
}

export async function disableLiturgyRemindersAsync() {
  await cancelLiturgyRemindersAsync();
  return getLiturgyReminderStateAsync();
}

export async function topUpLiturgyRemindersIfNeededAsync() {
  const state = await getLiturgyReminderStateAsync();
  if (!state.enabled || state.pendingCount > REMINDER_TOP_UP_THRESHOLD) {
    return state;
  }

  return scheduleLiturgyRemindersAsync(state.hour, state.minute);
}

export function formatReminderTime(hour: number, minute: number) {
  return `${String(hour).padStart(2, '0')}:${String(minute).padStart(2, '0')}`;
}

async function cancelLiturgyRemindersAsync() {
  const requests = await getScheduledLiturgyReminderRequestsAsync();
  await Promise.all(requests.map((request) => Notifications.cancelScheduledNotificationAsync(request.identifier)));
}

async function getScheduledLiturgyReminderRequestsAsync() {
  const requests = await Notifications.getAllScheduledNotificationsAsync();

  return requests
    .filter((request) => {
      const data = (request.content.data ?? {}) as ReminderData;
      return data.kind === REMINDER_KIND || request.identifier.startsWith(REMINDER_IDENTIFIER_PREFIX);
    })
    .sort((left, right) => left.identifier.localeCompare(right.identifier));
}

function getFirstReminderDate(hour: number, minute: number) {
  const date = new Date();
  date.setHours(hour, minute, 0, 0);

  if (date.getTime() <= Date.now()) {
    date.setDate(date.getDate() + 1);
  }

  return date;
}

function toDateKey(value: Date) {
  const year = value.getFullYear();
  const month = String(value.getMonth() + 1).padStart(2, '0');
  const day = String(value.getDate()).padStart(2, '0');
  return `${year}-${month}-${day}`;
}
