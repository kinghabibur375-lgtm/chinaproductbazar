function toggleSearch() {
    const overlay = document.getElementById('searchOverlay');
    const input = document.getElementById('searchInput');
    overlay.classList.toggle('active');
    if(overlay.classList.contains('active')) {
        setTimeout(() => input.focus(), 100);
    }
}

document.getElementById('searchInput').addEventListener('keyup', function() {
    let query = this.value;
    let box = document.getElementById('searchResults');
    
    if(query.length > 1) {
        let formData = new FormData();
        formData.append('query', query);
        fetch('search_suggest.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(data => { box.innerHTML = data; });
    } else {
        box.innerHTML = '';
    }
});