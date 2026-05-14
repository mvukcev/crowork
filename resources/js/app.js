import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const cwTrack = (eventName, payload = {}) => {
	if (!eventName || typeof eventName !== 'string') {
		return;
	}

	const detail = {
		event: eventName,
		payload,
		timestamp: new Date().toISOString(),
	};

	window.dispatchEvent(new CustomEvent('cw:analytics', { detail }));

	if (Array.isArray(window.dataLayer)) {
		window.dataLayer.push({ event: eventName, ...payload });
	}

	if (typeof window.plausible === 'function') {
		window.plausible(eventName, { props: payload });
	}
};

window.cwTrack = cwTrack;

document.addEventListener('DOMContentLoaded', () => {
	const banner = document.querySelector('[data-cw-cookie-banner]');
	if (!banner) {
		return;
	}

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

			banner.setAttribute('hidden', 'hidden');
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
	});
});
