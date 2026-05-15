import './bootstrap';

import Alpine from 'alpinejs';

const readCookieValue = (name) => {
	const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]+)'));
	return match ? decodeURIComponent(match[1]) : null;
};

const cwTheme = (() => {
	const storageKey = 'cw_theme_preference';
	const allowed = ['light', 'dark', 'system'];

	const readCookie = () => readCookieValue('cw_theme');

	const prefersDark = () => window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;

	const normalize = (value) => (allowed.includes(value) ? value : 'system');

	const getPreference = () => {
		let stored = null;

		try {
			stored = localStorage.getItem(storageKey);
		} catch (_) {
			stored = null;
		}

		if (stored) {
			return normalize(stored);
		}

		return normalize(readCookie());
	};

	const applyTheme = (preferenceValue) => {
		const preference = normalize(preferenceValue);
		const resolved = preference === 'system' ? (prefersDark() ? 'dark' : 'light') : preference;
		const root = document.documentElement;

		root.classList.remove('cw-theme-light', 'cw-theme-dark');
		root.classList.add(resolved === 'dark' ? 'cw-theme-dark' : 'cw-theme-light');
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
		} catch (_) {
			// Ignore storage failures in restricted/private contexts.
		}

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

window.Alpine = Alpine;

Alpine.start();

const cwTrack = (eventName, payload = {}) => {
	if (!eventName || typeof eventName !== 'string') {
		return;
	}

	const body = document.body;
	const consentRequired = body?.dataset?.cwConsentRequired === '1';
	const analyticsEnabled = body?.dataset?.cwAnalyticsEnabled === '1';
	const marketingEnabled = body?.dataset?.cwMarketingEnabled === '1';

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

	const analyticsAllowed = !consentRequired || cookieAnalytics || storedChoice === 'all' || storedConsent?.analytics === true;
	const marketingAllowed = !consentRequired || cookieMarketing || storedChoice === 'all' || storedConsent?.marketing === true;

	const shouldSendAnalytics = analyticsEnabled && analyticsAllowed;
	const shouldSendMarketing = marketingEnabled && marketingAllowed;

	const eventId = payload.event_id || `cw_${Date.now()}_${Math.random().toString(36).slice(2, 10)}`;

	const detail = {
		event: eventName,
		payload: {
			...payload,
			event_id: eventId,
		},
		timestamp: new Date().toISOString(),
	};

	window.dispatchEvent(new CustomEvent('cw:analytics', { detail }));

	if (shouldSendAnalytics && Array.isArray(window.dataLayer)) {
		window.dataLayer.push({ event: eventName, ...payload });
	}

	if (shouldSendAnalytics && typeof window.gtag === 'function') {
		window.gtag('event', eventName, payload);
	}

	if (shouldSendMarketing && typeof window.fbq === 'function') {
		const metaMap = {
			page_view: { type: 'track', name: 'PageView' },
			job_view: { type: 'track', name: 'ViewContent' },
			education_view: { type: 'track', name: 'ViewContent' },
			company_view: { type: 'track', name: 'ViewContent' },
			job_search: { type: 'track', name: 'Search' },
			education_search: { type: 'track', name: 'Search' },
			apply_start: { type: 'track', name: 'Lead' },
			employer_cta_click: { type: 'track', name: 'Lead' },
			registration_complete: { type: 'track', name: 'CompleteRegistration' },
			job_application_submit: { type: 'trackCustom', name: 'SubmitApplication' },
			education_application_submit: { type: 'trackCustom', name: 'EducationApplicationSubmit' },
			contact_submit: { type: 'trackCustom', name: 'Contact' },
		};

		const mapped = metaMap[eventName];
		if (mapped) {
			const options = { eventID: eventId };
			if (mapped.type === 'track') {
				window.fbq('track', mapped.name, payload, options);
			} else {
				window.fbq('trackCustom', mapped.name, payload, options);
			}
		}
	}

	if (typeof window.plausible === 'function') {
		window.plausible(eventName, { props: payload });
	}
};

window.cwTrack = cwTrack;

document.addEventListener('DOMContentLoaded', () => {
	cwTrack('page_view', {
		path: window.location.pathname,
		locale: document.documentElement.lang || null,
		theme: document.documentElement.dataset.theme || null,
	});

	const banner = document.querySelector('[data-cw-cookie-banner]');
	if (!banner) {
		return;
	}

	const consentRequired = document.body?.dataset?.cwConsentRequired === '1';
	if (!consentRequired) {
		banner.setAttribute('hidden', 'hidden');
		return;
	}

	const setConsent = (choice) => {
		const analytics = choice === 'all';
		const marketing = choice === 'all';

		document.cookie = `consent_analytics=${analytics ? '1' : '0'}; path=/; max-age=${365 * 24 * 60 * 60}; samesite=lax`;
		document.cookie = `consent_marketing=${marketing ? '1' : '0'}; path=/; max-age=${365 * 24 * 60 * 60}; samesite=lax`;

		try {
			localStorage.setItem('crowork_consent', JSON.stringify({
				analytics,
				marketing,
				timestamp: new Date().toISOString(),
			}));
		} catch (_) {
			// Ignore storage failures in restricted/private contexts.
		}
	};

	let savedChoice = null;
	try {
		savedChoice = localStorage.getItem('cw_cookie_choice');
	} catch (_) {
		savedChoice = null;
	}

	if (savedChoice === 'required' || savedChoice === 'all') {
		banner.setAttribute('hidden', 'hidden');
		return;
	}

	banner.removeAttribute('hidden');

	const choiceButtons = banner.querySelectorAll('[data-cw-cookie-choice]');
	choiceButtons.forEach((button) => {
		button.addEventListener('click', () => {
			const choice = button.getAttribute('data-cw-cookie-choice');
			if (choice !== 'required' && choice !== 'all') {
				return;
			}

			try {
				localStorage.setItem('cw_cookie_choice', choice);
			} catch (_) {
				// Ignore storage failures in restricted/private contexts.
			}

			setConsent(choice);
			cwTrack(choice === 'all' ? 'cookie_consent_accept_all' : 'cookie_consent_required_only', {
				choice,
			});

			banner.setAttribute('hidden', 'hidden');
			window.setTimeout(() => window.location.reload(), 120);
		});
	});

	document.querySelectorAll('select[name="locale"]').forEach((select) => {
		select.addEventListener('change', (event) => {
			cwTrack('language_change', {
				locale: event.target.value,
				path: window.location.pathname,
			});
		});
	});

	document.querySelectorAll('select[name="theme"]').forEach((select) => {
		select.addEventListener('change', (event) => {
			cwTrack('theme_change', {
				theme: event.target.value,
				path: window.location.pathname,
			});
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
			href: trackedElement.getAttribute('href') || null,
			text: (trackedElement.textContent || '').trim().slice(0, 120),
		});
	});

	document.addEventListener('submit', (event) => {
		const form = event.target;
		if (!(form instanceof HTMLFormElement)) {
			return;
		}

		const eventName = form.getAttribute('data-cw-track-submit');
		if (!eventName) {
			return;
		}

		cwTrack(eventName, {
			action: form.getAttribute('action') || null,
			method: (form.getAttribute('method') || 'GET').toUpperCase(),
		});

		return;
	});

	document.addEventListener('submit', (event) => {
		const form = event.target;
		if (!(form instanceof HTMLFormElement)) {
			return;
		}

		const action = (form.getAttribute('action') || '').toLowerCase();
		const method = (form.getAttribute('method') || 'GET').toUpperCase();

		if (method === 'POST' && action.includes('/access/email')) {
			cwTrack('registration_start', { source: 'access_email' });
			cwTrack('email_verification_started', { source: 'access_email' });
		}

		if (method === 'POST' && action.includes('/access/verify-code')) {
			cwTrack('email_verification_completed', { source: 'access_verify' });
		}

		if (method === 'POST' && action.includes('/access/register')) {
			cwTrack('registration_complete', { source: 'access_register' });
		}

		if (method === 'POST' && (action.endsWith('/access/login') || action.endsWith('/admin/login'))) {
			cwTrack('login', { source: 'access_login' });
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

		if (method === 'POST' && action.includes('/notifications/read-all')) {
			cwTrack('notification_mark_all_read', { source: 'notifications' });
		}
	});
});
