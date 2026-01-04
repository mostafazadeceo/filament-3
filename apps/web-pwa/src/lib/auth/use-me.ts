'use client';

import { useQuery } from '@tanstack/react-query';
import { me } from '@/lib/auth/auth-service';

export function useMe() {
  return useQuery({
    queryKey: ['me'],
    queryFn: me,
    staleTime: 30_000
  });
}
