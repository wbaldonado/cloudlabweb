<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\DocumentationController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\TechnologyController as AdminTechnologyController;
use App\Http\Controllers\Admin\PasswordController as AdminPasswordController;
use App\Http\Controllers\Admin\KnowledgeBaseController as AdminKnowledgeBaseController;
use App\Http\Controllers\Admin\CRM\ActivityController as CRMActivityController;
use App\Http\Controllers\Admin\CRM\CompanyController as CRMCompanyController;
use App\Http\Controllers\Admin\CRM\ContactController as CRMContactController;
use App\Http\Controllers\Admin\CRM\ContactEmailController as CRMContactEmailController;
use App\Http\Controllers\Admin\CRM\DashboardController as CRMDashboardController;
use App\Http\Controllers\Admin\CRM\DealController as CRMDealController;
use App\Http\Controllers\Admin\CRM\ImportController as CRMImportController;
use App\Http\Controllers\Admin\CRM\CampaignController as CRMCampaignController;
use App\Http\Controllers\Admin\AdminCrmController;
use App\Http\Controllers\CrmTrackingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WhatsAppWebhookController;

// CRM campaign tracking (public, no auth)
Route::get('/t/o/{token}', [CrmTrackingController::class, 'open'])->name('crm.track.open');
Route::get('/t/c/{token}', [CrmTrackingController::class, 'click'])->name('crm.track.click');
Route::get('/t/u/{token}', [CrmTrackingController::class, 'unsubscribe'])->name('crm.track.unsubscribe');

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Robots.txt and Sitemap
Route::get('/robots.txt', [\App\Http\Controllers\RobotsController::class, 'index'])->name('robots');

// Language Switcher
Route::get('/language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch');

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Meta WhatsApp Cloud API webhook (no auth / CSRF excluded)
Route::get('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
Route::post('/webhooks/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook.handle');

// Old unprefixed URLs permanently move to the Spanish canonical path.
// url() keeps the application base path (for example /cloudlablatam1/public on XAMPP).
Route::get('/', function () {
    return redirect(url('/es'), 301);
});

foreach ([
    'about',
    'contact',
    'technologies',
    'documentation',
    'cookies',
    'blogs',
    'projects',
    'servicios',
] as $legacyPage) {
    Route::get('/' . $legacyPage, function () use ($legacyPage) {
        return redirect(url('/es/' . $legacyPage), 301);
    });
}

Route::get('/blogs/id/{id}', function (string $id) {
    return redirect(url('/es/blogs/id/' . $id), 301);
})->where('id', '[0-9]+');

Route::get('/blogs/{slug}', function (string $slug) {
    return redirect(url('/es/blogs/' . $slug), 301);
})->where('slug', '[A-Za-z0-9\-]+');

Route::get('/projects/{slug}', function (string $slug) {
    return redirect(url('/es/projects/' . $slug), 301);
})->where('slug', '[A-Za-z0-9\-]+');

Route::get('/servicios/{slug}', function (string $slug) {
    return redirect(url('/es/servicios/' . $slug), 301);
})->where('slug', '[A-Za-z0-9\-]+');

// Public pages in /es and /en.
Route::prefix('{locale}')
    ->where(['locale' => 'es|en'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
        Route::get('/blogs/id/{id}', [BlogController::class, 'showById'])->name('blogs.show.by_id')->where('id', '[0-9]+');
        Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');
        Route::post('/blogs/{slug}/rate', [BlogController::class, 'rate'])->middleware('throttle:blog-interaction')->name('blogs.rate');
        Route::post('/blogs/{slug}/comment', [BlogController::class, 'comment'])->middleware('throttle:blog-interaction')->name('blogs.comment');
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('/technologies', [TechnologyController::class, 'index'])->name('public.technologies.index');
        Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
        Route::get('/cookies', [\App\Http\Controllers\CookiesController::class, 'index'])->name('cookies.index');
        Route::get('/about', [AboutController::class, 'index'])->name('about.index');
        Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
        Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');
        Route::get('/servicios', [ServiceController::class, 'index'])->name('services.index');
        Route::get('/servicios/{slug}', [ServiceController::class, 'show'])->name('services.show');
    });

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Admin Protected Routes (name('admin.') is required — without it, resource routes steal names like technologies.index)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'admin.timeout'])->group(function () {
    Route::get('/dashboard', function () {
        $blogCount = \Illuminate\Support\Facades\Schema::hasTable('blogs')
            ? \App\Models\Blog::count() : 0;
        $projectCount = \Illuminate\Support\Facades\Schema::hasTable('projects')
            ? \App\Models\Project::count() : 0;
        $technologyCount = \Illuminate\Support\Facades\Schema::hasTable('technologies')
            ? \App\Models\Technology::count() : 0;
        return view('admin.dashboard', compact('blogCount', 'projectCount', 'technologyCount'));
    })->name('dashboard');

    Route::resource('blogs', AdminBlogController::class);
    Route::post('blogs/upload-image', [AdminBlogController::class, 'uploadImage'])->name('blogs.upload-image');
    Route::post('projects/upload-image', [AdminProjectController::class, 'uploadImage'])->name('projects.upload-image');
    Route::resource('projects', AdminProjectController::class);
    Route::resource('technologies', AdminTechnologyController::class)->except(['show']);
    Route::resource('knowledge-base', AdminKnowledgeBaseController::class)->except(['show']);

    Route::get('blog-comments', [\App\Http\Controllers\Admin\BlogCommentController::class, 'index'])->name('blog-comments.index');
    Route::patch('blog-comments/{blogComment}/approve', [\App\Http\Controllers\Admin\BlogCommentController::class, 'approve'])->name('blog-comments.approve');
    Route::delete('blog-comments/{blogComment}', [\App\Http\Controllers\Admin\BlogCommentController::class, 'destroy'])->name('blog-comments.destroy');

    Route::get('settings/password', [AdminPasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [AdminPasswordController::class, 'update'])->name('password.update');

    // CRM — single dashboard (AdminCrmController = stable entry for Hostinger / cached routes)
    Route::get('crm', [AdminCrmController::class, 'index'])->name('crm.dashboard');
    Route::get('crm/companies', fn () => redirect()->route('admin.crm.dashboard'));
    Route::get('crm/contacts', fn () => redirect()->route('admin.crm.dashboard'));
    Route::get('crm/deals', fn () => redirect()->route('admin.crm.dashboard'));
    Route::post('crm/companies', [CRMCompanyController::class, 'store'])->name('crm.companies.store');
    Route::post('crm/companies/bulk-destroy', [CRMCompanyController::class, 'bulkDestroy'])->name('crm.companies.bulk-destroy');
    Route::put('crm/companies/{company}', [CRMCompanyController::class, 'update'])->name('crm.companies.update');
    Route::delete('crm/companies/{company}', [CRMCompanyController::class, 'destroy'])->name('crm.companies.destroy');
    Route::post('crm/contacts/{contact}/email', [CRMContactEmailController::class, 'store'])->name('crm.contacts.email.store');
    Route::get('crm/contacts/{contact}/campaigns', [CRMContactController::class, 'campaigns'])->name('crm.contacts.campaigns');
    Route::post('crm/contacts', [CRMContactController::class, 'store'])->name('crm.contacts.store');
    Route::post('crm/contacts/bulk-destroy', [CRMContactController::class, 'bulkDestroy'])->name('crm.contacts.bulk-destroy');
    Route::put('crm/contacts/{contact}', [CRMContactController::class, 'update'])->name('crm.contacts.update');
    Route::delete('crm/contacts/{contact}', [CRMContactController::class, 'destroy'])->name('crm.contacts.destroy');
    Route::post('crm/import', [CRMImportController::class, 'store'])->name('crm.import.store');
    Route::get('crm/import/template/{type}', [CRMImportController::class, 'template'])->name('crm.import.template');
    Route::get('crm/campaigns', [AdminCrmController::class, 'campaigns'])->name('crm.campaigns.index');
    Route::get('crm/campaigns/create', [AdminCrmController::class, 'campaignsCreate'])->name('crm.campaigns.create');
    Route::get('crm/campaigns/starters/{id}', [CRMCampaignController::class, 'loadStarterTemplate'])->name('crm.campaigns.starters.load');
    Route::post('crm/campaigns', [CRMCampaignController::class, 'store'])->name('crm.campaigns.store');
    Route::post('crm/campaigns/templates/upload', [CRMCampaignController::class, 'uploadTemplate'])->name('crm.campaigns.templates.upload');
    Route::get('crm/campaigns/templates/{slug}', [CRMCampaignController::class, 'loadTemplate'])->name('crm.campaigns.templates.load');
    Route::delete('crm/campaigns/templates/{slug}', [CRMCampaignController::class, 'destroyTemplate'])->name('crm.campaigns.templates.destroy');
    Route::post('crm/campaigns/media', [CRMCampaignController::class, 'uploadMedia'])->name('crm.campaigns.media.upload');
    Route::get('crm/campaigns/{campaign}', [CRMCampaignController::class, 'show'])->name('crm.campaigns.show');
    Route::post('crm/campaigns/{campaign}/send', [CRMCampaignController::class, 'send'])->name('crm.campaigns.send');
    Route::delete('crm/campaigns/{campaign}', [CRMCampaignController::class, 'destroy'])->name('crm.campaigns.destroy');
    Route::post('crm/deals', [CRMDealController::class, 'store'])->name('crm.deals.store');
    Route::put('crm/deals/{deal}', [CRMDealController::class, 'update'])->name('crm.deals.update');
    Route::delete('crm/deals/{deal}', [CRMDealController::class, 'destroy'])->name('crm.deals.destroy');
    Route::post('crm/activities', [CRMActivityController::class, 'store'])->name('crm.activities.store');
    Route::put('crm/activities/{activity}', [CRMActivityController::class, 'update'])->name('crm.activities.update');
    Route::delete('crm/activities/{activity}', [CRMActivityController::class, 'destroy'])->name('crm.activities.destroy');
});
