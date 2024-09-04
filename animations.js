document.addEventListener('DOMContentLoaded', function() {
    // Fade in the logo and welcome message
    const logoContainer = document.querySelector('.logo-container');
    const welcomeMessage = document.querySelector('.welcome-message');

    logoContainer.style.opacity = 0;
    welcomeMessage.style.opacity = 0;

    setTimeout(() => {
        logoContainer.style.transition = 'opacity 1.5s ease-in-out';
        logoContainer.style.opacity = 1;
    }, 500);

    setTimeout(() => {
        welcomeMessage.style.transition = 'opacity 2s ease-in-out';
        welcomeMessage.style.opacity = 1;
    }, 1000);

    // Bounce animation for the download button
    const downloadBtn = document.querySelector('.download-btn');
    setInterval(() => {
        downloadBtn.classList.add('bounce');
        setTimeout(() => {
            downloadBtn.classList.remove('bounce');
        }, 1000);
    }, 3000);
});
