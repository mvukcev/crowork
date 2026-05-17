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

const initCroworkUi = () => {
	const initPublicNav = () => {
		const nav = document.querySelector('[data-cw-public-nav]');
		if (!nav) return;
		if (nav.dataset.cwPublicNavBound === '1') return;
		nav.dataset.cwPublicNavBound = '1';

		// Sticky nav logic (unchanged)
		const scrolledClass = 'cw-public-nav-scrolled';
		const threshold = 12;
		const updateState = () => {
			const scrolled = window.scrollY > threshold;
			nav.classList.toggle(scrolledClass, scrolled);
			if (scrolled) {
				const isDark = document.documentElement.classList.contains('cw-theme-dark');
				nav.style.backgroundColor = isDark ? 'rgba(0, 0, 0, 0.62)' : 'rgba(255, 255, 255, 0.72)';
				nav.style.borderBottomColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(34, 34, 34, 0.1)';
				nav.style.boxShadow = isDark ? '0 8px 28px rgba(0, 0, 0, 0.26)' : '0 8px 28px rgba(34, 34, 34, 0.08)';
				nav.style.backdropFilter = 'blur(18px)';
				nav.style.webkitBackdropFilter = 'blur(18px)';
			} else {
				nav.style.backgroundColor = 'transparent';
				nav.style.borderBottomColor = 'transparent';
				nav.style.boxShadow = 'none';
				nav.style.backdropFilter = 'none';
				nav.style.webkitBackdropFilter = 'none';
			}
		};
		updateState();
		window.addEventListener('scroll', updateState, { passive: true });
		window.addEventListener('resize', updateState, { passive: true });

		// Premium fullscreen mobile nav overlay logic
		const mobileToggle = nav.querySelector('[data-cw-mobile-toggle]');
		const mobilePanel = nav.querySelector('[data-cw-mobile-panel]');
		const mobileClose = nav.querySelector('[data-cw-mobile-close]');
		let lastFocused = null;

		if (!mobileToggle || !mobilePanel) return;

		let setMobileOpen = (nextOpen) => {
			mobileToggle.setAttribute('aria-expanded', nextOpen ? 'true' : 'false');
			mobilePanel.hidden = !nextOpen;
			mobilePanel.style.display = nextOpen ? 'flex' : 'none';
			document.body.style.overflow = nextOpen ? 'hidden' : '';
			if (nextOpen) {
				lastFocused = document.activeElement;
				mobilePanel.setAttribute('tabindex', '-1');
				mobilePanel.focus();
			} else {
				if (lastFocused && typeof lastFocused.focus === 'function') {
					lastFocused.focus();
				}
			}
		};
		setMobileOpen(false);

		// Open overlay
		mobileToggle.addEventListener('click', (event) => {
			event.preventDefault();
			event.stopPropagation();
			const isOpen = mobileToggle.getAttribute('aria-expanded') === 'true';
			setMobileOpen(!isOpen);
		});

		// Close overlay (X button)
		if (mobileClose) {
			mobileClose.addEventListener('click', (event) => {
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
				   } else {
					   panel.style.transform = 'translateX(100%)';
					   panel.style.opacity = '0';
					   panel.style.pointerEvents = 'none';
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

			const setOpen = (nextOpen) => {
				panel.style.display = nextOpen ? 'block' : 'none';
				if (overlay) {
					overlay.style.display = !isDesktop() && nextOpen ? 'block' : 'none';
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
			});
		});
	};

	maybeInitFilterFallback();

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

	document.querySelectorAll('[data-cw-language-option]').forEach((button) => {
		button.addEventListener('click', () => {
			cwTrack('language_change', {
				locale: button.getAttribute('data-cw-language-option'),
				path: window.location.pathname,
			});
		});
	});

	document.querySelectorAll('[data-cw-theme-option]').forEach((button) => {
		button.addEventListener('click', () => {
			cwTrack('theme_change', {
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
			href: trackedElement.getAttribute('href') || null,
			text: (trackedElement.textContent || '').trim().slice(0, 120),
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
};

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initCroworkUi, { once: true });
} else {
	initCroworkUi();
}
