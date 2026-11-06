<?php
/**
 * UI helpers — shared Tailwind class sets and small render functions,
 * so pages stay readable and a style change happens in one place.
 */

const UI = [
    // ── Buttons ──────────────────────────────────────────────
    'btn'          => 'inline-flex items-center justify-center gap-[7px] border border-transparent font-semibold leading-snug whitespace-nowrap cursor-pointer no-underline transition-all duration-150',
    'md'           => 'px-4 py-2 text-sm rounded-[10px]',
    'sm'           => 'px-2.5 py-[5px] text-[.8rem] rounded-md',
    'xs'           => 'px-2 py-[3px] text-xs rounded-[5px]',
    'lg'           => 'px-6 py-3 text-base rounded-[10px]',
    'full'         => 'w-full',
    'primary'      => 'bg-primary text-white shadow-sm hover:bg-primary-hover hover:-translate-y-px hover:shadow-md',
    'secondary'    => 'bg-surface text-fg !border-line shadow-sm hover:bg-subtle hover:!border-line-hover hover:-translate-y-px',
    'ghost'        => 'bg-transparent text-muted hover:bg-subtle hover:text-fg',
    'danger'       => 'bg-absent text-white hover:bg-red-700 hover:-translate-y-px',
    'danger_soft'  => 'bg-absent-soft text-absent !border-absent/20 hover:-translate-y-px',
    'success'      => 'bg-present text-white hover:bg-green-700 hover:-translate-y-px',
    'grad'         => 'bg-gradient-to-b from-blue-500 to-blue-600 text-white font-bold tracking-wide shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_2px_8px_rgba(37,99,235,.4)] hover:-translate-y-px hover:shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_6px_16px_rgba(37,99,235,.5)] active:scale-[.96]',
    'grad_green'   => 'bg-gradient-to-b from-green-500 to-green-600 text-white font-bold tracking-wide shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_2px_8px_rgba(22,163,74,.4)] hover:-translate-y-px hover:shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_6px_16px_rgba(22,163,74,.5)] active:scale-[.96]',
    'grad_red'     => 'bg-gradient-to-b from-red-500 to-red-600 text-white font-bold tracking-wide shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_2px_8px_rgba(220,38,38,.4)] hover:-translate-y-px hover:shadow-[inset_0_1px_1px_rgba(255,255,255,.3),0_6px_16px_rgba(220,38,38,.5)] active:scale-[.96]',
    'cancel'       => 'bg-transparent text-muted !border-line font-semibold hover:!border-red-500/30 hover:text-red-500 hover:bg-red-500/10 hover:-translate-y-px active:scale-[.96]',
    'icon_sm'      => 'flex h-[30px] w-[30px] items-center justify-center rounded-md border border-line bg-surface text-muted cursor-pointer transition-colors hover:bg-subtle hover:text-fg [&_svg]:h-3.5 [&_svg]:w-3.5',
    'icon_btn'     => 'relative flex h-[38px] w-[38px] items-center justify-center rounded-[10px] border border-transparent bg-transparent text-muted cursor-pointer transition-all duration-150 hover:bg-subtle hover:text-fg hover:border-line [&_svg]:h-[18px] [&_svg]:w-[18px]',

    // ── Forms ────────────────────────────────────────────────
    'label'        => 'text-[.8rem] font-semibold text-fg',
    'input'        => 'w-full rounded-[10px] border border-line bg-surface px-3 py-2 text-sm text-fg outline-none transition focus:border-primary focus:ring-[3px] focus:ring-primary/20',
    'select'       => 'w-full cursor-pointer appearance-none select-arrow rounded-[10px] border border-line bg-surface py-2 pl-3 pr-8 text-sm text-fg outline-none transition focus:border-primary focus:ring-[3px] focus:ring-primary/20',
    'field'        => 'flex flex-col gap-1.5',
    'form_grid'    => 'grid grid-cols-[repeat(auto-fit,minmax(200px,1fr))] gap-3.5',

    // ── Cards & tables ───────────────────────────────────────
    'card'         => 'overflow-hidden rounded-[14px] border border-line bg-surface shadow-sm',
    'card_header'  => 'flex items-center justify-between px-5 py-4 border-b border-line',
    'card_title'   => 'text-[.9rem] font-bold',
    'card_body'    => 'p-5',
    'card_footer'  => 'border-t border-line px-5 py-3 text-[.8rem] text-muted',
    'toolbar'      => 'flex flex-wrap items-center gap-2.5 border-b border-line px-4 py-3',
    'search'       => 'flex flex-grow max-w-[280px] items-center gap-2 rounded-[10px] border border-line bg-subtle px-3 py-1.5',
    'search_input' => 'w-full border-0 bg-transparent text-[.8rem] text-fg outline-none placeholder:text-faint',
    'table'        => 'w-full border-collapse',
    'th'           => 'sticky top-0 z-10 whitespace-nowrap border-b border-line bg-subtle px-4 py-[11px] text-left text-xs font-bold uppercase tracking-wider text-muted',
    'td'           => 'border-b border-line px-4 py-3 align-middle text-sm group-hover:bg-subtle group-last:border-b-0',
    'empty'        => 'px-6 py-14 text-center text-sm text-muted',

    // ── Modals ───────────────────────────────────────────────
    'modal'        => 'hidden fixed inset-0 z-[9999] items-center justify-center bg-black/45 backdrop-blur-sm animate-fade-in',
    'modal_box'    => 'scroll-thin max-h-[90vh] w-full overflow-y-auto rounded-[18px] bg-surface shadow-xl animate-slide-up',
    'modal_head'   => 'flex items-center justify-between border-b border-line px-[22px] py-[18px]',
    'modal_close'  => 'flex h-[30px] w-[30px] cursor-pointer items-center justify-center rounded-[10px] border-0 bg-subtle text-base text-muted transition-colors hover:bg-line hover:text-fg',
];

/**
 * Join class sets by key. Buttons get the default `md` size unless a size
 * (sm / xs / lg) is passed:  ui('btn','primary','sm')
 */
function ui(string ...$keys): string {
    if (in_array('btn', $keys, true) && !array_intersect($keys, ['sm', 'xs', 'lg', 'md'])) {
        $keys[] = 'md';
    }
    return implode(' ', array_map(fn($k) => UI[$k] ?? $k, $keys));
}

/** Gradient classes for avatars — rotate by index. */
function avatar_gradient(int $i = 0): string {
    static $set = [
        'from-indigo-500 to-blue-600',
        'from-amber-500 to-red-500',
        'from-emerald-500 to-sky-600',
        'from-violet-600 to-pink-500',
        'from-teal-500 to-cyan-500',
        'from-orange-500 to-amber-400',
    ];
    return $set[$i % count($set)];
}

/** Round gradient avatar showing $text. $size e.g. 'h-9 w-9 text-[.8rem]'. */
function avatar(string $text, string $size = 'h-[34px] w-[34px] text-[.8rem]', string $grad = 'from-indigo-500 to-blue-600', string $extra = ''): string {
    return '<div class="flex shrink-0 items-center justify-center rounded-full bg-gradient-to-br font-bold text-white '
        . "$size $grad $extra\">" . htmlspecialchars($text) . '</div>';
}

/** Status chip (Present / Absent / Late / Leave). */
function status_chip_classes(string $status): string {
    return match ($status) {
        'Present' => 'bg-present-soft text-present border-present/25',
        'Absent'  => 'bg-absent-soft text-absent border-absent/25',
        'Late'    => 'bg-late-soft text-late border-late/25',
        'Leave'   => 'bg-leave-soft text-leave border-leave/25',
        default   => 'bg-subtle text-muted border-line',
    };
}
const CHIP_BASE = 'inline-flex items-center gap-[5px] whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-semibold transition-all duration-150';

/** Flash / alert banner. $type: success | error | warning */
function alert_box(string $message, string $type = 'success'): string {
    $tone = match ($type) {
        'success' => 'bg-present-soft text-present border-present/25',
        'error'   => 'bg-absent-soft text-absent border-absent/25',
        default   => 'bg-late-soft text-late border-late/25',
    };
    $emoji = match ($type) { 'success' => '✅', 'error' => '❌', default => '⚠️' };
    return '<div class="mb-5 flex items-center gap-2.5 rounded-[10px] border px-4 py-3 text-sm font-medium ' . $tone . '">'
        . $emoji . ' <span>' . htmlspecialchars($message) . '</span></div>';
}

/** Breadcrumb + title (+ optional subtitle HTML). Echoes the block. */
function page_header(array $crumbs, string $title, string $subtitle = ''): void {
    echo '<div class="mb-6"><div class="mb-1.5 flex items-center gap-1.5 text-[.8rem] text-muted">';
    $last = count($crumbs) - 1;
    foreach ($crumbs as $i => $c) {
        echo $i === $last ? '<span class="text-fg">' . htmlspecialchars($c) . '</span>'
                          : '<span>' . htmlspecialchars($c) . '</span><span class="text-faint">›</span>';
    }
    echo '</div><h1 class="text-2xl font-extrabold tracking-tight">' . $title . '</h1>';
    if ($subtitle !== '') echo '<p class="mt-0.5 text-sm text-muted">' . $subtitle . '</p>';
    echo '</div>';
}

/** Search box used in table toolbars. */
function search_box(string $id, string $placeholder, string $onInput): string {
    return '<div class="' . ui('search') . '"><span class="h-3.5 w-3.5 shrink-0 text-faint">' . icon('search') . '</span>'
        . '<input type="text" id="' . $id . '" placeholder="' . htmlspecialchars($placeholder) . '" oninput="' . $onInput . '" class="' . ui('search_input') . '"></div>';
}

/** Icon wrapped in a sized span: icon_span('bell', 'h-[18px] w-[18px]') */
function icon_span(string $name, string $size = 'h-4 w-4', string $extra = ''): string {
    return '<span class="inline-flex shrink-0 [&_svg]:h-full [&_svg]:w-full ' . $size . ' ' . $extra . '">' . icon($name) . '</span>';
}
