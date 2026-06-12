import './bootstrap';

import Alpine from 'alpinejs';

const readCookieValue = (name) => {
	const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]+)'));
	return match ? decodeURIComponent(match[1]) : null;
};

const cwTheme = (() => {
	const storageKey = 'cw-theme';
	const legacyStorageKey = 'cw_theme_preference';
	const filamentStorageKey = 'theme';
	const allowed = ['light', 'dark', 'system'];

	const readCookie = () => readCookieValue('cw_theme');

	const writeThemeCookie = (preference) => {
		document.cookie = `cw_theme=${encodeURIComponent(preference)}; path=/; max-age=31536000; samesite=lax`;
	};

	const prefersDark = () => window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

	const normalize = (value) => (allowed.includes(value) ? value : 'system');

	const getPreference = () => {
		let stored = null;
		let legacyStored = null;
		const root = document.documentElement;
		const serverInitializedPreference = root?.dataset?.themePreference || null;

		try {
			stored = localStorage.getItem(storageKey);
			legacyStored = localStorage.getItem(legacyStorageKey);
		} catch (_) {
			stored = null;
			legacyStored = null;
		}

		if (serverInitializedPreference) {
			return normalize(serverInitializedPreference);
		}

		if (stored) {
			return normalize(stored);
		}

		if (legacyStored) {
			return normalize(legacyStored);
		}

		return normalize(readCookie());
	};

	const applyTheme = (preferenceValue) => {
		const preference = normalize(preferenceValue);
		const resolved = preference === 'system' ? (prefersDark() ? 'dark' : 'light') : preference;
		const root = document.documentElement;

		root.classList.remove('cw-theme-light', 'cw-theme-dark');
		root.classList.add(resolved === 'dark' ? 'cw-theme-dark' : 'cw-theme-light');
		if (resolved === 'dark') {
			root.classList.add('dark');
		} else {
			root.classList.remove('dark');
		}
		root.dataset.theme = resolved;
		root.dataset.themePreference = preference;
		root.style.colorScheme = resolved;

		document.querySelectorAll('[data-cw-theme-switcher]').forEach((select) => {
			if (select instanceof HTMLSelectElement && select.value !== preference) {
				select.value = preference;
			}
		});

		return { preference, resolved };
	};

	const setPreference = (nextPreference) => {
		const preference = normalize(nextPreference);

		try {
			localStorage.setItem(storageKey, preference);
			localStorage.setItem(legacyStorageKey, preference);
			localStorage.setItem(filamentStorageKey, preference);
		} catch (_) {
			// Ignore storage failures in restricted/private contexts.
		}

		writeThemeCookie(preference);

		return applyTheme(preference);
	};

	const media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;
	if (media) {
		const handleSystemThemeChange = () => {
			if (getPreference() === 'system') {
				applyTheme('system');
			}
		};

		if (typeof media.addEventListener === 'function') {
			media.addEventListener('change', handleSystemThemeChange);
		} else if (typeof media.addListener === 'function') {
			media.addListener(handleSystemThemeChange);
		}
	}

	applyTheme(getPreference());

	return {
		getPreference,
		setPreference,
		applyTheme,
	};
})();

window.cwTheme = cwTheme;

if (!window.Alpine || !window.Alpine.version) {
	window.Alpine = Alpine;
}

if (!window.__cwAlpineStarted) {
	window.__cwAlpineStarted = true;
	window.Alpine.start();
}

const readConsentState = () => {
	const body = document.body;
	const consentRequired = body?.dataset?.cwConsentRequired === '1';
	const analyticsEnabled = body?.dataset?.cwAnalyticsEnabled === '1';
	const marketingEnabled = body?.dataset?.cwMarketingEnabled === '1';
	const authConsentAllowed = body?.dataset?.cwAuthConsentAllowed === '1';

	const cookieAnalytics = readCookieValue('consent_analytics') === '1';
	const cookieMarketing = readCookieValue('consent_marketing') === '1';

	let storedChoice = null;
	let storedConsent = null;

	try {
		storedChoice = localStorage.getItem('cw_cookie_choice');
		const rawConsent = localStorage.getItem('crowork_consent');
		storedConsent = rawConsent ? JSON.parse(rawConsent) : null;
	} catch (_) {
		storedChoice = null;
		storedConsent = null;
	}

	const analyticsAllowed = !consentRequired || authConsentAllowed || cookieAnalytics || storedChoice === 'all' || storedConsent?.analytics === true;
	const marketingAllowed = !consentRequired || authConsentAllowed || cookieMarketing || storedChoice === 'all' || storedConsent?.marketing === true;

	return {
		analyticsAllowed: analyticsEnabled && analyticsAllowed,
		marketingAllowed: marketingEnabled && marketingAllowed,
	};
};

const persistConsentPreference = async ({ analytics, marketing, choice, source = 'cookie_banner' }) => {
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
	if (!csrfToken) {
		return;
	}

	try {
		await fetch('/consent/preferences', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Accept': 'application/json',
				'X-CSRF-TOKEN': csrfToken,
				'X-Requested-With': 'XMLHttpRequest',
			},
			body: JSON.stringify({
				analytics,
				marketing,
				choice,
				source,
			}),
		});
	} catch (_) {
		// Keep UX responsive even if consent sync endpoint is temporarily unavailable.
	}
};

const updateGoogleConsentMode = ({ analytics, marketing }) => {
	if (typeof window.gtag !== 'function') {
		return;
	}

	window.gtag('consent', 'update', {
		analytics_storage: analytics ? 'granted' : 'denied',
		ad_storage: marketing ? 'granted' : 'denied',
		ad_user_data: marketing ? 'granted' : 'denied',
		ad_personalization: marketing ? 'granted' : 'denied',
	});
};

const resolvePageType = () => {
	const path = window.location.pathname;
	if (path === '/') return 'homepage';
	if (path.startsWith('/jobs/')) return 'job_detail';
	if (path === '/jobs') return 'jobs_listing';
	if (path.startsWith('/educations/')) return 'education_detail';
	if (path === '/educations') return 'educations_listing';
	if (path.startsWith('/resources/')) return 'resource_detail';
	if (path === '/resources') return 'resources_listing';
	if (path === '/for-employers') return 'for_employers';
	if (path === '/about') return 'about';
	if (path === '/access') return 'access';
	if (path.startsWith('/worker')) return 'worker_dashboard';
	if (path.startsWith('/employer')) return 'employer_dashboard';
	return 'page';
};

const resolveRole = () => {
	const path = window.location.pathname;
	if (path.startsWith('/worker')) return 'worker';
	if (path.startsWith('/employer')) return 'employer';
	if (path.startsWith('/admin')) return 'admin';
	return 'guest';
};

const EVENT_ALIASES = {
	language_change: 'language_switch',
	theme_change: 'theme_switch',
	apply_start: 'job_apply_click',
	job_application_submit: 'job_apply_submit',
	education_application_submit: 'education_apply_submit',
	job_filter_open: 'filter_open',
	job_filter_apply: 'filter_apply',
	job_filter_reset: 'filter_clear',
	education_filter_open: 'filter_open',
	education_filter_apply: 'filter_apply',
	education_filter_reset: 'filter_clear',
	registration_start: 'register_start',
	registration_complete: 'register_complete',
	email_verification_resend: 'verification_code_sent',
	email_verification_completed: 'verification_success',
	login: 'login_success',
	resource_detail_view: 'resource_view',
};

const SENSITIVE_KEYS = [
	'email',
	'em',
	'phone',
	'ph',
	'name',
	'first_name',
	'last_name',
	'message',
	'text',
	'query',
	'q',
];

const ANALYTICS_EVENT_SCOPE = {
	default: 'analytics',
	employer_cta_click: 'marketing',
	post_job_click: 'marketing',
	job_apply_click: 'marketing',
	job_apply_submit: 'marketing',
	job_apply_complete: 'marketing',
	education_apply_click: 'marketing',
	education_apply_submit: 'marketing',
	education_apply_complete: 'marketing',
	register_complete: 'marketing',
	employer_register_start: 'marketing',
	employer_register_complete: 'marketing',
	verification_success: 'marketing',
};

const META_EVENT_MAP = {
	job_view: { type: 'track', name: 'ViewContent' },
	education_view: { type: 'track', name: 'ViewContent' },
	resource_view: { type: 'track', name: 'ViewContent' },
	guide_open: { type: 'track', name: 'ViewContent' },
	job_search: { type: 'track', name: 'Search' },
	education_search: { type: 'track', name: 'Search' },
	resource_search: { type: 'track', name: 'Search' },
	employer_cta_click: { type: 'track', name: 'Lead' },
	post_job_click: { type: 'track', name: 'Lead' },
	employer_register_start: { type: 'track', name: 'Lead' },
	register_complete: { type: 'track', name: 'CompleteRegistration' },
	job_apply_submit: { type: 'trackCustom', name: 'JobApplyIntent' },
	education_apply_submit: { type: 'trackCustom', name: 'EducationApplyIntent' },
	job_apply_complete: { type: 'track', name: 'SubmitApplication' },
	education_apply_complete: { type: 'track', name: 'SubmitApplication' },
	password_reset_request: { type: 'trackCustom', name: 'PasswordResetRequest' },
};

const eventDedupeCache = new Map();

const getTrackDebugMode = () => {
	const bodyDebug = document.body?.dataset?.cwTrackDebug === '1';
	const explicitDebug = window.__CW_TRACK_DEBUG__ === true;
	const localHostDebug = ['localhost', '127.0.0.1'].includes(window.location.hostname);
	return bodyDebug || explicitDebug || localHostDebug;
};

const normalizeEventName = (eventName) => EVENT_ALIASES[eventName] || eventName;

const sanitizePayload = (payload = {}) => {
	const safePayload = {};
	Object.entries(payload || {}).forEach(([key, value]) => {
		if (value === null || typeof value === 'undefined') return;
		const lowerKey = key.toLowerCase();
		if (SENSITIVE_KEYS.includes(lowerKey)) {
			if (lowerKey === 'query' || lowerKey === 'q' || lowerKey === 'text') {
				safePayload.query_length = String(value).trim().length;
			}
			return;
		}

		if (typeof value === 'string') {
			safePayload[key] = value.slice(0, 120);
			return;
		}

		if (typeof value === 'number' || typeof value === 'boolean') {
			safePayload[key] = value;
			return;
		}

		if (Array.isArray(value)) {
			safePayload[key] = value.slice(0, 10).map((item) => (typeof item === 'string' ? item.slice(0, 40) : item));
		}
	});

	return safePayload;
};

const shouldDeduplicate = (eventName, payload) => {
	const dedupeKey = `${eventName}:${payload.page_type || 'page'}:${payload.item_slug || payload.job_slug || payload.education_slug || ''}:${payload.path || ''}`;
	const now = Date.now();
	const previous = eventDedupeCache.get(dedupeKey);
	if (previous && now - previous < 800) {
		return true;
	}
	eventDedupeCache.set(dedupeKey, now);
	return false;
};

const croworkMetaTrack = (eventName, payload = {}, explicitEventId = null) => {
	const consentState = readConsentState();
	if (!consentState.marketingAllowed || typeof window.fbq !== 'function') {
		return false;
	}

	const mapped = META_EVENT_MAP[eventName];
	if (!mapped) {
		return false;
	}

	const eventId = explicitEventId || payload.event_id || null;
	const options = eventId ? { eventID: eventId } : {};

	if (mapped.type === 'track') {
		window.fbq('track', mapped.name, payload, options);
		return true;
	}

	window.fbq('trackCustom', mapped.name, payload, options);
	return true;
};

window.croworkMetaTrack = croworkMetaTrack;

const cwTrack = (eventName, payload = {}) => {
	if (!eventName || typeof eventName !== 'string') {
		return;
	}

	const normalizedEventName = normalizeEventName(eventName);
	const consentState = readConsentState();
	const eventScope = ANALYTICS_EVENT_SCOPE[normalizedEventName] || ANALYTICS_EVENT_SCOPE.default;

	const eventId = payload.event_id || `cw_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;
	const cleanedPayload = sanitizePayload(payload);
	const finalPayload = {
		...cleanedPayload,
		event_id: eventId,
		locale: document.documentElement.lang || null,
		page_type: cleanedPayload.page_type || resolvePageType(),
		role: cleanedPayload.role || resolveRole(),
		path: cleanedPayload.path || window.location.pathname,
	};

	if (shouldDeduplicate(normalizedEventName, finalPayload)) {
		return;
	}

	const shouldSendAnalytics = consentState.analyticsAllowed;
	const shouldSendMarketing = consentState.marketingAllowed;
	const canSend = eventScope === 'marketing' ? shouldSendMarketing : shouldSendAnalytics;

	const detail = {
		event: normalizedEventName,
		payload: finalPayload,
		timestamp: new Date().toISOString(),
		scope: eventScope,
	};

	window.dispatchEvent(new CustomEvent('cw:analytics', { detail }));

	if (!canSend) {
		if (getTrackDebugMode()) {
			console.debug('[cwTrack:block]', normalizedEventName, detail);
		}
		return;
	}

	if (Array.isArray(window.dataLayer)) {
		window.dataLayer.push({ event: normalizedEventName, ...finalPayload });
	}

	if (typeof window.gtag === 'function') {
		window.gtag('event', normalizedEventName, finalPayload);
	}

	if (shouldSendMarketing) {
		croworkMetaTrack(normalizedEventName, finalPayload, eventId);
	}

	if (typeof window.plausible === 'function' && shouldSendAnalytics) {
		window.plausible(normalizedEventName, { props: finalPayload });
	}

	if (getTrackDebugMode()) {
		console.debug('[cwTrack]', normalizedEventName, detail);
	}
};

window.cwTrack = cwTrack;

const initCroworkUi = () => {
	const applyLogoFallback = (img) => {
		if (!(img instanceof HTMLImageElement) || img.dataset.cwFallbackApplied === '1') {
			return;
		}

		const fallbackText = (img.getAttribute('data-cw-fallback-text') || 'CW').trim().slice(0, 2).toUpperCase();
		const fallbackLabel = img.getAttribute('data-cw-fallback-label') || img.alt || 'Company';
		const logoContainer = img.closest('.cw-employer-logo');

		if (!logoContainer) {
			img.dataset.cwFallbackApplied = '1';
			img.style.display = 'none';
			return;
		}

		img.dataset.cwFallbackApplied = '1';
		img.remove();

		const fallback = document.createElement('span');
		fallback.className = 'cw-logo-fallback-initials';
		fallback.textContent = fallbackText || 'CW';
		logoContainer.setAttribute('aria-label', fallbackLabel);
		logoContainer.appendChild(fallback);
	};

	const bindLogoFallback = (img) => {
		if (!(img instanceof HTMLImageElement) || img.dataset.cwLogoFallbackBound === '1') {
			return;
		}

		img.dataset.cwLogoFallbackBound = '1';
		img.addEventListener('error', () => applyLogoFallback(img), { once: true });
	};

	const initLogoFallbacks = () => {
		document.querySelectorAll('img[data-cw-logo-image]').forEach((img) => bindLogoFallback(img));
	};

	const setTreeInteractiveState = (element, isInteractive) => {
		if (!element) {
			return;
		}

		element.setAttribute('aria-hidden', isInteractive ? 'false' : 'true');

		if ('inert' in element) {
			element.inert = !isInteractive;
		}

		element.querySelectorAll('a[href], button, input, select, textarea, [tabindex]').forEach((node) => {
			if (isInteractive) {
				const previousTabindex = node.getAttribute('data-cw-prev-tabindex');
				if (previousTabindex !== null) {
					node.setAttribute('tabindex', previousTabindex);
					node.removeAttribute('data-cw-prev-tabindex');
				} else if (node.getAttribute('tabindex') === '-1' && node.hasAttribute('data-cw-force-untabbable')) {
					node.removeAttribute('tabindex');
					node.removeAttribute('data-cw-force-untabbable');
				}
				return;
			}

			if (node.hasAttribute('tabindex')) {
				node.setAttribute('data-cw-prev-tabindex', node.getAttribute('tabindex') || '0');
			} else {
				node.setAttribute('data-cw-force-untabbable', '1');
			}
			node.setAttribute('tabindex', '-1');
		});
	};

	const updateBodyScrollLock = () => {
		const body = document.body;
		if (!body) {
			return;
		}

		const hasMobileLock = body.dataset.cwMobileNavScrollLock === '1';
		const hasFilterLock = body.dataset.cwFilterScrollLock === '1';
		body.style.overflow = hasMobileLock || hasFilterLock ? 'hidden' : '';
	};

	const setBodyScrollLock = (key, locked) => {
		if (!document.body) {
			return;
		}

		document.body.dataset[key] = locked ? '1' : '0';
		updateBodyScrollLock();
	};

	const initPublicNav = () => {
		const nav = document.querySelector('[data-cw-public-nav]');
		if (!nav) return;
		if (nav.dataset.cwPublicNavBound === '1') return;
		nav.dataset.cwPublicNavBound = '1';

		// Sticky nav logic with rAF batching to reduce scroll handler work.
		const scrolledClass = 'cw-public-nav-scrolled';
		const threshold = 12;
		let navRafId = null;
		const updateState = () => {
			navRafId = null;
			const scrolled = window.scrollY > threshold;
			nav.classList.toggle(scrolledClass, scrolled);
		};
		const scheduleUpdateState = () => {
			if (navRafId !== null) {
				return;
			}
			navRafId = window.requestAnimationFrame(updateState);
		};
		updateState();
		window.addEventListener('scroll', scheduleUpdateState, { passive: true });
		window.addEventListener('resize', scheduleUpdateState, { passive: true });

		// Premium fullscreen mobile nav overlay logic
		const mobileToggle = nav.querySelector('[data-cw-mobile-toggle]');
		const mobilePanel = nav.querySelector('[data-cw-mobile-panel]');
		const mobileClose = nav.querySelector('[data-cw-mobile-close]');
		const mobileBackdrop = nav.querySelector('[data-cw-mobile-backdrop]');
		let lastFocused = null;

		if (!mobileToggle || !mobilePanel) return;

		const isDesktopViewport = () => window.matchMedia('(min-width: 1024px)').matches;

		let setMobileOpen = (nextOpen) => {
			mobileToggle.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
			mobilePanel.hidden = !nextOpen;
			mobilePanel.setAttribute('aria-hidden', nextOpen ? 'false' : 'true');
			mobilePanel.style.display = nextOpen ? 'flex' : 'none';
			setBodyScrollLock('cwMobileNavScrollLock', nextOpen);
			setTreeInteractiveState(mobilePanel, nextOpen);
			if (nextOpen) {
				lastFocused = document.activeElement;
				mobilePanel.setAttribute('tabindex', '-1');
				mobilePanel.focus();
			} else {
				mobilePanel.removeAttribute('tabindex');
				if (lastFocused && typeof lastFocused.focus === 'function') {
					lastFocused.focus();
				}
			}
		};
		setMobileOpen(false);

		// Open overlay
		const toggleMobilePanel = (event) => {
			event.preventDefault();
			event.stopPropagation();
			const isOpen = mobileToggle.getAttribute('aria-expanded') === 'true';
			setMobileOpen(!isOpen);
		};

		if (window.PointerEvent) {
			mobileToggle.addEventListener('pointerup', toggleMobilePanel);
		} else {
			mobileToggle.addEventListener('click', toggleMobilePanel);
		}

		// Close overlay (X button)
		if (mobileClose) {
			mobileClose.addEventListener('click', (event) => {
				event.preventDefault();
				setMobileOpen(false);
			});
		}

		if (mobileBackdrop) {
			mobileBackdrop.addEventListener('click', (event) => {
				event.preventDefault();
				setMobileOpen(false);
			});
		}

		// Close on overlay click (outside nav content)
		mobilePanel.addEventListener('click', (event) => {
			if (event.target === mobilePanel) {
				setMobileOpen(false);
			}
		});

		// Close on Escape key
		window.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && mobilePanel.style.display === 'flex') {
				setMobileOpen(false);
			}
		});

		window.addEventListener('resize', () => {
			if (isDesktopViewport() && mobileToggle.getAttribute('aria-expanded') === 'true') {
				setMobileOpen(false);
			}
		});

		// Close on nav link or form submit
		mobilePanel.querySelectorAll('a[href], button[type="submit"]').forEach((item) => {
			item.addEventListener('click', () => {
				setMobileOpen(false);
			});
		});

		// Focus trap for accessibility
		mobilePanel.addEventListener('keydown', (event) => {
			if (event.key !== 'Tab') return;
			const focusable = mobilePanel.querySelectorAll('a[href], button, textarea, input, select, [tabindex]:not([tabindex="-1"])');
			const focusableArr = Array.prototype.slice.call(focusable);
			if (!focusableArr.length) return;
			const first = focusableArr[0];
			const last = focusableArr[focusableArr.length - 1];
			if (event.shiftKey) {
				if (document.activeElement === first) {
					event.preventDefault();
					last.focus();
				}
			} else {
				if (document.activeElement === last) {
					event.preventDefault();
					first.focus();
				}
			}
		});


		   // Mobile menu panel logic (main, language, theme, profile)
		   const mainPanel = mobilePanel.querySelector('[data-cw-mobile-content-main]');
		   const profilePanel = mobilePanel.querySelector('[data-cw-mobile-content-profile]');
		   const languagePanel = mobilePanel.querySelector('[data-cw-mobile-content-language]');
		   const themePanel = mobilePanel.querySelector('[data-cw-mobile-content-theme]');
		   const profileToggle = mobilePanel.querySelector('[data-cw-mobile-profile-toggle]');
		   const languageToggle = mobilePanel.querySelector('[data-cw-mobile-language-toggle]');
		   const themeToggle = mobilePanel.querySelector('[data-cw-mobile-theme-toggle]');
		   const backButtons = mobilePanel.querySelectorAll('[data-cw-mobile-back]');

		   const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		   const panels = {
			   main: mainPanel,
			   profile: profilePanel,
			   language: languagePanel,
			   theme: themePanel,
		   };

		   const setMobilePanel = (nextPanel) => {
			   mobilePanel.setAttribute('data-cw-mobile-state', nextPanel);
			   Object.entries(panels).forEach(([key, panel]) => {
				   if (!panel) return;
				   if (key === nextPanel) {
					   panel.style.transform = 'translateX(0)';
					   panel.style.opacity = '1';
					   panel.style.pointerEvents = 'auto';
					   setTreeInteractiveState(panel, true);
				   } else {
					   panel.style.transform = 'translateX(100%)';
					   panel.style.opacity = '0';
					   panel.style.pointerEvents = 'none';
					   setTreeInteractiveState(panel, false);
				   }
			   });

			   const languageExpanded = nextPanel === 'language';
			   const themeExpanded = nextPanel === 'theme';
			   const profileExpanded = nextPanel === 'profile';
			   languageToggle?.setAttribute('aria-expanded', languageExpanded ? 'true' : 'false');
			   themeToggle?.setAttribute('aria-expanded', themeExpanded ? 'true' : 'false');
			   profileToggle?.setAttribute('aria-expanded', profileExpanded ? 'true' : 'false');

			   window.requestAnimationFrame(() => {
				   const activePanel = panels[nextPanel];
				   if (!activePanel || mobileToggle.getAttribute('aria-expanded') !== 'true') {
					   return;
				   }

				   const focusTarget = activePanel.querySelector('button:not([disabled]), a[href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])');
				   if (focusTarget instanceof HTMLElement) {
					   focusTarget.focus();
				   }
			   });
		   };

		   // Reset to main panel when mobile menu opens
		   const originalSetMobileOpen = setMobileOpen;
		   setMobileOpen = (nextOpen) => {
			   originalSetMobileOpen(nextOpen);
			   if (nextOpen) {
				   setMobilePanel('main');
				   return;
			   }
			   setMobilePanel('main');
		   };

		   // Action button event listeners
		   if (profileToggle) {
			   profileToggle.addEventListener('click', (event) => {
				   event.preventDefault();
				   event.stopPropagation();
				   setMobilePanel('profile');
			   });
		   }
		   if (languageToggle) {
			   languageToggle.addEventListener('click', (event) => {
				   event.preventDefault();
				   event.stopPropagation();
				   setMobilePanel('language');
			   });
		   }
		   if (themeToggle) {
			   themeToggle.addEventListener('click', (event) => {
				   event.preventDefault();
				   event.stopPropagation();
				   setMobilePanel('theme');
			   });
		   }

		   // Back buttons in all submenus
		   backButtons.forEach((btn) => {
			   btn.addEventListener('click', (event) => {
				   event.preventDefault();
				   event.stopPropagation();
				   setMobilePanel('main');
			   });
		   });

		   // Close on profile links
		   if (profilePanel) {
			   profilePanel.querySelectorAll('a[href]').forEach((link) => {
				   link.addEventListener('click', () => {
					   setMobileOpen(false);
				   });
			   });
		   }

		   // Initialize to main panel
		   setMobilePanel('main');

		// Handle mobile locale and theme form submissions
		const mobileLocaleForm = mobilePanel.querySelector('[data-cw-mobile-locale-form]');
		const mobileThemeForm = mobilePanel.querySelector('[data-cw-mobile-theme-form]');

		if (mobileLocaleForm) {
			mobileLocaleForm.querySelectorAll('button[type="submit"]').forEach((btn) => {
				btn.addEventListener('click', (event) => {
					event.preventDefault();
					const localeValue = btn.getAttribute('value');
					const localeInput = mobileLocaleForm.querySelector('input[name="locale"]') || document.createElement('input');
					if (localeInput.tagName === 'INPUT') {
						localeInput.value = localeValue;
					} else {
						const input = document.createElement('input');
						input.type = 'hidden';
						input.name = 'locale';
						input.value = localeValue;
						mobileLocaleForm.appendChild(input);
					}
					mobileLocaleForm.submit();
				});
			});
		}

		if (mobileThemeForm) {
			mobileThemeForm.querySelectorAll('button[type="submit"]').forEach((btn) => {
				btn.addEventListener('click', (event) => {
					event.preventDefault();
					const themeValue = btn.getAttribute('value');
					const themeInput = mobileThemeForm.querySelector('input[name="theme"]') || document.createElement('input');
					if (themeInput.tagName === 'INPUT') {
						themeInput.value = themeValue;
					} else {
						const input = document.createElement('input');
						input.type = 'hidden';
						input.name = 'theme';
						input.value = themeValue;
						mobileThemeForm.appendChild(input);
					}

					// Apply theme immediately
					if (window.cwTheme && typeof window.cwTheme.setPreference === 'function') {
						window.cwTheme.setPreference(themeValue);
					}

					mobileThemeForm.submit();
				});
			});
		}
	};

	initPublicNav();
	initLogoFallbacks();
 	const initDropdownRoot = (root) => {
		if (!root || root.dataset.cwDropdownBound === '1') {
			return;
		}

		root.dataset.cwDropdownBound = '1';

		const triggers = Array.from(root.querySelectorAll('[data-cw-dropdown-trigger]'));
		const panels = Array.from(root.querySelectorAll('[data-cw-dropdown-panel]'));

		const hidePanel = (panel) => {
			if (!panel) {
				return;
			}

			panel.style.display = 'none';
			panel.setAttribute('aria-hidden', 'true');
			setTreeInteractiveState(panel, false);

			const id = panel.getAttribute('id');
			if (!id) {
				return;
			}

			triggers
				.filter((trigger) => trigger.getAttribute('data-cw-dropdown-trigger') === id)
				.forEach((trigger) => trigger.setAttribute('aria-expanded', 'false'));
		};

		const showPanel = (panel) => {
			if (!panel) {
				return;
			}

			panel.style.display = 'block';
			panel.setAttribute('aria-hidden', 'false');
			setTreeInteractiveState(panel, true);

			const id = panel.getAttribute('id');
			if (!id) {
				return;
			}

			triggers
				.filter((trigger) => trigger.getAttribute('data-cw-dropdown-trigger') === id)
				.forEach((trigger) => trigger.setAttribute('aria-expanded', 'true'));
		};

		const closeAll = () => {
			panels.forEach((panel) => hidePanel(panel));
		};

		closeAll();

		triggers.forEach((trigger) => {
			trigger.addEventListener('click', (event) => {
				event.preventDefault();
				event.stopPropagation();

				const targetId = trigger.getAttribute('data-cw-dropdown-trigger');
				const targetPanel = targetId ? root.querySelector(`#${targetId}`) : null;
				if (!targetPanel) {
					return;
				}

				const willOpen = targetPanel.style.display === 'none';
				closeAll();
				if (willOpen) {
					showPanel(targetPanel);
				}
			});
		});

		document.addEventListener('click', (event) => {
			if (!root.contains(event.target)) {
				closeAll();
			}
		});

		window.addEventListener('keydown', (event) => {
			if (event.key === 'Escape') {
				closeAll();
			}
		});

		root.querySelectorAll('[data-cw-dropdown-select]').forEach((button) => {
			button.addEventListener('click', () => {
				const action = button.getAttribute('data-cw-dropdown-select');

				if (action === 'locale') {
					const localeValue = button.getAttribute('data-cw-locale-value');
					const localeInput = root.querySelector('[data-cw-locale-input]');
					const localeForm = root.querySelector('[data-cw-locale-form]');

					if (localeInput && localeValue) {
						localeInput.value = localeValue;
					}

					closeAll();

					if (localeForm && typeof localeForm.submit === 'function') {
						localeForm.submit();
					}

					return;
				}

				if (action === 'theme') {
					const themeValue = button.getAttribute('data-cw-theme-value');
					const themeInput = root.querySelector('[data-cw-theme-input]');
					const themeForm = root.querySelector('[data-cw-theme-form]');

					if (themeInput && themeValue) {
						themeInput.value = themeValue;
					}

					if (themeValue && window.cwTheme && typeof window.cwTheme.setPreference === 'function') {
						window.cwTheme.setPreference(themeValue);
					}

					closeAll();

					if (themeForm && typeof themeForm.submit === 'function') {
						themeForm.submit();
					}

					return;
				}

				closeAll();
			});
		});
 	};

 document.querySelectorAll('[data-cw-dropdown-root]').forEach((root) => initDropdownRoot(root));

	cwTrack('page_view', {
		path: window.location.pathname,
		locale: document.documentElement.lang || null,
		theme: document.documentElement.dataset.theme || null,
	});

	if (Array.isArray(window.__cwTrackQueue)) {
		window.__cwTrackQueue.forEach((queued) => {
			if (!queued || typeof queued.event !== 'string') {
				return;
			}

			cwTrack(queued.event, queued.payload || {});
		});
		window.__cwTrackQueue = [];
	}

	const maybeInitFilterFallback = () => {
		const hasActiveAlpine = !!document.querySelector('[x-data]')?.__x;
		if (hasActiveAlpine) {
			return;
		}

		document.querySelectorAll('[data-cw-filter-form]').forEach((form) => {
			if (form.dataset.cwFallbackBound === '1') {
				return;
			}

			form.dataset.cwFallbackBound = '1';

			const panelId = form.getAttribute('data-cw-filter-panel-id');
			if (!panelId) {
				return;
			}

			const panel = form.querySelector(`#${panelId}`) || document.getElementById(panelId);
			if (!panel) {
				return;
			}

			const overlay = form.querySelector('[data-cw-filter-overlay]');
			const toggles = form.querySelectorAll(`[data-cw-filter-toggle][aria-controls="${panelId}"]`);
			const openers = form.querySelectorAll(`[data-cw-filter-open][aria-controls="${panelId}"]`);
			const closers = form.querySelectorAll(`[data-cw-filter-close][aria-controls="${panelId}"]`);

			const isDesktop = () => window.matchMedia('(min-width: 768px)').matches;
			const lockBodyScroll = () => setBodyScrollLock('cwFilterScrollLock', true);
			const unlockBodyScroll = () => setBodyScrollLock('cwFilterScrollLock', false);

			const setOpen = (nextOpen) => {
				panel.style.display = nextOpen ? 'block' : 'none';
				panel.setAttribute('aria-hidden', nextOpen ? 'false' : 'true');
				setTreeInteractiveState(panel, nextOpen);
				if (overlay) {
					overlay.style.display = !isDesktop() && nextOpen ? 'block' : 'none';
					overlay.setAttribute('aria-hidden', !isDesktop() && nextOpen ? 'false' : 'true');
				}
				if (!isDesktop() && nextOpen) {
					lockBodyScroll();
				} else {
					unlockBodyScroll();
				}

				toggles.forEach((button) => {
					button.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
				});
			};

			setOpen(false);

			toggles.forEach((button) => {
				button.addEventListener('click', (event) => {
					event.preventDefault();
					const currentlyOpen = panel.style.display !== 'none';
					if (isDesktop()) {
						setOpen(!currentlyOpen);
					} else {
						setOpen(true);
					}
				});
			});

			openers.forEach((button) => {
				button.addEventListener('click', (event) => {
					event.preventDefault();
					setOpen(true);
				});
			});

			closers.forEach((button) => {
				button.addEventListener('click', (event) => {
					event.preventDefault();
					setOpen(false);
				});
			});

			overlay?.addEventListener('click', () => setOpen(false));

			window.addEventListener('keydown', (event) => {
				if (event.key === 'Escape') {
					setOpen(false);
				}
			});

			window.addEventListener('resize', () => {
				if (isDesktop() && overlay) {
					overlay.style.display = 'none';
				}
				if (isDesktop()) {
					unlockBodyScroll();
				}
			});
		});
	};

	maybeInitFilterFallback();

	const banner = document.querySelector('[data-cw-cookie-banner]');
	if (banner) {
		const modal = document.querySelector('[data-cw-cookie-modal]');
		const analyticsInput = modal?.querySelector('[data-cw-cookie-analytics]');
		const marketingInput = modal?.querySelector('[data-cw-cookie-marketing]');
		const savePreferencesButton = modal?.querySelector('[data-cw-cookie-save]');
		const consentRequired = document.body?.dataset?.cwConsentRequired === '1';
		if (!consentRequired) {
			banner.setAttribute('hidden', 'hidden');
			modal?.setAttribute('hidden', 'hidden');
		} else {
			const setConsent = (choice, analytics, marketing) => {
				const normalizedChoice = choice === 'all' || choice === 'required' || choice === 'custom' ? choice : 'required';

				document.cookie = `consent_analytics=${analytics ? '1' : '0'}; path=/; max-age=${365 * 24 * 60 * 60}; samesite=lax`;
				document.cookie = `consent_marketing=${marketing ? '1' : '0'}; path=/; max-age=${365 * 24 * 60 * 60}; samesite=lax`;
				document.cookie = `cw_cookie_choice=${encodeURIComponent(normalizedChoice)}; path=/; max-age=${365 * 24 * 60 * 60}; samesite=lax`;

				try {
					localStorage.setItem('cw_cookie_choice', normalizedChoice);
					localStorage.setItem('crowork_consent', JSON.stringify({
						analytics,
						marketing,
						choice: normalizedChoice,
						timestamp: new Date().toISOString(),
					}));
				} catch (_) {
					// Ignore storage failures in restricted/private contexts.
				}

				persistConsentPreference({
					analytics,
					marketing,
					choice: normalizedChoice,
					source: 'cookie_banner',
				});

				updateGoogleConsentMode({ analytics, marketing });
			};

			const openModal = () => {
				if (!modal) {
					return;
				}

				const state = readConsentState();
				if (analyticsInput instanceof HTMLInputElement) {
					analyticsInput.checked = state.analyticsAllowed;
				}
				if (marketingInput instanceof HTMLInputElement) {
					marketingInput.checked = state.marketingAllowed;
				}

				banner.setAttribute('hidden', 'hidden');
				modal.removeAttribute('hidden');
			};

			const closeModal = () => {
				modal?.setAttribute('hidden', 'hidden');
				if (!(savedChoice === 'required' || savedChoice === 'all' || savedChoice === 'custom')) {
					banner.removeAttribute('hidden');
				}
			};

			let savedChoice = null;
			try {
				savedChoice = localStorage.getItem('cw_cookie_choice');
			} catch (_) {
				savedChoice = null;
			}

			if (savedChoice === 'required' || savedChoice === 'all' || savedChoice === 'custom') {
				banner.setAttribute('hidden', 'hidden');
			} else {
				banner.removeAttribute('hidden');

				const choiceButtons = banner.querySelectorAll('[data-cw-cookie-choice]');
				choiceButtons.forEach((button) => {
					button.addEventListener('click', () => {
						const choice = button.getAttribute('data-cw-cookie-choice');
						if (choice === 'customize') {
							openModal();
							return;
						}

						if (choice !== 'required' && choice !== 'all') {
							return;
						}

						const allowOptional = choice === 'all';
						setConsent(choice, allowOptional, allowOptional);
						savedChoice = choice;
						cwTrack(choice === 'all' ? 'cookie_consent_accept_all' : 'cookie_consent_required_only', {
							choice,
						});

						banner.setAttribute('hidden', 'hidden');
						closeModal();
						window.setTimeout(() => window.location.reload(), 120);
					});
				});

				modal?.addEventListener('click', (event) => {
					if (event.target === modal) {
						closeModal();
					}
				});

				savePreferencesButton?.addEventListener('click', () => {
					const analytics = analyticsInput instanceof HTMLInputElement ? analyticsInput.checked : false;
					const marketing = marketingInput instanceof HTMLInputElement ? marketingInput.checked : false;
					const choice = analytics && marketing ? 'all' : (!analytics && !marketing ? 'required' : 'custom');

					setConsent(choice, analytics, marketing);
					savedChoice = choice;
					cwTrack('cookie_consent_customize', {
						choice,
						analytics,
						marketing,
					});

					banner.setAttribute('hidden', 'hidden');
					closeModal();
					window.setTimeout(() => window.location.reload(), 120);
				});
			}
		}
	}

	document.querySelectorAll('[data-cw-language-option]').forEach((button) => {
		button.addEventListener('click', () => {
			cwTrack('language_switch', {
				locale: button.getAttribute('data-cw-language-option'),
				path: window.location.pathname,
			});
		});
	});

	document.querySelectorAll('[data-cw-theme-option]').forEach((button) => {
		button.addEventListener('click', () => {
			cwTrack('theme_switch', {
				theme: button.getAttribute('data-cw-theme-option'),
				path: window.location.pathname,
			});

			// Apply theme preference immediately for mobile submenu
			const themeValue = button.getAttribute('data-cw-theme-option');
			if (themeValue && window.cwTheme && typeof window.cwTheme.setPreference === 'function') {
				window.cwTheme.setPreference(themeValue);
			}
		});
	});

	document.addEventListener('click', (event) => {
		const trackedElement = event.target.closest('[data-cw-track-click]');
		if (!trackedElement) {
			return;
		}

		const eventName = trackedElement.getAttribute('data-cw-track-click');
		if (!eventName) {
			return;
		}

		cwTrack(eventName, {
			item_slug: trackedElement.getAttribute('data-cw-item-slug') || null,
			item_type: trackedElement.getAttribute('data-cw-item-type') || null,
			has_href: Boolean(trackedElement.getAttribute('href')),
		});
	});

	document.addEventListener('submit', (event) => {
		const form = event.target;
		if (!(form instanceof HTMLFormElement)) {
			return;
		}

		let submitter = event.submitter;
		if (!(submitter instanceof HTMLButtonElement || submitter instanceof HTMLInputElement)) {
			submitter = form.querySelector('button[type="submit"], input[type="submit"]');
		}

		if (submitter instanceof HTMLElement) {
			const label = submitter.querySelector('[data-cw-submit-label]');
			if (label) {
				const loadingLabel = label.getAttribute('data-loading-label');
				if (loadingLabel) {
					label.textContent = loadingLabel;
				}
			}

			submitter.setAttribute('disabled', 'disabled');
		}

		form.classList.add('cw-is-submitting');
		form.setAttribute('aria-busy', 'true');

		const eventName = form.getAttribute('data-cw-track-submit');
		if (eventName) {
			const method = (form.getAttribute('method') || 'GET').toUpperCase();
			const action = (form.getAttribute('action') || '').toLowerCase();
			const isHandledSearchForm = method === 'GET' && (action.includes('/jobs') || action.includes('/educations'));
			if (isHandledSearchForm && (eventName === 'homepage_search' || eventName === 'job_search' || eventName === 'education_search')) {
				return;
			}

			cwTrack(eventName, {
				action: form.getAttribute('action') || null,
				method,
			});
		}
	});

	document.addEventListener('submit', (event) => {
		const form = event.target;
		if (!(form instanceof HTMLFormElement)) {
			return;
		}

		const action = (form.getAttribute('action') || '').toLowerCase();
		const method = (form.getAttribute('method') || 'GET').toUpperCase();
		const accountType = form.querySelector('[name="account_type"]')?.value || null;
		const intentType = form.querySelector('[name="intent_type"]')?.value || null;
		const isPublishChecked = form.querySelector('[name="is_active"]')?.checked === true;

		if (method === 'GET' && action.includes('/jobs')) {
			const queryValue = form.querySelector('[name="q"]')?.value || '';
			const isHomepage = window.location.pathname === '/';
			cwTrack(isHomepage ? 'homepage_search' : 'job_search', {
				query_length: String(queryValue).trim().length,
			});
		}

		if (method === 'GET' && action.includes('/educations')) {
			const queryValue = form.querySelector('[name="q"]')?.value || '';
			cwTrack('education_search', {
				query_length: String(queryValue).trim().length,
			});
		}

		if (method === 'POST' && action.includes('/access/email')) {
			cwTrack('access_start', { source: 'access_email' });
		}

		if (method === 'POST' && action.includes('/access/register')) {
			cwTrack('register_start', { source: 'access_register', account_type: accountType });
			if (accountType === 'employer') {
				cwTrack('employer_register_start', { source: 'access_register' });
			}
		}

		if (method === 'POST' && action.includes('/logout')) {
			cwTrack('logout', { source: 'header_logout' });
		}

		if (method === 'POST' && /\/jobs\/[^/]+\/apply/.test(action)) {
			cwTrack('job_application_submit', { source: 'job_apply_form' });
		}

		if (method === 'POST' && /\/educations\/[^/]+\/apply/.test(action)) {
			cwTrack('education_application_submit', { source: 'education_apply_form' });
		}

		if (method === 'POST' && action.includes('/password/email')) {
			cwTrack('password_reset_request', { source: 'forgot_password' });
		}

		if ((method === 'POST' || method === 'PUT' || method === 'PATCH') && action.includes('/worker/profile')) {
			cwTrack('worker_profile_update', { source: 'worker_dashboard' });
		}

		if ((method === 'POST' || method === 'PUT' || method === 'PATCH') && action.includes('/employer/settings/profile')) {
			cwTrack('employer_branding_update', { source: 'employer_dashboard' });
		}

		if (method === 'POST' && action.includes('/employer/jobs')) {
			cwTrack('employer_job_create', { source: 'employer_jobs' });
			if (isPublishChecked) {
				cwTrack('employer_job_publish', { source: 'employer_jobs' });
			}
		}

		if ((method === 'PUT' || method === 'PATCH') && /\/employer\/jobs\//.test(action) && isPublishChecked) {
			cwTrack('employer_job_publish', { source: 'employer_jobs' });
		}

		if (method === 'POST' && action.includes('/notifications/read-all')) {
			cwTrack('notification_mark_all_read', { source: 'notifications' });
		}
	});

	const resourceSearchInput = document.querySelector('[data-cw-resource-search]');
	if (resourceSearchInput instanceof HTMLInputElement) {
		let searchDebounceId = null;
		resourceSearchInput.addEventListener('input', (event) => {
			window.clearTimeout(searchDebounceId);
			const value = event.target.value || '';
			searchDebounceId = window.setTimeout(() => {
				if (String(value).trim().length >= 2) {
					cwTrack('resource_search', { query_length: String(value).trim().length });
				}
			}, 380);
		});
	}

	document.addEventListener('click', (event) => {
		const faqToggle = event.target.closest('[data-cw-faq-toggle]');
		if (faqToggle) {
			cwTrack('faq_open', { section: faqToggle.getAttribute('data-cw-faq-section') || 'resources' });
		}
	});

	document.addEventListener('invalid', (event) => {
		const field = event.target;
		if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
			return;
		}

		field.classList.add('cw-field-invalid');
		field.setAttribute('aria-invalid', 'true');
	}, true);

	document.addEventListener('input', (event) => {
		const field = event.target;
		if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
			return;
		}

		if (field.classList.contains('cw-field-invalid') && field.checkValidity()) {
			field.classList.remove('cw-field-invalid');
			field.removeAttribute('aria-invalid');
		}
	});

	window.addEventListener('pageshow', () => {
		document.querySelectorAll('form.cw-is-submitting').forEach((form) => {
			form.classList.remove('cw-is-submitting');
			form.removeAttribute('aria-busy');
			form.querySelectorAll('button[disabled][type="submit"], input[disabled][type="submit"]').forEach((button) => {
				button.removeAttribute('disabled');
			});
		});
	});
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initCroworkUi, { once: true });
} else {
	initCroworkUi();
}
