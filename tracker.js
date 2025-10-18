(function() {
  function getAssetId() {
    const scripts = document.querySelectorAll('script[src*="tracker.js"]');
    for (let script of scripts) {
      if (script.dataset.assetId) {
        return parseInt(script.dataset.assetId);
      }
    }
    
    if (window.BRICKMMO_ASSET_ID) {
      return parseInt(window.BRICKMMO_ASSET_ID);
    }
    
    const params = new URLSearchParams(window.location.search);
    if (params.get('asset_id')) {
      return parseInt(params.get('asset_id'));
    }
    
    return 1;
  }

  function getBrowserInfo() {
    const ua = navigator.userAgent;
    let browser = "Unknown";
    let os = "Unknown";

    if (ua.indexOf("Edg") > -1) browser = "Edge";
    else if (ua.indexOf("Chrome") > -1) browser = "Chrome";
    else if (ua.indexOf("Safari") > -1) browser = "Safari";
    else if (ua.indexOf("Firefox") > -1) browser = "Firefox";
    else if (ua.indexOf("Opera") > -1 || ua.indexOf("OPR") > -1) browser = "Opera";
    else if (ua.indexOf("MSIE") > -1 || ua.indexOf("Trident/") > -1) browser = "IE";

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

    return { browser, os };
  }

  function trackPageView() {
    const { browser, os } = getBrowserInfo();
    
    const payload = {
      asset_id: getAssetId(),
      url: window.location.href,
      referrer: document.referrer || null,
      browser: browser,
      os: os,
      user_agent: navigator.userAgent
    };

    const trackingUrl = window.BRICKMMO_TRACKING_URL || 
                       document.querySelector('script[src*="tracker.js"]')?.dataset.trackingUrl || 
                       '/BrickMMO_stats-v1/track.php';

    fetch(trackingUrl, {
      method: "POST",
      headers: { 
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      body: JSON.stringify(payload),
      mode: 'cors'
    })
    .catch(err => {
      if (window.BRICKMMO_DEBUG) {
        console.error("Tracking failed:", err);
      }
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', trackPageView);
  } else {
    trackPageView();
  }

  window.BrickMMOTrack = trackPageView;

})();
