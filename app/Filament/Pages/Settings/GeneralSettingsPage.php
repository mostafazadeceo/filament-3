<?php

namespace App\Filament\Pages\Settings;

use App\Settings\GeneralSettings;
use Filamat\IamSuite\Support\MegaSuperAdmin;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Facades\Storage;

class GeneralSettingsPage extends SettingsPage
{
    protected static string $settings = GeneralSettings::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'تنظیمات کلی مگا سوپرادمین';

    protected static ?string $title = 'تنظیمات کلی';

    protected static string|\UnitEnum|null $navigationGroup = 'مگا سوپر ادمین';

    protected static ?int $navigationSort = 30;

    protected static ?string $slug = 'settings/general';

    public static function canAccess(): bool
    {
        return MegaSuperAdmin::check(auth()->user()) && parent::canAccess();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return MegaSuperAdmin::check(auth()->user());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('ورود پنل (Auth UI Enhancer)')
                    ->schema([
                        Toggle::make('enable_auth_ui_enhancer')
                            ->label('فعال‌سازی قالب ورود')
                            ->helperText('اگر فعال باشد، صفحه ورود پنل دو بخشی و زیباتر نمایش داده می‌شود.'),
                        FileUpload::make('auth_ui_empty_panel_background_image_path')
                            ->label('تصویر پس‌زمینه بخش خالی')
                            ->disk('public')
                            ->directory('auth-ui')
                            ->visibility('public')
                            ->image()
                            ->imagePreviewHeight('120')
                            ->helperText('اختیاری. در صورت بارگذاری، تصویر سمت خالی نمایش داده می‌شود.')
                            ->columnSpanFull(),
                        TextInput::make('auth_ui_empty_panel_background_image_opacity')
                            ->label('شفافیت تصویر پس‌زمینه')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('%')
                            ->helperText('عدد بین 0 تا 100.')
                            ->required(),
                        Select::make('auth_ui_form_panel_position')
                            ->label('جایگاه فرم در دسکتاپ')
                            ->options([
                                'right' => 'راست',
                                'left' => 'چپ',
                            ])
                            ->required(),
                        Select::make('auth_ui_mobile_form_panel_position')
                            ->label('جایگاه فرم در موبایل')
                            ->options([
                                'top' => 'بالا',
                                'bottom' => 'پایین',
                            ])
                            ->required(),
                        TextInput::make('auth_ui_form_panel_width')
                            ->label('عرض فرم در دسکتاپ')
                            ->placeholder('50%')
                            ->helperText('نمونه: 50% یا 28rem یا 520px')
                            ->rules(['regex:/^\d+(\.\d+)?(rem|%|px|em|vw|vh|pt)$/'])
                            ->maxLength(20)
                            ->required(),
                        Toggle::make('auth_ui_show_empty_panel_on_mobile')
                            ->label('نمایش بخش خالی در موبایل')
                            ->helperText('اگر خاموش شود، در موبایل فقط فرم نمایش داده می‌شود.'),
                    ])
                    ->columns(2),
                Section::make('کارت معرفی پنل')
                    ->schema([
                        Toggle::make('panel_info_widget_enabled')
                            ->label('نمایش کارت معرفی')
                            ->helperText('نمایش کارت معرفی در داشبورد پنل.')
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('panel_info_title')
                            ->label('عنوان / متن برند')
                            ->placeholder('Haida Panel')
                            ->helperText('اگر لوگو بارگذاری نشود، این متن به‌جای لوگو نمایش داده می‌شود.')
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled'))
                            ->maxLength(120),
                        FileUpload::make('panel_info_logo_path')
                            ->label('لوگو / تصویر کارت')
                            ->disk('public')
                            ->directory('branding')
                            ->visibility('public')
                            ->image()
                            ->imagePreviewHeight('120')
                            ->helperText('اگر لوگو بارگذاری شود، به‌جای متن برند نمایش داده می‌شود.')
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled'))
                            ->columnSpanFull(),
                        TextInput::make('panel_info_logo_link')
                            ->label('لینک لوگو')
                            ->placeholder('https://example.com')
                            ->helperText('با کلیک روی لوگو به این لینک هدایت می‌شود.')
                            ->url()
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled'))
                            ->maxLength(2048),
                        Toggle::make('panel_info_show_version')
                            ->label('نمایش نسخه')
                            ->helperText('نمایش نسخه کنار لوگو.')
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled')),
                        TextInput::make('panel_info_version_text')
                            ->label('متن نسخه سفارشی')
                            ->placeholder('v4.3.1')
                            ->helperText('اگر خالی باشد، نسخه فیلامنت نمایش داده می‌شود.')
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled')),
                        Toggle::make('panel_info_first_link_enabled')
                            ->label('نمایش لینک اول')
                            ->live()
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled')),
                        TextInput::make('panel_info_first_link_label')
                            ->label('متن لینک اول')
                            ->placeholder('مستندات')
                            ->required(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled') && (bool) $get('panel_info_first_link_enabled'))
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled') && (bool) $get('panel_info_first_link_enabled'))
                            ->maxLength(120),
                        TextInput::make('panel_info_first_link_url')
                            ->label('لینک اول')
                            ->placeholder('https://example.com')
                            ->url()
                            ->required(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled') && (bool) $get('panel_info_first_link_enabled'))
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled') && (bool) $get('panel_info_first_link_enabled'))
                            ->maxLength(2048),
                        Toggle::make('panel_info_second_link_enabled')
                            ->label('نمایش لینک دوم')
                            ->live()
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled')),
                        TextInput::make('panel_info_second_link_label')
                            ->label('متن لینک دوم')
                            ->placeholder('گیت‌هاب')
                            ->required(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled') && (bool) $get('panel_info_second_link_enabled'))
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled') && (bool) $get('panel_info_second_link_enabled'))
                            ->maxLength(120),
                        TextInput::make('panel_info_second_link_url')
                            ->label('لینک دوم')
                            ->placeholder('https://example.com')
                            ->url()
                            ->required(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled') && (bool) $get('panel_info_second_link_enabled'))
                            ->visible(fn (Get $get): bool => (bool) $get('panel_info_widget_enabled') && (bool) $get('panel_info_second_link_enabled'))
                            ->maxLength(2048),
                    ])
                    ->columns(2),
                Section::make('تاریخ و تقویم')
                    ->schema([
                        Select::make('calendar_display_mode')
                            ->label('تقویم پیش‌فرض نمایش در پنل')
                            ->options([
                                'jalali' => 'شمسی (جلالی)',
                                'gregorian' => 'میلادی',
                                'hijri' => 'قمری (هجری)',
                            ])
                            ->helperText('تمام تاریخ‌ها در جدول‌ها و نمایش‌ها بر اساس این تقویم نمایش داده می‌شوند.')
                            ->required(),
                        Toggle::make('topbar_date_enabled')
                            ->label('نمایش تاریخ در نوار بالا')
                            ->helperText('تاریخ در نوار بالای پنل نمایش داده می‌شود.')
                            ->live()
                            ->columnSpanFull(),
                        Select::make('topbar_primary_calendar')
                            ->label('تقویم اصلی نوار بالا')
                            ->options([
                                'jalali' => 'شمسی (جلالی)',
                                'gregorian' => 'میلادی',
                                'hijri' => 'قمری (هجری)',
                            ])
                            ->visible(fn (Get $get): bool => (bool) $get('topbar_date_enabled'))
                            ->required(fn (Get $get): bool => (bool) $get('topbar_date_enabled')),
                        Toggle::make('topbar_show_jalali')
                            ->label('نمایش شمسی در راهنما')
                            ->visible(fn (Get $get): bool => (bool) $get('topbar_date_enabled')),
                        Toggle::make('topbar_show_gregorian')
                            ->label('نمایش میلادی در راهنما')
                            ->visible(fn (Get $get): bool => (bool) $get('topbar_date_enabled')),
                        Toggle::make('topbar_show_hijri')
                            ->label('نمایش قمری در راهنما')
                            ->visible(fn (Get $get): bool => (bool) $get('topbar_date_enabled')),
                    ])
                    ->columns(2),
                Section::make('فونت پنل')
                    ->schema([
                        Toggle::make('enable_custom_font')
                            ->label('فعال‌سازی فونت سفارشی')
                            ->helperText('اگر خاموش باشد، فونت پیش‌فرض فیلامنت استفاده می‌شود.')
                            ->live()
                            ->columnSpanFull(),
                        TextInput::make('font_family')
                            ->label('نام فونت')
                            ->placeholder('Vazirmatn')
                            ->helperText('نام فونتی که می‌خواهید در پنل نمایش داده شود.')
                            ->required(fn (Get $get): bool => (bool) $get('enable_custom_font'))
                            ->visible(fn (Get $get): bool => (bool) $get('enable_custom_font')),
                        Select::make('font_source')
                            ->label('منبع فونت')
                            ->options([
                                'bunny' => 'کتابخانه Bunny (آنلاین)',
                                'url' => 'آدرس CSS فونت',
                                'upload_css' => 'آپلود فایل CSS فونت',
                                'upload_file' => 'آپلود فایل فونت',
                            ])
                            ->live()
                            ->required(fn (Get $get): bool => (bool) $get('enable_custom_font'))
                            ->visible(fn (Get $get): bool => (bool) $get('enable_custom_font')),
                        TextInput::make('font_url')
                            ->label('آدرس CSS فونت')
                            ->helperText('لینک فایل CSS شامل @font-face. نمونه: https://example.com/fonts.css')
                            ->required(fn (Get $get): bool => $get('enable_custom_font') && $get('font_source') === 'url')
                            ->visible(fn (Get $get): bool => $get('enable_custom_font') && $get('font_source') === 'url')
                            ->maxLength(2048),
                        FileUpload::make('font_upload_css_path')
                            ->label('آپلود فایل CSS فونت')
                            ->disk('public')
                            ->directory('fonts')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->acceptedFileTypes(['text/css', 'text/plain'])
                            ->helperText('فایل CSS شامل @font-face. اگر فایل‌های فونت را جداگانه آپلود می‌کنید نام‌ها را ثابت نگه دارید.')
                            ->required(fn (Get $get): bool => $get('enable_custom_font') && $get('font_source') === 'upload_css')
                            ->visible(fn (Get $get): bool => $get('enable_custom_font') && $get('font_source') === 'upload_css'),
                        FileUpload::make('font_upload_file_path')
                            ->label('آپلود فایل فونت')
                            ->disk('public')
                            ->directory('fonts')
                            ->visibility('public')
                            ->preserveFilenames()
                            ->acceptedFileTypes([
                                'font/woff2',
                                'application/font-woff2',
                                'application/x-font-woff2',
                                'application/woff2',
                                'application/x-woff2',
                                'font/woff',
                                'application/font-woff',
                                'application/x-font-woff',
                                'application/woff',
                                'font/ttf',
                                'application/x-font-ttf',
                                'font/otf',
                                'application/x-font-otf',
                                'application/octet-stream',
                            ])
                            ->helperText('فرمت‌های پیشنهادی: woff2, woff, ttf, otf')
                            ->required(fn (Get $get): bool => $get('enable_custom_font') && $get('font_source') === 'upload_file')
                            ->visible(fn (Get $get): bool => $get('enable_custom_font') && $get('font_source') === 'upload_file'),
                        TextInput::make('font_upload_weight')
                            ->label('وزن فونت')
                            ->numeric()
                            ->minValue(100)
                            ->maxValue(900)
                            ->step(100)
                            ->helperText('عدد بین 100 تا 900. پیش‌فرض 400.')
                            ->visible(fn (Get $get): bool => $get('enable_custom_font') && $get('font_source') === 'upload_file'),
                        Select::make('font_upload_style')
                            ->label('سبک فونت')
                            ->options([
                                'normal' => 'معمولی',
                                'italic' => 'ایتالیک',
                            ])
                            ->visible(fn (Get $get): bool => $get('enable_custom_font') && $get('font_source') === 'upload_file'),
                    ])
                    ->columns(2),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = parent::mutateFormDataBeforeSave($data);

        if (! ($data['enable_custom_font'] ?? false)) {
            return $data;
        }

        if (($data['font_source'] ?? null) !== 'upload_file') {
            return $data;
        }

        $fontFilePath = $data['font_upload_file_path'] ?? null;
        if (! filled($fontFilePath)) {
            $data['font_upload_css_path'] = null;
            return $data;
        }

        $family = (string) ($data['font_family'] ?? 'Custom Font');
        $weight = (int) ($data['font_upload_weight'] ?? 400);
        $weight = min(max($weight, 100), 900);
        $style = $data['font_upload_style'] ?? 'normal';
        if (! in_array($style, ['normal', 'italic'], true)) {
            $style = 'normal';
        }

        $extension = strtolower(pathinfo($fontFilePath, PATHINFO_EXTENSION));
        $format = match ($extension) {
            'woff2' => 'woff2',
            'woff' => 'woff',
            'ttf' => 'truetype',
            'otf' => 'opentype',
            default => 'woff2',
        };

        $fontUrl = '/storage/' . ltrim($fontFilePath, '/');
        $css = "@font-face {\n"
            . "    font-family: '{$family}';\n"
            . "    src: url('{$fontUrl}') format('{$format}');\n"
            . "    font-weight: {$weight};\n"
            . "    font-style: {$style};\n"
            . "    font-display: swap;\n"
            . "}\n";

        $cssPath = 'fonts/filament-custom-font.css';
        Storage::disk('public')->put($cssPath, $css);
        $data['font_upload_css_path'] = $cssPath;

        return $data;
    }
}
