document.addEventListener('DOMContentLoaded', () => {
    const video = document.getElementById('bg-video');
    const playPauseBtn = document.getElementById('play-pause-btn');
    const muteUnmuteBtn = document.getElementById('mute-unmute-btn');


    document.addEventListener('DOMContentLoaded', () => {
        const video = document.getElementById('bg-video');
        const nextArrow = document.getElementById('next-arrow');
    
        // List of video sources
        const videoSources = [
            "vチェンソーマンノンクレシットオーフニンク CHAINSAW MAN Opening米津玄師 KICK BACK - YouTub.mp4",
            "video2.mp4",
            "video3.mp4"
        ];
        let currentVideoIndex = 0;
    
        // Function to update video source
        const updateVideoSource = () => {
            video.src = videoSources[currentVideoIndex];
            video.play(); // Play the new video automatically
        };
    
        // Event listener for the next arrow
        nextArrow.addEventListener('click', () => {
            currentVideoIndex = (currentVideoIndex + 1) % videoSources.length;
            updateVideoSource();
        });
    });
    







    // Play/Pause functionality
    playPauseBtn.addEventListener('click', () => {
        if (video.paused) {
            video.play();
            playPauseBtn.textContent = 'Pause';
        } else {
            video.pause();
            playPauseBtn.textContent = 'Play';
        }
    });

    // Mute/Unmute functionality
    muteUnmuteBtn.addEventListener('click', () => {
        if (video.muted) {
            video.muted = false;
            muteUnmuteBtn.textContent = 'Mute';
        } else {
            video.muted = true;
            muteUnmuteBtn.textContent = 'Unmute';document.addEventListener('DOMContentLoaded', () => {
                const video = document.getElementById('bg-video');
                const playPauseBtn = document.getElementById('play-pause-btn');
                const muteUnmuteBtn = document.getElementById('mute-unmute-btn');
                const prevArrow = document.getElementById('prev-arrow');
                const nextArrow = document.getElementById('next-arrow');
                const sliderContent = document.querySelector('.slider-content');
                const sliderImages = document.querySelectorAll('.slider-image');
                let currentIndex = 0;
            
                // Play/Pause functionality
                playPauseBtn.addEventListener('click', () => {
                    if (video.paused) {
                        video.play();
                        playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                    } else {
                        video.pause();
                        playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                    }
                });
            
                // Mute/Unmute functionality
                muteUnmuteBtn.addEventListener('click', () => {
                    if (video.muted) {
                        video.muted = false;
                        muteUnmuteBtn.innerHTML = '<i class="fas fa-volume-up"></i>';
                    } else {
                        video.muted = true;
                        muteUnmuteBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                    }
                });
            
                // Slider functionality for scrolling through images
                const updateSlider = () => {
                    const sliderWidth = document.querySelector('.image-slider').clientWidth;
                    sliderContent.style.transform = `translateX(-${currentIndex * sliderWidth}px)`;
                };
            
                prevArrow.addEventListener('click', () => {
                    if (currentIndex > 0) {
                        currentIndex--;
                        updateSlider();
                    }
                });
            
                nextArrow.addEventListener('click', () => {
                    if (currentIndex < sliderImages.length - 1) {
                        currentIndex++;
                        updateSlider();
                    }
                });
            
                // Keyboard arrow keys functionality
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft') {
                        if (currentIndex > 0) {
                            currentIndex--;
                            updateSlider();
                        }
                    } else if (e.key === 'ArrowRight') {
                        if (currentIndex < sliderImages.length - 1) {
                            currentIndex++;
                            updateSlider();
                        }
                    }
                });
            });
            
        }
    });
});
