<?php

$file = __DIR__ . '/_ide_helper.php';

if (!file_exists($file)) {
    echo "File not found\n";
    exit(1);
}

// Ensure fresh state
shell_exec('php artisan ide-helper:generate');

$content = file_get_contents($file);

$replacements = [
    // Pseudotypes and unknown types
    '/[a-zA-Z0-9_\\\\]*never\b/' => 'never',
    '/[a-zA-Z0-9_\\\\]*non-negative-int\b/' => 'int',
    '/[a-zA-Z0-9_\\\\]*uppercase-string\b/' => 'string',
    '/[a-zA-Z0-9_\\\\]*SessionNotFoundException\b/' => '\Exception',
    '/[a-zA-Z0-9_\\\\]*BadRequestException\b/' => '\Exception',
    '/[a-zA-Z0-9_\\\\]*SuspiciousOperationException\b/' => '\Exception',
    '/[a-zA-Z0-9_\\\\]*UnableToProvideChecksum\b/' => '\Exception',
    '/[a-zA-Z0-9_\\\\]*RawPushType\b/' => 'mixed',
    '/[a-zA-Z0-9_\\\\]*Pusher\\\\Pusher\b/' => 'mixed',
    '/[a-zA-Z0-9_\\\\]*Ably\\\\AblyRest\b/' => 'mixed',
    '/[a-zA-Z0-9_\\\\]*Support\\\\Facades\\\\Collection\b/' => '\Illuminate\Support\Collection',
    '/[a-zA-Z0-9_\\\\]*Events\\\\Queued\\\\Closure\b/' => '\Closure',
    '/[a-zA-Z0-9_\\\\]*Livewire\\\\Testable\b/' => '\Livewire\Features\SupportTesting\Testable',
    '/[a-zA-Z0-9_\\\\]*Filesystem\\\\FilesystemManager\b/' => 'mixed',
];
$content = preg_replace(array_keys($replacements), array_values($replacements), $content);

$returnReplacements = [
    '/@return\s+[a-zA-Z0-9_\\\\]*Support\\\\Facades\\\\App\b/' => '@return \Illuminate\Foundation\Application',
    '/@return\s+[a-zA-Z0-9_\\\\]*Support\\\\Facades\\\\Cache\b/' => '@return \Illuminate\Cache\Repository',
    '/@return\s+[a-zA-Z0-9_\\\\]*Support\\\\Facades\\\\Context\b/' => '@return \Illuminate\Log\Context\Repository',
    '/@return\s+[a-zA-Z0-9_\\\\]*Support\\\\Facades\\\\Gate\b/' => '@return \Illuminate\Auth\Access\Gate',
    '/@return\s+[a-zA-Z0-9_\\\\]*Support\\\\Facades\\\\Lang\b/' => '@return \Illuminate\Translation\Translator',
    '/@return\s+[a-zA-Z0-9_\\\\]*Support\\\\Facades\\\\Request\b/' => '@return \Illuminate\Http\Request',
];
$content = preg_replace(array_keys($returnReplacements), array_values($returnReplacements), $content);

// Eloquent Builder mismatch
$content = str_replace('/** @var \Illuminate\Database\Query\Builder $instance */', '/** @var \Illuminate\Database\Eloquent\Builder $instance */', $content);

// applyScopes, setModel returning Eloquent
$content = str_replace('@return \Eloquent', '@return \Illuminate\Database\Eloquent\Builder', $content);
$content = str_replace('@return Eloquent', '@return \Illuminate\Database\Eloquent\Builder', $content);

// Fix dd and abort
$content = str_replace('@return \Illuminate\Support\Traits\never', '@return never', $content);
$content = str_replace('@return \Illuminate\Database\Query\never', '@return never', $content);

file_put_contents($file, $content);
echo "Refixed _ide_helper.php\n";
