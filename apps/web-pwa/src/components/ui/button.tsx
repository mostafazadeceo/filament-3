import type React from 'react';
import { cn } from '@/lib/utils';

type ButtonProps = React.ButtonHTMLAttributes<HTMLButtonElement> & {
  variant?: 'primary' | 'secondary' | 'ghost';
};

export function Button({ className, variant = 'primary', ...props }: ButtonProps) {
  return (
    <button
      className={cn(
        'inline-flex items-center justify-center rounded-xl px-4 py-2 text-sm font-medium transition',
        variant === 'primary' && 'bg-accent-600 text-white shadow-sm hover:bg-accent-500',
        variant === 'secondary' &&
          'bg-base-100 text-base-900 border border-base-100/80 hover:border-base-200',
        variant === 'ghost' && 'bg-transparent text-base-700 hover:bg-base-50',
        className
      )}
      {...props}
    />
  );
}
