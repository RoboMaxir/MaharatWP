/**
 * File navigation.js.
 *
 * Handles the primary navigation menu toggle functionality.
 */

(function() {
	'use strict';

	// Get the navigation elements
	const siteNavigation = document.querySelector('.site-navigation');
	if (!siteNavigation) {
		return;
	}

	const button = siteNavigation.querySelector('button');
	if (!button) {
		return;
	}

	const menu = siteNavigation.querySelector('ul');
	if (!menu) {
		return;
	}

	// Add classes for accessibility
	button.classList.add('primary-menu-toggle');
	menu.classList.add('primary-menu');

	// Toggle menu visibility
	button.addEventListener('click', function() {
		siteNavigation.classList.toggle('toggled');
		button.classList.toggle('toggled');
		
		// Update ARIA attributes
		const expanded = button.getAttribute('aria-expanded') === 'true';
		button.setAttribute('aria-expanded', !expanded);
		menu.setAttribute('aria-expanded', !expanded);
	});

	// Close menu when clicking outside
	document.addEventListener('click', function(event) {
		if (!siteNavigation.contains(event.target)) {
			siteNavigation.classList.remove('toggled');
			button.setAttribute('aria-expanded', false);
			menu.setAttribute('aria-expanded', false);
		}
	});

	// Handle keyboard navigation
	menu.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			siteNavigation.classList.remove('toggled');
			button.setAttribute('aria-expanded', false);
			menu.setAttribute('aria-expanded', false);
			button.focus();
		}
	});

	// Submenu handling
	const submenuItems = menu.querySelectorAll('.menu-item-has-children');
	submenuItems.forEach(function(item) {
		const submenuToggle = document.createElement('button');
		submenuToggle.classList.add('submenu-toggle');
		submenuToggle.setAttribute('aria-expanded', 'false');
		submenuToggle.innerHTML = '<span class="screen-reader-text">Expand child menu</span><span class="icon"></span>';
		
		const link = item.querySelector('a');
		if (link) {
			link.after(submenuToggle);
		}

		submenuToggle.addEventListener('click', function() {
			const submenu = item.querySelector('ul');
			if (submenu) {
				const expanded = submenuToggle.getAttribute('aria-expanded') === 'true';
				submenuToggle.setAttribute('aria-expanded', !expanded);
				submenu.classList.toggle('toggled');
			}
		});
	});

	// Mobile menu toggle
	const mobileToggle = document.createElement('button');
	mobileToggle.classList.add('mobile-menu-toggle');
	mobileToggle.setAttribute('aria-expanded', 'false');
	mobileToggle.innerHTML = '<span class="screen-reader-text">Toggle mobile menu</span><span class="icon"></span>';
	
	if (siteNavigation.firstElementChild) {
		siteNavigation.firstElementChild.before(mobileToggle);
	}

	mobileToggle.addEventListener('click', function() {
		const expanded = mobileToggle.getAttribute('aria-expanded') === 'true';
		mobileToggle.setAttribute('aria-expanded', !expanded);
		siteNavigation.classList.toggle('mobile-toggled');
	});

	// Responsive menu handling
	function handleResponsiveMenu() {
		if (window.innerWidth > 768) {
			// Desktop view - ensure menu is visible
			siteNavigation.classList.remove('mobile-toggled');
			mobileToggle.setAttribute('aria-expanded', 'false');
		} else {
			// Mobile view - hide menu by default
			siteNavigation.classList.add('mobile-toggled');
			mobileToggle.setAttribute('aria-expanded', 'false');
		}
	}

	// Initial check
	handleResponsiveMenu();

	// Check on resize
	window.addEventListener('resize', handleResponsiveMenu);

	// Add smooth scrolling for anchor links
	document.querySelectorAll('a[href^="#"]').forEach(anchor => {
		anchor.addEventListener('click', function(e) {
			const targetId = this.getAttribute('href');
			if (targetId === '#') return;
			
			const targetElement = document.querySelector(targetId);
			if (targetElement) {
				e.preventDefault();
				
				// Calculate offset for fixed header
				const offset = 80;
				const bodyRect = document.body.getBoundingClientRect().top;
				const elementRect = targetElement.getBoundingClientRect().top;
				const elementPosition = elementRect - bodyRect;
				const offsetPosition = elementPosition - offset;
			
				window.scrollTo({
					top: offsetPosition,
					behavior: 'smooth'
				});
			}
		});
	});

	// Add scroll detection for header effects
	let lastScrollTop = 0;
	const header = document.querySelector('.site-header');
	
	if (header) {
		window.addEventListener('scroll', function() {
			const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
			
			if (scrollTop > lastScrollTop && scrollTop > 100) {
				// Scrolling down - hide header
				header.classList.add('header-hidden');
			} else {
				// Scrolling up - show header
				header.classList.remove('header-hidden');
			}
			
			lastScrollTop = scrollTop;
		});
	}

	// Add active class to current menu item
	function setActiveMenuItem() {
		const currentURL = window.location.href;
		const menuLinks = menu.querySelectorAll('a');
		
		menuLinks.forEach(function(link) {
			if (link.href === currentURL) {
				link.classList.add('current');
				
				// Add active class to parent menu items
				let parent = link.parentElement;
				while (parent && parent !== menu) {
					if (parent.classList.contains('menu-item')) {
						parent.classList.add('current-menu-item');
					}
					parent = parent.parentElement;
				}
			} else {
				link.classList.remove('current');
			}
		});
	}

	// Run on page load and URL change
	setActiveMenuItem();
	
	// Handle popstate for back/forward buttons
	window.addEventListener('popstate', setActiveMenuItem);

	// Add loading state for external links that open in same window
	const externalLinks = document.querySelectorAll('a[href^="http"]:not([target="_blank"])');
	externalLinks.forEach(function(link) {
		link.addEventListener('click', function() {
			// Add loading indicator
			document.body.classList.add('loading');
		});
	});

	// Remove loading state when page is loaded
	window.addEventListener('load', function() {
		document.body.classList.remove('loading');
	});
})();