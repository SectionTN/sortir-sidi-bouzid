<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title.' - Sortir à Sidi Bouzid' : 'Sortir à Sidi Bouzid' }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-paper font-sans text-ink antialiased">
    <x-navbar />

    <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-10 sm:px-6 sm:py-14">
        @if (session('status'))
            <x-alert type="success" class="mb-8">{{ session('status') }}</x-alert>
        @endif

        {{ $slot }}
    </main>

    <footer class="border-t border-ink/10">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-2 px-4 py-8 text-sm text-stone-600 sm:px-6">
            <p>Sortir à Sidi Bouzid</p>
            <p>Atelier Framework Côté Serveur, ISET Sidi Bouzid</p>
        </div>
    </footer>
</body>
</html>
