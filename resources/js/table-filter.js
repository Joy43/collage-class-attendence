/*
 * Client-side row filter.
 *   <x-search-box data-filter=".row-selector" />   (wrapper carries the selector)
 *   <tr class="row-selector" data-search="lowercase searchable text">
 */
document.querySelectorAll('[data-filter]').forEach((box) => {
    const input = box.querySelector('input');
    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        document.querySelectorAll(box.dataset.filter).forEach((row) => {
            row.hidden = !row.dataset.search.includes(query);
        });
    });
});
