import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

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
});
