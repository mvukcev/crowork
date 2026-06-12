@php
    $isAdminPath = request()->is('admin') || request()->is('admin/*');
@endphp

@if (! $isAdminPath)
    <style>
        .cw-bug-banner-trigger {
            position: fixed;
            right: -44px;
            top: 50%;
            transform: translateY(-50%) rotate(-90deg);
            transform-origin: center;
            z-index: 45;
            border-radius: 10px 10px 0 0;
            border: 1px solid rgba(15, 23, 42, 0.16);
            background: rgba(255, 255, 255, 0.96);
            color: #0c2340;
            padding: 8px 12px;
            min-width: 120px;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.01em;
            box-shadow: 0 6px 20px rgba(12, 35, 64, 0.12);
            backdrop-filter: blur(6px);
        }

        .cw-bug-banner-trigger:hover {
            background: #fff;
            color: #fe5000;
        }

        .cw-bug-panel {
            position: fixed;
            right: 12px;
            bottom: 12px;
            z-index: 50;
            width: min(430px, calc(100vw - 24px));
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
            display: none;
        }

        .cw-bug-panel[data-open="1"] {
            display: block;
        }

        @media (max-width: 768px) {
            .cw-bug-banner-trigger {
                top: auto;
                right: 14px;
                bottom: 16px;
                transform: none;
                border-radius: 999px;
                border-right: 1px solid rgba(15, 23, 42, 0.16);
                padding: 11px 14px;
                min-width: unset;
            }
        }
    </style>

    <button type="button" class="cw-bug-banner-trigger" data-cw-bug-open>
        {{ __('ui.bug_report.trigger') }}
    </button>

    <section class="cw-bug-panel" data-cw-bug-panel>
        <form method="POST" action="{{ route('bugs.report.store') }}" enctype="multipart/form-data" class="p-4 md:p-5 space-y-3">
            @csrf
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-sm md:text-base font-semibold text-slate-900">{{ __('ui.bug_report.title') }}</h3>
                <button type="button" class="cw-button-secondary !px-2 !py-1 text-xs" data-cw-bug-close>{{ __('ui.bug_report.close') }}</button>
            </div>

            <p class="text-xs text-slate-600">
                {{ __('ui.bug_report.description') }}
            </p>

            <input type="hidden" name="page_uri" value="{{ url()->current() }}" data-cw-bug-uri>

            <div>
                <label for="cw-bug-description" class="cw-label">{{ __('ui.bug_report.problem_label') }}</label>
                <textarea id="cw-bug-description" name="description" rows="4" class="cw-field w-full" maxlength="5000" required placeholder="{{ __('ui.bug_report.problem_placeholder') }}"></textarea>
            </div>

            <div>
                <label for="cw-bug-screenshot" class="cw-label">{{ __('ui.bug_report.screenshot_label') }}</label>
                <input id="cw-bug-screenshot" name="screenshot" type="file" accept="image/png,image/jpeg,image/webp" class="cw-field w-full" />
                <p class="text-xs text-slate-500 mt-1">{{ __('ui.bug_report.screenshot_help') }}</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="cw-button-primary">{{ __('ui.bug_report.submit') }}</button>
            </div>
        </form>
    </section>

    <script>
        (function () {
            const openBtn = document.querySelector('[data-cw-bug-open]');
            const panel = document.querySelector('[data-cw-bug-panel]');
            const closeBtn = document.querySelector('[data-cw-bug-close]');
            const uriInput = document.querySelector('[data-cw-bug-uri]');

            if (!openBtn || !panel) {
                return;
            }

            if (uriInput) {
                uriInput.value = window.location.href;
            }

            openBtn.addEventListener('click', function () {
                panel.setAttribute('data-open', '1');
            });

            if (closeBtn) {
                closeBtn.addEventListener('click', function () {
                    panel.removeAttribute('data-open');
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    panel.removeAttribute('data-open');
                }
            });
        })();
    </script>
@endif
