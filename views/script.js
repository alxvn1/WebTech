const movies = [
    {
        title: "Interstellar",
        genre: "Sci-Fi",
        // poster: "https://m.media-amazon.com/images/M/MV5BYzdjMDAxZGItMjI2My00ODA1LTlkNzItOWFjMDU5ZDJlYWY3XkEyXkFqcGc@._V1_.jpg"
    },
    {
        title: "Inception",
        genre: "Sci-Fi",
        // poster: "https://m.media-amazon.com/images/M/MV5BMjAxMzY3NjcxNF5BMl5BanBnXkFtZTcwNTI5OTM0Mw@@._V1_FMjpg_UX1000_.jpg"
    },
    {
        title: "Fight Club",
        genre: "Drama",
        // poster: "https://m.media-amazon.com/images/M/MV5BOTgyOGQ1NDItNGU3Ny00MjU3LTg2YWEtNmEyYjBiMjI1Y2M5XkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg"
    },
    {
        title: "The Shawshank Redemption",
        genre: "Drama",
        // poster: "https://m.media-amazon.com/images/M/MV5BMDAyY2FhYjctNDc5OS00MDNlLThiMGUtY2UxYWVkNGY2ZjljXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg"
    },
    {
        title: "The Dark Knight",
        genre: "Action",
        // poster: "https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_FMjpg_UX1000_.jpg"
    }
];

function displayMovies(filter = "") {
    const list = document.getElementById("movieList");
    list.innerHTML = "";

    const filteredMovies = movies.filter(movie =>
        movie.title.toLowerCase().includes(filter.toLowerCase())
    );

    filteredMovies.forEach(movie => {
        const li = document.createElement("li");
        li.className = "movie-item";

        const img = document.createElement("img");
        img.src = movie.poster;
        img.alt = `${movie.title} Poster`;
        li.appendChild(img);

        const text = document.createElement("span");
        text.textContent = `${movie.title} (${movie.genre})`;
        li.appendChild(text);

        list.appendChild(li);
    });
}

document.getElementById("search").addEventListener("input", (e) => {
    displayMovies(e.target.value);
});

displayMovies();
