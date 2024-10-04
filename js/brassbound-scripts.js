jQuery(function($) {
    
    $.fn.isOnScreen = function(){
        
        var win = $(window);
        
        var viewport = {
            top : win.scrollTop(),
            left : win.scrollLeft()
        };
        viewport.right = viewport.left + win.width();
        viewport.bottom = viewport.top + win.height();
        
        var bounds = this.offset();
        bounds.right = bounds.left + this.outerWidth();
        bounds.bottom = bounds.top + this.outerHeight();
        
        return (!(viewport.right < bounds.left || viewport.left > bounds.right || viewport.bottom < bounds.top || viewport.top > bounds.bottom));
        
    };

    function trapFocus(element) {
        var focusableEls = element.querySelectorAll('a[href]:not([disabled]), button:not([disabled]), textarea:not([disabled]), input[type="text"]:not([disabled]), input[type="radio"]:not([disabled]), input[type="checkbox"]:not([disabled]), select:not([disabled])');
        var firstFocusableEl = focusableEls[0];
        var lastFocusableEl = focusableEls[focusableEls.length - 1];
        var KEYCODE_TAB = 9;

        element.addEventListener('keydown', function(e) {
            var isTabPressed = (e.key === 'Tab' || e.keyCode === KEYCODE_TAB);

            if (!isTabPressed) {
                return;
            }

            if (e.shiftKey) /* shift + tab */ {
                if (document.activeElement === firstFocusableEl) {
                    lastFocusableEl.focus();
                    e.preventDefault();
                }
            } else /* tab */ {
                if (document.activeElement === lastFocusableEl) {
                    firstFocusableEl.focus();
                    e.preventDefault();
                }
            }
        });
    }

    function stickyHeader() {
        var scrollHeight = $('.superheader').outerHeight();
        
        if (scrollHeight == null) {
            scrollHeight = 0;
        }
        
        if ($(window).scrollTop() > scrollHeight) {
            $('.site-header').addClass('site-header--sticky');
        } else {
            $('.site-header').removeClass('site-header--sticky');
        }

    }


    function mobileToggle() {
    
        var navContainer = document.querySelector('.mobile-nav');
    
        $('.menu-toggle').on('click', function() {
            var scrollPos = $(window).scrollTop();
            var headerHeight = $('.site-header').outerHeight();
    
            $('.site-container').addClass('fixed');
            if (scrollPos > 0) {
                $('.site-container').addClass('fixed--scroll');
                var siteOffset = scrollPos - headerHeight;
                $('.site-container').css('margin-top', -siteOffset);
                //$('.site-header').css('top', scrollPos);
            }
            $('.site-container').attr('aria-hidden', 'true');
            $('.site-container').attr('data-position', scrollPos);
            $('.mobile-nav').addClass('mobile-nav--visible');
            $('.mobile-nav').attr({
                'aria-hidden': 'false',
                'open': 'true',
                'tabindex': 0
            });
            $('.menu-close').focus();
            trapFocus(navContainer);
        });
    
        $('.menu-close').click(function() {
            var sitePos = $('.site-container').attr('data-position');
    
            $('.site-container').removeClass('fixed');
            if (sitePos > 0) {
                $('.site-container').removeClass('fixed--scroll');
                $('.site-container').css('padding-top', '0');
                $('.site-container').css('margin-top', '0');
            } else {
                sitePos = 0;
            }
            $('.site-container').attr('aria-hidden', 'false');
            //$('.site-header').css('top', '0');
            $('.mobile-nav').attr('aria-hidden', 'true');
            $('.mobile-nav').removeAttr('open tabindex');
            $('.menu-toggle').focus();
            $(window).scrollTop(sitePos);
            $('.mobile-nav').removeClass('mobile-nav--visible');
        });
        
        $('.menu-anchor').click(function() {
          var sitePos = $('.site-container').attr('data-position');
          
          $('.site-container').removeClass('fixed');
          if (sitePos > 0) {
              $('.site-container').removeClass('fixed--scroll');
              $('.site-container').css('padding-top', '0');
              $('.site-container').css('margin-top', '0');
          } else {
              sitePos = 0;
          }
          $('.site-container').attr('aria-hidden', 'false');
          //$('.site-header').css('top', '0');
          $('.mobile-nav').attr('aria-hidden', 'true');
          $('.mobile-nav').removeAttr('open tabindex');
          $('.menu-toggle').focus();
          $(window).scrollTop(sitePos);
          $('.mobile-nav').removeClass('mobile-nav--visible');
        });
    
        $('.submenu-toggle').click(function(e) {
            let parent = $(this).parent().parent('.menu-item-has-children');
            if ($(this).hasClass('rotated')) {
                $(this).removeClass('rotated');
                parent.children('.sub-menu').slideUp();
                $(this).focus();
                e.stopPropagation();
            } else {
                $(this).addClass('rotated');
                parent.children('.sub-menu').slideDown();
                parent.children('.sub-menu > li:first-of-type a').focus();
                e.stopPropagation();
            }
        });
    
    }


    function windowFix() {

        if ($(window).width() < 1100 && $('.mobile-nav').hasClass('mobile-nav--visible')) {
            $('.site-container').addClass('fixed');
        } else {
            $('.site-container').removeClass('fixed');
            $('.site-container').removeClass('fixed--scroll');
        }

    }

    $(document).ready(function() {
        mobileToggle();
        stickyHeader();
    });

    var windowWidth = $(window).width();

    $(window).resize(function() {
        if ($(window).width() != windowWidth) {
            windowFix();
        }
    });

    $(window).scroll(function() {
        stickyHeader();
    });
    
    $(window).on('resize scroll load', function() {
        $('.is-style-fade-in, .is-style-fade-in--left, .is-style-fade-in--right').each(function() {
            if ($(this).isOnScreen()) {
                $(this).addClass('fade-in--visible');
            }
        });
    });

});