// List of music tracks
const tracks = [
    'track1.mp3',
    'track2.mp3',
    'track3.mp3'
];

let currentTrackIndex = 0; // Start with the first track
let isPlaying = false; // Track if audio is currently playing

const audioPlayer = document.getElementById('audio-player');
const playPauseButton = document.getElementById('play-pause-btn');
const prevButton = document.getElementById('prev-btn');
const nextButton = document.getElementById('next-btn');

// Load the initial track
audioPlayer.src = tracks[currentTrackIndex];

// Play or pause audio
playPauseButton.addEventListener('click', () => {
    if (isPlaying) {
        audioPlayer.pause();
        playPauseButton.innerHTML = '&#9654;'; // Show play icon
    } else {
        audioPlayer.play();
        playPauseButton.innerHTML = '&#10074;&#10074;'; // Show pause icon
    }
    isPlaying = !isPlaying;
});

// Go to the previous track
prevButton.addEventListener('click', () => {
    currentTrackIndex = (currentTrackIndex - 1 + tracks.length) % tracks.length;
    loadTrack(currentTrackIndex);
    audioPlayer.play();
    playPauseButton.innerHTML = '&#10074;&#10074;'; // Show pause icon
    isPlaying = true;
});

// Go to the next track
nextButton.addEventListener('click', () => {
    currentTrackIndex = (currentTrackIndex + 1) % tracks.length;
    loadTrack(currentTrackIndex);
    audioPlayer.play();
    playPauseButton.innerHTML = '&#10074;&#10074;'; // Show pause icon
    isPlaying = true;
});

// Load a track by index
function loadTrack(index) {
    audioPlayer.src = tracks[index];
    audioPlayer.load();
}

// Update the play button icon when audio ends
audioPlayer.addEventListener('ended', () => {
    playPauseButton.innerHTML = '&#9654;'; // Show play icon
    isPlaying = false;
});

