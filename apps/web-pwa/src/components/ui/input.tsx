import type React from 'react';
import { cn } from '@/lib/utils';

type InputProps = React.InputHTMLAttributes<HTMLInputElement>;

export function Input({ className, ...props }: InputProps) {
  return (
    <input
      className={cn(
        'w-full rounded-xl border border-base-100 bg-white/90 px-3 py-2 text-sm text-base-900 placeholder:text-base-500 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-200',
        className
      )}
      {...props}
    />
  );
}
