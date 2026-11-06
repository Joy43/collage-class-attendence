/** fetch() wrapper that sends the Laravel CSRF token and asks for JSON. */
export function http(url, { method = 'GET' } = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    return fetch(url, {
        method,
        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token ?? '', 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin',
    }).then((response) => response.json());
}
