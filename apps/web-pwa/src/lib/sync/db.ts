import Dexie, { type Table } from 'dexie';

export type OutboxStatus = 'pending' | 'syncing' | 'failed' | 'completed';

export type OutboxItem = {
  id: string;
  module: string;
  action: string;
  payload: unknown;
  status: OutboxStatus;
  retries: number;
  idempotencyKey: string;
  createdAt: string;
  updatedAt: string;
};

export type SyncCursor = {
  module: string;
  cursor: string;
  updatedAt: string;
};

export type PosOrder = {
  id: string;
  status: string;
  total: number;
  payment_status?: string | null;
  updatedAt: string;
  offline: boolean;
};

export type SupportTicket = {
  id: string;
  subject: string;
  status: string;
  priority: string;
  updatedAt: string;
};

export type TaskItem = {
  id: string;
  title: string;
  status: string;
  updatedAt: string;
};

export type AttendanceRecord = {
  id: string;
  status: string;
  clockedAt: string;
  updatedAt: string;
};

export type MeetingItem = {
  id: string;
  title: string;
  scheduledAt?: string | null;
  updatedAt: string;
};

export type CryptoInvoice = {
  id: string;
  status: string;
  amount: number;
  updatedAt: string;
};

export type LoyaltyProfile = {
  id: string;
  name: string;
  tier?: string | null;
  points: number;
  updatedAt: string;
};

class HubAppDb extends Dexie {
  outbox!: Table<OutboxItem, string>;
  cursors!: Table<SyncCursor, string>;
  posOrders!: Table<PosOrder, string>;
  supportTickets!: Table<SupportTicket, string>;
  tasks!: Table<TaskItem, string>;
  attendance!: Table<AttendanceRecord, string>;
  meetings!: Table<MeetingItem, string>;
  cryptoInvoices!: Table<CryptoInvoice, string>;
  loyaltyProfiles!: Table<LoyaltyProfile, string>;

  constructor() {
    super('haidaHubApp');
    this.version(1).stores({
      outbox: 'id, module, status, createdAt',
      cursors: 'module, updatedAt',
      posOrders: 'id, status, updatedAt',
      supportTickets: 'id, status, updatedAt',
      tasks: 'id, status, updatedAt',
      attendance: 'id, status, updatedAt',
      meetings: 'id, updatedAt',
      cryptoInvoices: 'id, status, updatedAt',
      loyaltyProfiles: 'id, updatedAt'
    });
  }
}

export const db = new HubAppDb();
