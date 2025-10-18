(function() {
    'use strict';

    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    ready(function() {
        initLiveStats();
        initDebugPanel();
        initAnimations();
        initNavigationTracking();
    });

    function initLiveStats() {
        if (!document.getElementById('live-stats')) return;

        const ua = navigator.userAgent;
        let browser = "Unknown";
        let os = "Unknown";

        // Browser detection
        if (ua.indexOf("Edg") > -1) browser = "Microsoft Edge";
        else if (ua.indexOf("Chrome") > -1) browser = "Google Chrome";
        else if (ua.indexOf("Safari") > -1) browser = "Safari";
        else if (ua.indexOf("Firefox") > -1) browser = "Mozilla Firefox";
        else if (ua.indexOf("Opera") > -1 || ua.indexOf("OPR") > -1) browser = "Opera";

        // OS detection
        if (ua.indexOf("Windows NT 10.0") > -1) os = "Windows 10";
        else if (ua.indexOf("Windows NT 6.3") > -1) os = "Windows 8.1";
        else if (ua.indexOf("Windows NT 6.2") > -1) os = "Windows 8";
        else if (ua.indexOf("Windows NT 6.1") > -1) os = "Windows 7";
        else if (ua.indexOf("Windows NT") > -1) os = "Windows";
        else if (ua.indexOf("Mac OS X") > -1) {
            const version = ua.match(/Mac OS X ([0-9_]+)/);
            os = version ? "macOS " + version[1].replace(/_/g, ".") : "macOS";
        }
        else if (ua.indexOf("Linux") > -1) os = "Linux";
        else if (/Android/.test(ua)) {
            const version = ua.match(/Android ([0-9.]+)/);
            os = version ? "Android " + version[1] : "Android";
        }
        else if (/iPhone|iPad|iPod/.test(ua)) {
            const version = ua.match(/OS ([0-9_]+)/);
            os = version ? "iOS " + version[1].replace(/_/g, ".") : "iOS";
        }

        // Get timezone
        let timezone = 'Unknown';
        try {
            timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        } catch (e) {
            timezone = 'UTC' + (new Date().getTimezoneOffset() / -60);
        }

        // Update display elements
        const elements = {
            'browser-info': browser,
            'os-info': os,
            'screen-info': `${screen.width} x ${screen.height}`,
            'viewport-info': `${window.innerWidth} x ${window.innerHeight}`,
            'language-info': navigator.language || 'Unknown',
            'timezone-info': timezone,
            'url-info': window.location.href,
            'referrer-info': document.referrer || 'Direct',
            'timestamp-info': new Date().toLocaleString()
        };

        for (const [id, value] of Object.entries(elements)) {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = value;
            }
        }

        // Update viewport info on resize
        window.addEventListener('resize', function() {
            const viewportElement = document.getElementById('viewport-info');
            if (viewportElement) {
                viewportElement.textContent = `${window.innerWidth} x ${window.innerHeight}`;
            }
        });
    }

    // Initialize debug panel
    function initDebugPanel() {
        const debugPanel = document.getElementById('debug-info');
        if (!debugPanel) return;

        // Show debug panel initially
        setTimeout(() => {
            debugPanel.classList.add('show');
            updateDebugStatus('Tracker loaded');
        }, 1000);

        // Hide debug panel after 5 seconds
        setTimeout(() => {
            debugPanel.classList.remove('show');
        }, 6000);

        // Listen for tracking events (if available)
        if (window.BRICKMMO_DEBUG) {
            // Override console.log temporarily to catch tracking messages
            const originalLog = console.log;
            console.log = function(...args) {
                if (args[0] && args[0].includes && args[0].includes('BrickMMO Tracking')) {
                    updateDebugStatus('Tracking successful');
                    setTimeout(() => {
                        debugPanel.classList.remove('show');
                    }, 3000);
                }
                originalLog.apply(console, args);
            };
        }
    }

    function updateDebugStatus(status) {
        const statusElement = document.getElementById('debug-status');
        if (statusElement) {
            statusElement.textContent = status;
        }
    }

    // Initialize scroll animations
    function initAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                }
            });
        }, observerOptions);

        // Observe all feature cards and sections
        document.querySelectorAll('.feature-card, .stats-box, .content-section h2').forEach(el => {
            observer.observe(el);
        });
    }

    // Track navigation events
    function initNavigationTracking() {
        // Track external link clicks
        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href) return;

            // Track external links
            if (href.startsWith('http') && !href.includes(window.location.hostname)) {
                if (window.BRICKMMO_DEBUG) {
                    console.log('External link clicked:', href);
                }
                // You could send custom tracking data here
            }

            // Track admin dashboard access
            if (href.includes('/admin/')) {
                if (window.BRICKMMO_DEBUG) {
                    console.log('Admin dashboard accessed');
                }
            }
        });

        // Track form interactions
        document.addEventListener('focus', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                if (window.BRICKMMO_DEBUG) {
                    console.log('Form field focused:', e.target.name || e.target.id);
                }
            }
        }, true);

        // Track scroll depth
        let maxScroll = 0;
        window.addEventListener('scroll', function() {
            const scrollPercent = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
            if (scrollPercent > maxScroll) {
                maxScroll = scrollPercent;
                if (maxScroll >= 25 && maxScroll < 30 && window.BRICKMMO_DEBUG) {
                    console.log('Scroll depth: 25%');
                }
                if (maxScroll >= 50 && maxScroll < 55 && window.BRICKMMO_DEBUG) {
                    console.log('Scroll depth: 50%');
                }
                if (maxScroll >= 75 && maxScroll < 80 && window.BRICKMMO_DEBUG) {
                    console.log('Scroll depth: 75%');
                }
                if (maxScroll >= 90 && window.BRICKMMO_DEBUG) {
                    console.log('Scroll depth: 90%');
                }
            }
        });

        // Track time on page
        const startTime = Date.now();
        window.addEventListener('beforeunload', function() {
            const timeOnPage = Math.round((Date.now() - startTime) / 1000);
            if (window.BRICKMMO_DEBUG) {
                console.log('Time on page:', timeOnPage, 'seconds');
            }
        });
    }

    // Expose some functions globally for testing
    window.Demo2 = {
        updateDebugStatus: updateDebugStatus,
        maxScroll: 0
    };

})();