import type { Config } from 'tailwindcss';

const config: Config = {
  content: [
    './src/app/**/*.{ts,tsx}',
    './src/components/**/*.{ts,tsx}',
    './src/lib/**/*.{ts,tsx}'
  ],
  theme: {
    extend: {
      colors: {
        base: {
          950: '#0b0f1a',
          900: '#101826',
          800: '#1a2433',
          700: '#2b3444',
          600: '#3e4a5e',
          500: '#51627a',
          200: '#cdd7e3',
          100: '#e6edf5',
          50: '#f3f7fb'
        },
        accent: {
          600: '#f97316',
          500: '#fb923c',
          200: '#fed7aa'
        },
        teal: {
          600: '#0ea5a4',
          500: '#14b8a6',
          200: '#99f6e4'
        }
      },
      fontFamily: {
        sans: ['Vazirmatn', 'ui-sans-serif', 'system-ui']
      },
      boxShadow: {
        card: '0 16px 40px -24px rgba(15, 23, 42, 0.45)'
      }
    }
  },
  plugins: []
};

export default config;
