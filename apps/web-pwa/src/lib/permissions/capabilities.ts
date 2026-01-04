import { apiFetch } from '@/lib/api-client/client';

export type Capability = {
  key: string;
  label: string;
};

export type CapabilityResponse = {
  permissions: Capability[];
  navigation: Record<string, string>;
  feature_flags: Record<string, boolean>;
};

export async function fetchCapabilities() {
  return apiFetch<CapabilityResponse>('/api/v1/app/capabilities');
}
