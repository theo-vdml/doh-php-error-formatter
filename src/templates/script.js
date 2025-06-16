document.addEventListener('DOMContentLoaded', () => {
	const accordions = document.querySelectorAll('.accordion > button');

	accordions.forEach((btn, i) => {
		btn.addEventListener('click', () => {
			const expanded = btn.getAttribute('aria-expanded') === 'true';

			// Close all accordions
			accordions.forEach((otherBtn) => {
				otherBtn.setAttribute('aria-expanded', 'false');
				const panel = document.getElementById(otherBtn.getAttribute('aria-controls'));
				panel.classList.remove('max-h-screen', 'p-4');
				panel.classList.add('max-h-0', 'p-0');
				// Rotate icon back
				otherBtn.querySelector('i').classList.remove('rotate-180');
				otherBtn.classList.add('rounded-b');
			});

			if (!expanded) {
				// Open clicked accordion
				btn.setAttribute('aria-expanded', 'true');
				const panel = document.getElementById(btn.getAttribute('aria-controls'));
				panel.classList.remove('max-h-0');
				panel.classList.add('max-h-screen');
				btn.classList.remove('rounded-b');
				// Rotate icon arrow
				btn.querySelector('i').classList.add('rotate-180');
			}
		});
	});

	// On page load, rotate the open one's icon
	accordions.forEach((btn) => {
		if (btn.getAttribute('aria-expanded') === 'true') {
			btn.querySelector('i').classList.add('rotate-180');
		}
	});
});
