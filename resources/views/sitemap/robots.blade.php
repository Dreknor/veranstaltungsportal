User-agent: *
Allow: /

# Disallow admin areas
Disallow: /admin/
Disallow: /organizer/

# Disallow user account areas
Disallow: /dashboard
Disallow: /settings/
Disallow: /bookings/
Disallow: /connections/
Disallow: /profile/edit

# Disallow authentication pages
Disallow: /login
Disallow: /register
Disallow: /password/

# Disallow API endpoints
Disallow: /api/

# Allow public areas
Allow: /events
Allow: /help
Allow: /badges

# Sitemap location
Sitemap: {{ url('/sitemap.xml') }}

