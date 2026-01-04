'use client';

import { useQuery } from '@tanstack/react-query';
import { fetchCapabilities } from '@/lib/permissions/capabilities';

export function useCapabilities() {
  return useQuery({
    queryKey: ['capabilities'],
    queryFn: fetchCapabilities,
    staleTime: 60_000
  });
}
