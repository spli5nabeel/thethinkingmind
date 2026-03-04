# Advertisement Integration Guide

## Overview

Ad placeholders have been added to key pages throughout the application to enable monetization through display advertising. These placeholders are styled and positioned strategically for optimal visibility without disrupting user experience.

## Ad Placement Locations

### 1. **Homepage (index.php)**
- **Top Banner**: 728x90 leaderboard ad above welcome section
- **Mid Content**: 300x250 medium rectangle ad between options and "How It Works"

### 2. **Categories Page (categories.php)**
- **Top Banner**: 728x90 leaderboard ad below header

### 3. **Exam Page (exam.php)**
- **Top Banner**: 728x90 leaderboard ad above exam information

### 4. **Dashboard (dashboard.php)**
- **Top Banner**: 728x90 leaderboard ad above user statistics

### 5. **Results Review Page (exam_review.php)**
- **Top Banner**: 728x90 leaderboard ad above results summary
- **Mid Content**: 300x250 medium rectangle ad between results and detailed review

## Ad Placeholder Styles

All ad placeholders use the `.ad-placeholder` CSS class with the following variants:

### Size Variants
- `.ad-placeholder-banner` - 728x90 (min-height: 120px)
- `.ad-placeholder-large` - 300x250 (min-height: 250px)
- `.ad-placeholder-small` - Small ads (min-height: 90px)
- `.ad-placeholder-sidebar` - Sidebar ads (min-height: 600px, sticky positioning)

### Visual Design
- Light gray gradient background
- Dashed border for easy identification
- Centered placeholder icon (📢)
- Descriptive text showing ad dimensions
- Responsive design that adapts to mobile devices

## How to Replace Placeholders with Real Ads

### Option 1: Google AdSense

Replace the placeholder div with AdSense code:

```html
<!-- BEFORE (Placeholder) -->
<div class="ad-placeholder ad-placeholder-banner">
    <div class="ad-placeholder-text">Advertisement Space - 728x90</div>
</div>

<!-- AFTER (Google AdSense) -->
<div class="ad-container">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-XXXXXXXXXX"
         crossorigin="anonymous"></script>
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="ca-pub-XXXXXXXXXX"
         data-ad-slot="YYYYYYYYYY"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>
```

### Option 2: Direct Ad Networks (e.g., Media.net, PropellerAds)

Replace with network-specific code:

```html
<!-- Example: Media.net -->
<div class="ad-container" id="XXXXXXXXXX"></div>
<script src="//contextual.media.net/dmedianet.js?cid=XXXXXXXXXX"></script>
```

### Option 3: Custom/Direct Ad Sales

For self-managed ads:

```html
<div class="ad-container custom-ad">
    <a href="https://advertiser-link.com" target="_blank" rel="noopener">
        <img src="ads/banner-728x90.jpg" alt="Advertisement" 
             style="max-width: 100%; height: auto;">
    </a>
</div>
```

### Option 4: Programmatic Ads (Header Bidding)

```html
<div id="div-gpt-ad-XXXXXXXXXX" style="min-height: 90px;">
    <script>
        googletag.cmd.push(function() { googletag.display('div-gpt-ad-XXXXXXXXXX'); });
    </script>
</div>
```

## Best Practices

### 1. **Ad Placement Strategy**
- ✅ Top banner ads: High visibility, catches user attention
- ✅ Mid-content ads: Natural reading breaks, good engagement
- ⚠️ Avoid too many ads on a single page (negatively impacts UX)
- ⚠️ Keep ads above the fold for better viewability

### 2. **Performance Optimization**
- Use asynchronous loading for ad scripts
- Implement lazy loading for below-the-fold ads
- Set minimum heights to prevent layout shift (CLS)
- Monitor page load times after ad integration

### 3. **User Experience**
- Maintain clear visual separation between content and ads
- Ensure ads are responsive on mobile devices
- Avoid intrusive ad formats (pop-ups, auto-play video with sound)
- Consider user ad-blocker rates

### 4. **Ad Network Compliance**
- Follow Google AdSense policies (if using AdSense)
- Ensure GDPR/CCPA compliance for user data
- Don't click your own ads
- Avoid invalid traffic sources

## Revenue Optimization Tips

### 1. **A/B Testing**
Test different ad placements to find optimal positions:
```javascript
// Example: Track ad performance
gtag('event', 'ad_impression', {
    'ad_position': 'top_banner',
    'page': 'homepage'
});
```

### 2. **Ad Refresh**
Implement time-based ad refresh (respect network policies):
```javascript
// Refresh ads every 30 seconds (check network rules)
setInterval(function() {
    googletag.pubads().refresh();
}, 30000);
```

### 3. **Responsive Ad Units**
Use flexible ad units that adapt to screen size:
```html
<ins class="adsbygoogle"
     style="display:block"
     data-ad-format="fluid"
     data-ad-layout-key="-fb+5w+4e-db+86"
     data-ad-client="ca-pub-XXXXXXXXXX"
     data-ad-slot="YYYYYYYYYY"></ins>
```

## Privacy & Compliance

### GDPR Consent (Required for EU users)

Add a consent management platform (CMP):

```html
<!-- Example: Google Funding Choices -->
<script async src="https://fundingchoicesmessages.google.com/i/YOUR_PUBLISHER_ID"></script>
```

### CCPA Compliance (Required for California users)

Provide "Do Not Sell My Personal Information" link:

```html
<footer>
    <a href="#" onclick="showPrivacyManager()">Do Not Sell My Info</a>
</footer>
```

## Mobile Optimization

All ad placeholders are responsive. For mobile-specific ads:

```css
/* Desktop: 728x90 leaderboard */
@media (min-width: 769px) {
    .ad-mobile-only { display: none; }
}

/* Mobile: 320x50 mobile banner */
@media (max-width: 768px) {
    .ad-desktop-only { display: none; }
}
```

## Monitoring & Analytics

Track ad performance:

1. **Google Analytics 4**: Monitor revenue events
2. **Ad Network Dashboard**: Check impressions, CTR, RPM
3. **Page Speed Insights**: Monitor performance impact
4. **User Behavior**: Watch bounce rate changes after ad integration

## Quick Start Checklist

- [ ] Sign up for ad network (Google AdSense, Media.net, etc.)
- [ ] Get ad unit codes and publisher ID
- [ ] Replace placeholder divs with actual ad codes
- [ ] Test ads on desktop and mobile devices
- [ ] Verify ads display correctly without layout issues
- [ ] Implement consent management (GDPR/CCPA)
- [ ] Monitor performance and revenue
- [ ] A/B test different placements
- [ ] Optimize based on analytics data

## Support

For questions about:
- **Ad Integration**: Consult your ad network's documentation
- **Technical Issues**: Check browser console for errors
- **Layout Problems**: Adjust CSS in `css/style.css`
- **Revenue Optimization**: Review ad network best practices

---

**Last Updated**: February 2026  
**Compatible With**: Google AdSense, Media.net, PropellerAds, and other display ad networks
