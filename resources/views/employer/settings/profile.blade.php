@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen dark:bg-gray-950">
    <!-- Header -->
    <div class="cw-surface-header border-b border-neutral-200 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h1 class="cw-heading-1">{{ __('employer.settings.title') }}</h1>
                <a href="{{ route('employer.dashboard') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                    {{ __('employer.settings.back_to_dashboard') }}
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2">
                @if($errors->any())
                    <div class="cw-surface border border-red-200 bg-red-50 rounded-lg p-4 mb-6 dark:border-red-900/60 dark:bg-red-950/40">
                        <p class="font-medium text-red-900 mb-2">{{ __('employer.settings.fix_errors') }}</p>
                        <ul class="list-disc list-inside space-y-1 text-sm text-red-800">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('success'))
                    <div class="cw-surface border border-emerald-200 bg-emerald-50 rounded-lg p-4 mb-6 dark:border-emerald-900/60 dark:bg-emerald-950/40">
                        <p class="text-emerald-900 font-medium">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('employer.settings.profile.update') }}" enctype="multipart/form-data" class="space-y-6" data-cw-track-submit="employer_branding_update">
                    @csrf
                    @method('PATCH')

                    <div class="border border-neutral-200 rounded-xl p-5 bg-white/70 backdrop-blur-sm dark:border-gray-800 dark:bg-gray-900/70">
                        <h3 class="cw-heading-3 mb-4">{{ __('employer.settings.branding') }}</h3>
                        <p class="text-sm text-neutral-600 mb-4 dark:text-gray-400">{{ __('employer.settings.branding_intro') }}</p>

                        <div>
                            <label class="cw-label">{{ __('employer.settings.company_display_name') }}</label>
                            <input type="text"
                                   name="company_display_name"
                                   class="cw-field"
                                   value="{{ old('company_display_name', $employer->company_display_name) }}"
                                placeholder="{{ __('employer.settings.company_display_name_placeholder') }}">
                            <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.company_display_name_help') }}</p>
                        </div>

                        <div class="mt-5">
                            <label class="cw-label">{{ __('employer.settings.company_logo') }}</label>
                            <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" class="cw-field" data-crop-file-input="logo">
                            <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.company_logo_help') }}</p>
                            <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.crop_help') }}</p>

                            <input type="hidden" name="logo_crop_zoom" value="1" data-crop-zoom-input="logo">
                            <input type="hidden" name="logo_crop_x" value="0" data-crop-x-input="logo">
                            <input type="hidden" name="logo_crop_y" value="0" data-crop-y-input="logo">

                            <div class="mt-3 flex items-center gap-4">
                                <div class="h-20 w-20 rounded-full border border-slate-200 overflow-hidden bg-slate-50 shadow-sm dark:border-gray-700 dark:bg-gray-800" data-crop-preview="logo" data-crop-aspect="1">
                                    @if($employer->logo_path)
                                        <img src="{{ asset('storage/' . $employer->logo_path) }}" alt="{{ $employer->company_name }} logo" class="h-full w-full object-cover" data-crop-preview-image="logo" data-cw-logo-image data-cw-fallback-text="{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($employer->company_display_name ?: $employer->company_name, 0, 2)) }}" data-cw-fallback-label="{{ $employer->company_name }}" onerror="this.onerror=null;this.src='{{ asset('assets/placeholders/shared/company-logo-placeholder-400x400.jpg') }}';">
                                    @else
                                        <div class="h-full w-full grid place-items-center text-sm font-semibold text-slate-500" data-crop-preview-fallback="logo">
                                            {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($employer->company_display_name ?: $employer->company_name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="text-xs text-neutral-600 dark:text-gray-400">
                                    <p>{{ __('employer.settings.logo_preview_help') }}</p>
                                </div>
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3" data-crop-controls="logo">
                                <label class="text-xs text-neutral-600 dark:text-gray-400">{{ __('employer.settings.zoom') }}
                                    <input type="range" min="1" max="3" step="0.05" value="1" class="w-full" data-crop-zoom="logo">
                                </label>
                                <label class="text-xs text-neutral-600 dark:text-gray-400">{{ __('employer.settings.horizontal_position') }}
                                    <input type="range" min="-100" max="100" step="1" value="0" class="w-full" data-crop-x="logo">
                                </label>
                                <label class="text-xs text-neutral-600 dark:text-gray-400">{{ __('employer.settings.vertical_position') }}
                                    <input type="range" min="-100" max="100" step="1" value="0" class="w-full" data-crop-y="logo">
                                </label>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="cw-label">{{ __('employer.settings.brand_color') }}</label>
                                <div class="flex items-center gap-2">
                                    <input type="color" value="{{ old('brand_color', $employer->brand_color ?? '#0F274D') }}" class="h-10 w-14 rounded border border-slate-300 p-1" data-brand-color-picker>
                                    <input type="text" name="brand_color" value="{{ old('brand_color', $employer->brand_color ?? '#0F274D') }}" class="cw-field" pattern="^#[A-Fa-f0-9]{6}$" placeholder="#0F274D" data-brand-color-text>
                                </div>
                                <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.brand_color_help') }}</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 p-3 bg-white/80 dark:border-gray-700 dark:bg-gray-800/60" data-brand-preview-card>
                                <p class="text-xs text-slate-500 mb-2">{{ __('employer.settings.brand_preview') }}</p>
                                <div class="h-12 rounded-md" data-brand-preview-fill style="background: linear-gradient(135deg, {{ old('brand_color', $employer->brand_color ?? '#0F274D') }}22, {{ old('brand_color', $employer->brand_color ?? '#0F274D') }}55);"></div>
                            </div>
                        </div>

                        <div class="mt-5">
                            <label class="cw-label">{{ __('employer.settings.cover_image') }}</label>
                            <input type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="cw-field" data-crop-file-input="cover">
                            <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.cover_image_help') }}</p>
                            <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.crop_help') }}</p>

                            <input type="hidden" name="cover_crop_zoom" value="1" data-crop-zoom-input="cover">
                            <input type="hidden" name="cover_crop_x" value="0" data-crop-x-input="cover">
                            <input type="hidden" name="cover_crop_y" value="0" data-crop-y-input="cover">

                            <div class="mt-3 aspect-[13/5] overflow-hidden rounded-xl border border-slate-200 bg-slate-100 dark:border-gray-700 dark:bg-gray-800" data-crop-preview="cover" data-crop-aspect="2.6">
                                @if($employer->cover_image_path)
                                    <img src="{{ asset('storage/' . $employer->cover_image_path) }}" alt="{{ $employer->company_name }} cover" class="h-full w-full object-cover" data-crop-preview-image="cover">
                                @else
                                    <div class="h-full w-full grid place-items-center text-sm text-slate-500" data-crop-preview-fallback="cover">{{ __('employer.settings.cover_placeholder') }}</div>
                                @endif
                            </div>

                            <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-3" data-crop-controls="cover">
                                <label class="text-xs text-neutral-600 dark:text-gray-400">{{ __('employer.settings.zoom') }}
                                    <input type="range" min="1" max="3" step="0.05" value="1" class="w-full" data-crop-zoom="cover">
                                </label>
                                <label class="text-xs text-neutral-600 dark:text-gray-400">{{ __('employer.settings.horizontal_position') }}
                                    <input type="range" min="-100" max="100" step="1" value="0" class="w-full" data-crop-x="cover">
                                </label>
                                <label class="text-xs text-neutral-600 dark:text-gray-400">{{ __('employer.settings.vertical_position') }}
                                    <input type="range" min="-100" max="100" step="1" value="0" class="w-full" data-crop-y="cover">
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-5">
                            <div>
                                <label class="cw-label">{{ __('employer.settings.contact_email') }}</label>
                                <input type="email"
                                       name="contact_email"
                                       class="cw-field"
                                       value="{{ old('contact_email', $employer->contact_email) }}"
                                        autocomplete="email"
                                        placeholder="{{ __('employer.settings.contact_email_placeholder') }}">
                            </div>

                            <div>
                                    <label class="cw-label">{{ __('employer.settings.contact_phone') }}</label>
                                <input type="text"
                                       name="contact_phone"
                                       class="cw-field"
                                       value="{{ old('contact_phone', $employer->contact_phone) }}"
                                        autocomplete="tel"
                                        inputmode="tel"
                                       placeholder="{{ __('employer.settings.contact_phone_placeholder') }}">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="cw-label">{{ __('employer.settings.company_address') }}</label>
                            <input type="text"
                                   name="company_address"
                                   class="cw-field"
                                   value="{{ old('company_address', $employer->company_address) }}"
                                   placeholder="{{ __('employer.settings.company_address_placeholder') }}">
                        </div>
                    </div>

                    <div>
                        <label class="cw-label">{{ __('employer.settings.company_name') }}</label>
                        <input type="text" 
                               name="company_name" 
                               class="cw-field"
                               value="{{ old('company_name', $employer->company_name) }}"
                               required>
                        <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.company_name_help') }}</p>
                    </div>

                    <!-- City -->
                    <div>
                        <label class="cw-label">{{ __('employer.settings.city') }}</label>
                        <input type="text" 
                               name="city" 
                               class="cw-field"
                               value="{{ old('city', $employer->city) }}"
                               placeholder="{{ __('employer.settings.city_placeholder') }}">
                           <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.city_help') }}</p>
                    </div>

                    <div>
                           <label class="cw-label">{{ __('employer.settings.country') }}</label>
                        <input type="text"
                               name="country"
                               class="cw-field"
                               value="{{ old('country', $employer->country) }}"
                               placeholder="{{ __('employer.settings.country_placeholder') }}">
                           <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.country_help') }}</p>
                    </div>

                    <!-- Industry -->
                    <div>
                        <label class="cw-label">{{ __('employer.settings.industry') }}</label>
                        <select name="industry" class="cw-field">
                            <option value="">{{ __('employer.settings.select_industry') }}</option>
                            <option value="Technology" @selected(old('industry', $employer->industry) === 'Technology')>{{ __('employer.settings.industry_options.technology') }}</option>
                            <option value="Healthcare" @selected(old('industry', $employer->industry) === 'Healthcare')>{{ __('employer.settings.industry_options.healthcare') }}</option>
                            <option value="Finance" @selected(old('industry', $employer->industry) === 'Finance')>{{ __('employer.settings.industry_options.finance') }}</option>
                            <option value="Retail" @selected(old('industry', $employer->industry) === 'Retail')>{{ __('employer.settings.industry_options.retail') }}</option>
                            <option value="Manufacturing" @selected(old('industry', $employer->industry) === 'Manufacturing')>{{ __('employer.settings.industry_options.manufacturing') }}</option>
                            <option value="Education" @selected(old('industry', $employer->industry) === 'Education')>{{ __('employer.settings.industry_options.education') }}</option>
                            <option value="Hospitality" @selected(old('industry', $employer->industry) === 'Hospitality')>{{ __('employer.settings.industry_options.hospitality') }}</option>
                            <option value="Construction" @selected(old('industry', $employer->industry) === 'Construction')>{{ __('employer.settings.industry_options.construction') }}</option>
                            <option value="Transportation" @selected(old('industry', $employer->industry) === 'Transportation')>{{ __('employer.settings.industry_options.transportation') }}</option>
                            <option value="Agriculture" @selected(old('industry', $employer->industry) === 'Agriculture')>{{ __('employer.settings.industry_options.agriculture') }}</option>
                            <option value="Other" @selected(old('industry', $employer->industry) === 'Other')>{{ __('employer.settings.industry_options.other') }}</option>
                        </select>
                        <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.industry_help') }}</p>
                    </div>

                    <!-- Website -->
                    <div>
                        <label class="cw-label">{{ __('employer.settings.website') }}</label>
                        <input type="url" 
                               name="website" 
                               class="cw-field"
                               value="{{ old('website', $employer->website) }}"
                               placeholder="{{ __('employer.settings.website_placeholder') }}">
                           <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.website_help') }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="cw-label">{{ __('employer.settings.company_description') }}</label>
                        <textarea name="description" 
                                  class="cw-field h-32 resize-none"
                                  placeholder="{{ __('employer.settings.company_description_placeholder') }}">{{ old('description', $employer->description) }}</textarea>
                        <p class="text-xs text-neutral-600 mt-1 dark:text-gray-400">{{ __('employer.settings.company_description_help') }}</p>
                    </div>

                    <!-- Support Flags -->
                    <div class="border-t border-neutral-200 pt-6 dark:border-gray-800">
                        <h3 class="cw-heading-3 mb-4">{{ __('employer.settings.support_and_benefits') }}</h3>
                        
                        <div class="space-y-4">
                            <label class="flex items-start gap-3">
                                <input type="checkbox" 
                                       name="relocation_support" 
                                       value="1"
                                       class="mt-1"
                                       @checked(old('relocation_support', $employer->relocation_support))>
                                <div>
                                    <p class="font-medium text-neutral-900 dark:text-gray-100">{{ __('employer.settings.relocation_support') }}</p>
                                    <p class="text-sm text-neutral-600 dark:text-gray-400">{{ __('employer.settings.relocation_support_help') }}</p>
                                </div>
                            </label>

                            <label class="flex items-start gap-3">
                                <input type="checkbox" 
                                       name="accommodation_support" 
                                       value="1"
                                       class="mt-1"
                                       @checked(old('accommodation_support', $employer->accommodation_support))>
                                <div>
                                    <p class="font-medium text-neutral-900 dark:text-gray-100">{{ __('employer.settings.accommodation_support') }}</p>
                                    <p class="text-sm text-neutral-600 dark:text-gray-400">{{ __('employer.settings.accommodation_support_help') }}</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="border-t border-neutral-200 pt-6 flex gap-3 dark:border-gray-800">
                        <button type="submit" class="cw-button-primary">
                            {{ __('employer.settings.save_changes') }}
                        </button>
                        <a href="{{ route('employer.dashboard') }}" class="cw-button-secondary">
                            {{ __('common.cancel') }}
                        </a>
                    </div>
                </form>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Profile Completeness -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 mb-6 dark:border-gray-800 dark:bg-gray-900/60">
                    <h3 class="cw-heading-3 mb-4">{{ __('employer.dashboard.profile_completeness') }}</h3>
                    
                    <div class="relative w-32 h-32 mx-auto mb-4">
                        <svg class="w-full h-full" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                            <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" 
                                class="text-emerald-600" 
                                stroke-dasharray="{{ $readiness * 2.83 }},283"
                                stroke-linecap="round"
                                style="transform: rotate(-90deg); transform-origin: 50% 50%;"
                            />
                            <text x="50" y="55" text-anchor="middle" font-size="20" font-weight="bold" fill="currentColor">
                                {{ $readiness }}%
                            </text>
                        </svg>
                    </div>

                    <p class="text-sm text-center text-neutral-600 mb-4 dark:text-gray-400">
                        @if($readiness === 100)
                            {{ __('employer.settings.readiness.complete') }}
                        @elseif($readiness >= 75)
                            {{ __('employer.settings.readiness.almost') }}
                        @elseif($readiness >= 50)
                            {{ __('employer.settings.readiness.halfway') }}
                        @else
                            {{ __('employer.settings.readiness.start') }}
                        @endif
                    </p>

                    @if($missing)
                        <div class="bg-neutral-50 rounded p-3 dark:bg-gray-800/60">
                            <p class="text-xs font-medium text-neutral-700 mb-2 dark:text-gray-200">{{ __('employer.settings.missing_fields') }}</p>
                            <ul class="space-y-1 text-xs text-neutral-600 dark:text-gray-400">
                                @foreach($missing as $field => $label)
                                    <li>• {{ $label }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <!-- Info Card -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 dark:border-gray-800 dark:bg-gray-900/60">
                    <h3 class="cw-heading-3 mb-3">{{ __('employer.settings.why_complete_profile') }}</h3>
                    <ul class="space-y-2 text-sm text-neutral-700 dark:text-gray-300">
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ __('employer.settings.why_items.trust') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ __('employer.settings.why_items.visibility') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ __('employer.settings.why_items.quality') }}</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>{{ __('employer.settings.why_items.culture') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const initCropUploader = function (key) {
                const fileInput = document.querySelector('[data-crop-file-input="' + key + '"]');
                const preview = document.querySelector('[data-crop-preview="' + key + '"]');
                const zoomRange = document.querySelector('[data-crop-zoom="' + key + '"]');
                const xRange = document.querySelector('[data-crop-x="' + key + '"]');
                const yRange = document.querySelector('[data-crop-y="' + key + '"]');
                const zoomInput = document.querySelector('[data-crop-zoom-input="' + key + '"]');
                const xInput = document.querySelector('[data-crop-x-input="' + key + '"]');
                const yInput = document.querySelector('[data-crop-y-input="' + key + '"]');

                if (!fileInput || !preview || !zoomRange || !xRange || !yRange || !zoomInput || !xInput || !yInput) {
                    return;
                }

                const applyTransform = function () {
                    const img = preview.querySelector('img');
                    if (!img) {
                        return;
                    }

                    const zoom = Number(zoomRange.value || 1);
                    const x = Number(xRange.value || 0);
                    const y = Number(yRange.value || 0);

                    // Keep pan movement inside the frame and disable it when zoom is 1.
                    const panFactor = zoom > 1 ? ((zoom - 1) / zoom) : 0;
                    const translateX = x * panFactor;
                    const translateY = y * panFactor;

                    preview.style.position = 'relative';
                    img.style.position = 'absolute';
                    img.style.inset = '0';
                    img.style.transform = 'translate(' + translateX + '%, ' + translateY + '%) scale(' + zoom + ')';
                    img.style.transformOrigin = 'center center';

                    zoomInput.value = String(zoom);
                    xInput.value = String(x);
                    yInput.value = String(y);
                };

                fileInput.addEventListener('change', function (event) {
                    const file = event.target.files?.[0];
                    if (!file) {
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const existingImage = preview.querySelector('img');
                        const fallback = preview.querySelector('[data-crop-preview-fallback="' + key + '"]');

                        if (existingImage) {
                            existingImage.remove();
                        }
                        if (fallback) {
                            fallback.remove();
                        }

                        const img = document.createElement('img');
                        img.src = String(e.target?.result || '');
                        img.alt = @js(__('employer.settings.logo_preview_alt'));
                        img.className = 'h-full w-full object-cover block';
                        preview.appendChild(img);

                        zoomRange.value = '1';
                        xRange.value = '0';
                        yRange.value = '0';
                        applyTransform();
                    };
                    reader.readAsDataURL(file);
                });

                zoomRange.addEventListener('input', applyTransform);
                xRange.addEventListener('input', applyTransform);
                yRange.addEventListener('input', applyTransform);
            };

            initCropUploader('logo');
            initCropUploader('cover');

            const brandPicker = document.querySelector('[data-brand-color-picker]');
            const brandText = document.querySelector('[data-brand-color-text]');
            const brandPreview = document.querySelector('[data-brand-preview-fill]');

            if (brandPicker && brandText && brandPreview) {
                const syncPreview = function (value) {
                    const normalized = /^#[A-Fa-f0-9]{6}$/.test(value) ? value : '#0F274D';
                    brandPreview.style.background = 'linear-gradient(135deg, ' + normalized + '22, ' + normalized + '55)';
                };

                brandPicker.addEventListener('input', function () {
                    brandText.value = brandPicker.value;
                    syncPreview(brandPicker.value);
                });

                brandText.addEventListener('input', function () {
                    if (/^#[A-Fa-f0-9]{6}$/.test(brandText.value)) {
                        brandPicker.value = brandText.value;
                    }
                    syncPreview(brandText.value);
                });

                syncPreview(brandText.value);
            }
        });
    </script>
@endpush
@endsection
