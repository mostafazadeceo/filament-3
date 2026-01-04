export function formatJalali(value: string | number | Date) {
  const date = value instanceof Date ? value : new Date(value);
  return new Intl.DateTimeFormat('fa-IR-u-ca-persian', {
    dateStyle: 'medium',
    timeStyle: 'short'
  }).format(date);
}
