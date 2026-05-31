# Fix Blog 404 Error in Production

## Problem
Getting 404 error when clicking "Read more" button on blog posts in production:
```
https://cloudlablatam.tech/blogs/beneficios-empresariales-del-analisis-de-datos-con-power-bi-y-su-potencial-con-inteligencia-artificial
```

## Changes Made

### 1. Removed Route Validation Restriction
- **File**: `routes/web.php`
- **Change**: Removed `->where('slug', '[a-z0-9\-_]+')` constraint
- **Reason**: The regex was too restrictive and may have been blocking valid slugs

### 2. Enhanced Slug Matching
- **File**: `app/Http/Controllers/BlogController.php`
- **Changes**:
  - Added case-insensitive slug matching
  - Improved logging to help debug issues
  - Added fallback search methods

### 3. Better Error Logging
- Added detailed logging that includes:
  - Requested slug
  - Available slugs in database
  - IP address
  - Full URL
  - User agent

## Deployment Steps

1. **Upload the updated files to production:**
   - `routes/web.php`
   - `app/Http/Controllers/BlogController.php`

2. **Clear all caches on production:**
   ```bash
   php artisan route:clear
   php artisan config:clear
   php artisan view:clear
   php artisan cache:clear
   ```

3. **Verify the blog exists and is published:**
   ```bash
   php artisan tinker
   ```
   Then run:
   ```php
   $blog = \App\Models\Blog::where('slug', 'beneficios-empresariales-del-analisis-de-datos-con-power-bi-y-su-potencial-con-inteligencia-artificial')->first();
   if ($blog) {
       echo "Found: " . $blog->title . "\n";
       echo "Published: " . ($blog->is_published ? 'Yes' : 'No') . "\n";
       echo "Slug: " . $blog->slug . "\n";
   } else {
       echo "Blog not found\n";
       // List all published blogs
       \App\Models\Blog::where('is_published', true)->get(['id', 'title', 'slug'])->each(function($b) {
           echo $b->id . ": " . $b->title . " -> " . $b->slug . "\n";
       });
   }
   ```

4. **Check logs for debugging:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Common Issues and Solutions

### Issue 1: Blog exists but not published
**Solution**: Go to admin panel and publish the blog

### Issue 2: Slug mismatch
**Solution**: Check the exact slug in database vs URL. The new code handles case-insensitive matching.

### Issue 3: Route cache
**Solution**: Always clear route cache after deploying:
```bash
php artisan route:clear
```

### Issue 4: .htaccess issues
**Solution**: Verify `.htaccess` is working correctly. Test with:
```bash
curl -I https://cloudlablatam.tech/blogs
```

## Testing

After deployment, test with:
1. Visit blog listing page
2. Click "Read more" on any blog post
3. Verify the URL loads correctly
4. Check logs if 404 still occurs

## Additional Debugging

If the issue persists, check:
1. **Database**: Verify blog exists and `is_published = 1`
2. **Slug**: Compare exact slug in database vs URL (case-sensitive)
3. **Logs**: Check `storage/logs/laravel.log` for detailed error messages
4. **Server**: Verify mod_rewrite is enabled on Apache
5. **Permissions**: Check file permissions on production server
