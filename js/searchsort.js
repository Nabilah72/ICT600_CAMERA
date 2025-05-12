
// Search filter
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function () {
    const filter = searchInput.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});

// Sortable table headers
document.querySelectorAll('th.sortable').forEach((header, columnIndex) => {
    let ascending = true;
    header.addEventListener('click', () => {
        const rows = Array.from(document.querySelectorAll('tbody tr'));
        rows.sort((a, b) => {
            const cellA = a.children[columnIndex].innerText.trim().toLowerCase();
            const cellB = b.children[columnIndex].innerText.trim().toLowerCase();

            if (!isNaN(cellA) && !isNaN(cellB)) {
                return ascending ? cellA - cellB : cellB - cellA;
            } else {
                return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            }
        });

        rows.forEach(row => row.parentElement.appendChild(row));
        ascending = !ascending;
    });
});
