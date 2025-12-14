/**
 * Main theme JavaScript file
 * This file contains general theme functionality
 */

(function($) {
    'use strict';

    // Document ready
    $(document).ready(function() {
        
        // Mobile menu toggle
        $('.menu-toggle').on('click', function(e) {
            e.preventDefault();
            $('.site-navigation ul').slideToggle();
        });
        
        // Responsive navigation
        if ($(window).width() < 768) {
            $('.site-navigation ul').hide();
        }
        
        $(window).resize(function() {
            if ($(window).width() > 767) {
                $('.site-navigation ul').show();
            } else {
                if (!$('.menu-toggle').hasClass('active')) {
                    $('.site-navigation ul').hide();
                }
            }
        });
        
        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(event) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                event.preventDefault();
                $('html, body').animate({
                    scrollTop: target.offset().top - 80
                }, 500);
            }
        });
        
        // Project filtering
        $('.project-filter-btn').on('click', function(e) {
            e.preventDefault();
            
            var filter = $(this).data('filter');
            
            // Update active button
            $('.project-filter-btn').removeClass('active');
            $(this).addClass('active');
            
            // Filter projects
            if (filter === 'all') {
                $('.project').show();
            } else {
                $('.project').hide();
                $('.project[data-status="' + filter + '"]').show();
            }
        });
        
        // Back to top button
        var backToTop = $('<a href="#" class="back-to-top">↑</a>');
        backToTop.hide();
        $('body').append(backToTop);
        
        $(window).scroll(function() {
            if ($(this).scrollTop() > 300) {
                backToTop.fadeIn();
            } else {
                backToTop.fadeOut();
            }
        });
        
        backToTop.click(function(e) {
            e.preventDefault();
            $('html, body').animate({scrollTop: 0}, 500);
        });
        
        // Accessibility improvements
        $('a, button, input, select, textarea').each(function() {
            $(this).on('focus blur', function(e) {
                if (e.type === 'focus') {
                    $(this).addClass('focused');
                } else {
                    $(this).removeClass('focused');
                }
            });
        });
        
        // Search form submission
        $('.search-form').on('submit', function(e) {
            var searchTerm = $(this).find('.search-field').val();
            if (searchTerm.trim() === '') {
                e.preventDefault();
                alert('Please enter a search term');
            }
        });
        
        // Comment form validation
        $('#commentform').on('submit', function(e) {
            var comment = $('#comment').val();
            var name = $('#author').val();
            var email = $('#email').val();
            
            if (comment.trim() === '') {
                e.preventDefault();
                alert('Please enter your comment');
                return false;
            }
            
            if (name.trim() === '') {
                e.preventDefault();
                alert('Please enter your name');
                return false;
            }
            
            if (email.trim() === '') {
                e.preventDefault();
                alert('Please enter your email');
                return false;
            }
        });
        
        // Handle AJAX contact form if exists
        $('#contact-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = $(this).serialize();
            var submitButton = $(this).find('input[type="submit"]');
            
            // Show loading state
            submitButton.prop('disabled', true).val('Sending...');
            
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'contact_form_submit',
                    nonce: ajax_object.nonce,
                    form_data: formData
                },
                success: function(response) {
                    if (response.success) {
                        $('#contact-form').html('<div class="alert alert-success">Message sent successfully!</div>');
                    } else {
                        $('#contact-form-response').html('<div class="alert alert-error">Error: ' + response.data + '</div>');
                        submitButton.prop('disabled', false).val('Send Message');
                    }
                },
                error: function() {
                    $('#contact-form-response').html('<div class="alert alert-error">An error occurred. Please try again.</div>');
                    submitButton.prop('disabled', false).val('Send Message');
                }
            });
        });
        
        // Initialize project gallery if exists
        if ($('.project-gallery').length) {
            $('.project-gallery').each(function() {
                var $gallery = $(this);
                var $items = $gallery.find('.gallery-item');
                
                if ($items.length > 1) {
                    $items.hide();
                    $items.first().show();
                    
                    var $nav = $('<div class="gallery-nav"></div>');
                    var $prev = $('<button class="gallery-prev">‹ Prev</button>');
                    var $next = $('<button class="gallery-next">Next ›</button>');
                    
                    $nav.append($prev, $next);
                    $gallery.append($nav);
                    
                    var currentIndex = 0;
                    var totalItems = $items.length;
                    
                    $next.on('click', function(e) {
                        e.preventDefault();
                        currentIndex = (currentIndex + 1) % totalItems;
                        $items.hide();
                        $items.eq(currentIndex).show();
                    });
                    
                    $prev.on('click', function(e) {
                        e.preventDefault();
                        currentIndex = (currentIndex - 1 + totalItems) % totalItems;
                        $items.hide();
                        $items.eq(currentIndex).show();
                    });
                }
            });
        }
        
        // Initialize tooltips if exists
        if ($('[data-tooltip]').length) {
            $('[data-tooltip]').each(function() {
                var $el = $(this);
                var tooltipText = $el.data('tooltip');
                
                $el.append('<span class="tooltip">' + tooltipText + '</span>');
                
                $el.hover(
                    function() {
                        $(this).find('.tooltip').fadeIn();
                    },
                    function() {
                        $(this).find('.tooltip').fadeOut();
                    }
                );
            });
        }
        
    });
    
    // Window load event
    $(window).on('load', function() {
        // Initialize any components that need full page load
        initLazyLoading();
    });
    
    // Helper function for lazy loading
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }
    
    // Debounce function to limit rate of function calls
    function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
    
    // Throttle function to ensure function is called at most once per time interval
    function throttle(func, limit) {
        var inThrottle;
        return function() {
            var args = arguments;
            var context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() {
                    inThrottle = false;
                }, limit);
            }
        }
    }

})(jQuery);