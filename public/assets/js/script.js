document.getElementById("search").addEventListener("input", (e) => {
    const searchTerm = e.target.value.toLowerCase();
    const movieItems = document.querySelectorAll(".movie-item");

    movieItems.forEach(item => {
        const title = item.querySelector("span").textContent.toLowerCase();
        if (title.includes(searchTerm)) {
            item.style.display = "flex";
        } else {
            item.style.display = "none";
        }
    });
});