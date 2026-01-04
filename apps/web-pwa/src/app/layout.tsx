import type { Metadata } from 'next';
import '@fontsource/vazirmatn/arabic.css';
import './globals.css';
import { Providers } from './providers';

export const metadata: Metadata = {
  title: 'هاب حایدا',
  description: 'کلاینت عملیاتی چندماژوله برای کسب‌وکارهای چنداجاره‌ای',
  manifest: '/manifest.json',
  icons: {
    icon: '/icon.svg',
    apple: '/icon.svg'
  }
};

export default function RootLayout({
  children
}: {
  children: React.ReactNode;
}) {
  return (
    <html lang="fa" dir="rtl">
      <body>
        <Providers>{children}</Providers>
      </body>
    </html>
  );
}
