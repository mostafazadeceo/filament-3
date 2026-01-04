'use client';

import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Card, CardDescription, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { login } from '@/lib/auth/auth-service';

export default function LoginPage() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    setLoading(true);
    setError(null);
    try {
      await login({ email, password });
      window.location.href = '/';
    } catch (err) {
      setError((err as { message?: string }).message ?? 'ورود ناموفق بود.');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex min-h-screen items-center justify-center px-6">
      <Card className="w-full max-w-md">
        <CardTitle>ورود به هاب</CardTitle>
        <CardDescription>حساب کاربری خود را برای ادامه وارد کنید.</CardDescription>
        <form className="mt-6 space-y-4" onSubmit={handleSubmit}>
          <Input
            type="email"
            placeholder="ایمیل"
            value={email}
            onChange={(event) => setEmail(event.target.value)}
            required
          />
          <Input
            type="password"
            placeholder="رمز عبور"
            value={password}
            onChange={(event) => setPassword(event.target.value)}
            required
          />
          {error && <p className="text-sm text-red-500">{error}</p>}
          <Button className="w-full" disabled={loading}>
            {loading ? 'در حال ورود...' : 'ورود'}
          </Button>
        </form>
      </Card>
    </div>
  );
}
