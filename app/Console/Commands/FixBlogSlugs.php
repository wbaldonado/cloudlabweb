<?php

namespace App\Console\Commands;

use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class FixBlogSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:fix-slugs {--dry-run : Show what would be changed without actually changing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix blog slugs by removing HTML tags and ensuring they are URL-friendly';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $this->info('Scanning blogs for slugs with HTML tags...');
        
        $blogs = Blog::all();
        $fixed = 0;
        $needsFix = [];
        
        foreach ($blogs as $blog) {
            $originalSlug = $blog->slug;
            $cleanSlug = Blog::cleanSlug($originalSlug);
            
            // Check if slug needs fixing
            if ($originalSlug !== $cleanSlug || $this->hasHtmlTags($originalSlug)) {
                $needsFix[] = [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'old_slug' => $originalSlug,
                    'new_slug' => $cleanSlug ?: Blog::cleanSlug($blog->title),
                ];
            }
        }
        
        if (empty($needsFix)) {
            $this->info('✓ All blog slugs are clean!');
            return 0;
        }
        
        $this->warn("Found " . count($needsFix) . " blog(s) with problematic slugs:");
        $this->newLine();
        
        foreach ($needsFix as $item) {
            $this->line("ID: {$item['id']} - {$item['title']}");
            $this->line("  Old: {$item['old_slug']}");
            $this->line("  New: {$item['new_slug']}");
            $this->newLine();
        }
        
        if ($dryRun) {
            $this->info('Dry run mode - no changes made. Remove --dry-run to apply fixes.');
            return 0;
        }
        
        if (!$this->confirm('Do you want to fix these slugs?', true)) {
            $this->info('Operation cancelled.');
            return 0;
        }
        
        $this->info('Fixing slugs...');
        
        foreach ($needsFix as $item) {
            $blog = Blog::find($item['id']);
            if ($blog) {
                $newSlug = $item['new_slug'];
                
                // Ensure unique slug
                $baseSlug = $newSlug;
                $counter = 1;
                while (Blog::where('slug', $newSlug)->where('id', '!=', $blog->id)->exists()) {
                    $newSlug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $blog->slug = $newSlug;
                $blog->save();
                
                $this->line("✓ Fixed blog ID {$blog->id}: '{$item['old_slug']}' → '{$newSlug}'");
                $fixed++;
            }
        }
        
        $this->newLine();
        $this->info("✓ Successfully fixed {$fixed} blog slug(s)!");
        
        return 0;
    }
    
    /**
     * Check if string contains HTML tags
     *
     * @param string $string
     * @return bool
     */
    private function hasHtmlTags($string)
    {
        return $string !== strip_tags($string);
    }
}
