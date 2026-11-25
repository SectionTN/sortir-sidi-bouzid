<?php

it('uses the Tunis time zone and French', function () {
    $this->travelTo('2026-12-05 10:00');

    expect(config('app.timezone'))->toBe('Africa/Tunis')
        ->and(app()->getLocale())->toBe('fr')
        ->and(now()->translatedFormat('l j F'))->toBe('samedi 5 décembre');
});
