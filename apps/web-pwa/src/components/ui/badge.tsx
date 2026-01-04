import type React from 'react';
import { cn } from '@/lib/utils';

type BadgeProps = React.HTMLAttributes<HTMLSpanElement> & {
  tone?: 'teal' | 'amber' | 'neutral';
};

export function Badge({ className, tone = 'neutral', ...props }: BadgeProps) {
  return (
    <span
      className={cn(
        'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
        tone === 'teal' && 'bg-teal-200/60 text-teal-600',
        tone === 'amber' && 'bg-accent-200/70 text-accent-600',
        tone === 'neutral' && 'bg-base-100 text-base-600',
        className
      )}
      {...props}
    />
  );
}
