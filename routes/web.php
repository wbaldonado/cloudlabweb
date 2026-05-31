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
use App\Http\Controllers\Admin\CRM\CompanyController as CRMCompanyController;
use App\Http\Controllers\Admin\CRM\ContactController as CRMContactController;
use App\Http\Controllers\Admin\CRM\DealController as CRMDealController;

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

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/id/{id}', [BlogController::class, 'showById'])->name('blogs.show.by_id')->where('id', '[0-9]+');
Route::get('/blogs/{slug}', [BlogController::class, 'show'])->name('blogs.show');
Route::post('/blogs/{slug}/rate', [BlogController::class, 'rate'])->middleware('throttle:blog-interaction')->name('blogs.rate');
Route::post('/blogs/{slug}/comment', [BlogController::class, 'comment'])->middleware('throttle:blog-interaction')->name('blogs.comment');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{slug}', [ProjectController::class, 'show'])->name('projects.show');
Route::get('/technologies', [TechnologyController::class, 'index'])->name('technologies.index');
Route::get('/documentation', [DocumentationController::class, 'index'])->name('documentation.index');
Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:contact')->name('contact.store');

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'admin.timeout'])->group(function () {
    Route::get('/dashboard', function () {
        $blogCount = \App\Models\Blog::count();
        $projectCount = \App\Models\Project::count();
        $technologyCount = \App\Models\Technology::count();
        return view('admin.dashboard', compact('blogCount', 'projectCount', 'technologyCount'));
    })->name('dashboard');

    Route::resource('blogs', AdminBlogController::class);
    Route::post('blogs/upload-image', [AdminBlogController::class, 'uploadImage'])->name('blogs.upload-image');
    Route::resource('projects', AdminProjectController::class);
    Route::resource('technologies', AdminTechnologyController::class)->except(['show']);
    Route::resource('knowledge-base', AdminKnowledgeBaseController::class)->except(['show']);

    Route::get('blog-comments', [\App\Http\Controllers\Admin\BlogCommentController::class, 'index'])->name('blog-comments.index');
    Route::patch('blog-comments/{blogComment}/approve', [\App\Http\Controllers\Admin\BlogCommentController::class, 'approve'])->name('blog-comments.approve');
    Route::delete('blog-comments/{blogComment}', [\App\Http\Controllers\Admin\BlogCommentController::class, 'destroy'])->name('blog-comments.destroy');

    Route::get('settings/password', [AdminPasswordController::class, 'edit'])->name('password.edit');
    Route::put('settings/password', [AdminPasswordController::class, 'update'])->name('password.update');

    // CRM Routes
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::resource('companies', CRMCompanyController::class);
        Route::resource('contacts', CRMContactController::class);
        Route::resource('deals', CRMDealController::class);
    });
});
