User-agent: *
Allow: /

# Crawl delay to be respectful of server resources
Crawl-delay: 1

# Disallow admin areas
Disallow: /admin/
Disallow: /organizer/

# Disallow user account areas
Disallow: /dashboard
Disallow: /settings/
Disallow: /bookings/
Disallow: /connections/
Disallow: /profile/edit
Disallow: /favorites/
Disallow: /notifications/

# Disallow authentication pages
Disallow: /login
Disallow: /register
Disallow: /password/
Disallow: /email/verify
Disallow: /logout

# Disallow API endpoints
Disallow: /api/

# Disallow search results with parameters (to avoid duplicate content)
Disallow: /*?*utm_
Disallow: /*?*sort=
Disallow: /*?*page=

# Allow public areas explicitly
Allow: /events
Allow: /events/*
Allow: /help
Allow: /help/*
Allow: /badges
Allow: /badges/*
Allow: /*.css
Allow: /*.js
Allow: /images/
Allow: /storage/

# Sitemap location
Sitemap: {{ url('/sitemap.xml') }}

# Additional sitemaps
Sitemap: {{ url('/sitemap-static.xml') }}
Sitemap: {{ url('/sitemap-events.xml') }}
Sitemap: {{ url('/sitemap-categories.xml') }}
Sitemap: {{ url('/sitemap-organizers.xml') }}

