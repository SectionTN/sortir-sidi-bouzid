<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/a-propos', [PageController::class, 'about'])->name('about');

Route::get('/events/passes', [EventController::class, 'past'])->name('events.past');
Route::get('/events/brouillons', [EventController::class, 'drafts'])->name('events.drafts');

Route::get('/events/mois/{year}/{month}', [EventController::class, 'month'])
    ->whereNumber('year')
    ->where('month', '0?[1-9]|1[0-2]')
    ->name('events.month');

Route::get('/events/categorie/{category}', [EventController::class, 'byCategory'])->name('events.category');
Route::get('/events/lieu/{venue}', [EventController::class, 'byVenue'])->name('events.venue');
Route::get('/events/tag/{tag}', [EventController::class, 'byTag'])->name('events.tag');

Route::resource('events', EventController::class);

Route::patch('/events/{event}/publication', [EventController::class, 'togglePublication'])->name('events.publication');
