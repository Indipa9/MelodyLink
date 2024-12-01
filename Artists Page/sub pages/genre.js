const artistsByGenre = {
    "Electronic": ["Ashanthi De Alwis", "Ravihans Wetthasinghe", "Clifford Issac"],
    "Pop": ["Bathiya & Santhush (BNS)", "Ashanthi De Alwis", "Dilin Nadeeshan"],
    "Metal": ["Stigmata", "Paranoid Earthling", "Tantrum"],
    "Punk": ["Killfeed", "Hollowpoint", "The Soul"],
    "Latin": ["Bhathiya Jayakody", "Santhush Weeraman", "Shalitha Abeywickrama"],
    "Hip Hop": ["Iraj Weeraratne", "Krishan Maheson", "Kanchana Anuradhi"],
    "Rock": ["Chitral Somapala", "Indrachapa Liyanage", "Wasantha Dukgannarala"],
    "Country": ["Rookantha Gunathilake", "Keerthi Pasquel", "Clarence Wijewardena"],
    "European": ["N/A (Specific European genre artists not prominent in Sri Lanka)"],
    "Blues": ["Jerome Speldewinde", "Shehara Jayathilaka", "Tantrum (Crossover)"],
    "Classical": ["Rohan De Silva", "Sunil Santha", "Premasiri Khemadasa"],
    "Middle Eastern": ["Yohani (for her cover influences)", "Shihan Mihiranga"],
    "Asian": ["Yohani", "Dinesh Subasinghe", "Dushyanth Weeraman"],
    "R&B": ["Dinesh Kanagaratnam", "Shan Putha", "Iraj Weeraratne"],
    "Jazz": ["Jerome Speldewinde", "Georgina de Alwis", "Nimal Mendis"],
    "Religious": ["Chitral Somapala (for devotional)", "Shashika Nisansala", "Kasun Kalhara"],
    "African": ["No popular crossover into African beats in Sri Lanka currently"],
    "Reggae": ["Brown Boogie Nation", "TNL Onstage Fusion Acts", "Fun Loving Criminals"]
};


document.querySelectorAll('.genre-box').forEach(box => {
    box.addEventListener('click', () => {
        const genre = box.getAttribute('data-genre');
        const artistList = artistsByGenre[genre] || ["No artists available"];
        const artistListContainer = document.getElementById('artists');
        artistListContainer.innerHTML = artistList.map(artist => `<li>${artist}</li>`).join('');
        document.getElementById('artist-list').scrollIntoView({ behavior: "smooth" });
    });
});
