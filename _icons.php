<?php

function app_icon(string $name, string $class = "icon"): string
{
    $paths = [
        "grid" => '<rect x="3.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.2"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.2"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.2"/>',
        "package" => '<path d="m12 3 8.5 4.5v9L12 21l-8.5-4.5v-9L12 3Z"/><path d="m3.8 7.7 8.2 4.5 8.2-4.5M12 21v-8.8M7.8 5.2l8.4 4.6"/>',
        "folder" => '<path d="M3.5 6.5h6l2 2h9v9a2 2 0 0 1-2 2h-13a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2Z"/><path d="M3.5 9h17"/>',
        "arrows" => '<path d="m8 7 4-4 4 4M12 3v18m-4-4 4 4 4-4"/>',
        "warning" => '<path d="m10.3 4.4-7 12.1A1.7 1.7 0 0 0 4.8 19h14.4a1.7 1.7 0 0 0 1.5-2.5l-7-12.1a1.9 1.9 0 0 0-3.4 0Z"/><path d="M12 9v4m0 3h.01"/>',
        "logout" => '<path d="M10 17l5-5-5-5m5 5H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>',
        "chart" => '<path d="M4 19.5h16"/><rect x="5.5" y="11" width="3" height="6.5" rx=".7"/><rect x="10.5" y="7.5" width="3" height="10" rx=".7"/><rect x="15.5" y="4.5" width="3" height="13" rx=".7"/>',
        "wallet" => '<rect x="3" y="5" width="18" height="15" rx="2"/><path d="M3 8h18m-5 6h2"/>',
        "ban" => '<circle cx="12" cy="12" r="9"/><path d="m5.6 5.6 12.8 12.8"/>',
        "plus" => '<path d="M12 5v14m-7-7h14"/>',
        "minus" => '<path d="M5 12h14"/>',
        "check" => '<path d="m5 12 4.5 4.5L19 7"/>',
        "user" => '<circle cx="12" cy="8" r="3.5"/><path d="M5 20a7 7 0 0 1 14 0"/>',
    ];

    if (!isset($paths[$name])) {
        return "";
    }

    return '<svg class="' . htmlspecialchars($class, ENT_QUOTES, "UTF-8") . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths[$name] . '</svg>';
}
