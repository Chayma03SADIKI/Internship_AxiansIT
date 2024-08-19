document.addEventListener("DOMContentLoaded", function() {
    let slides = document.querySelectorAll(".slideshow .slide");
    let currentSlide = 0;

    function nextSlide() {
        slides[currentSlide].style.opacity = 0;
        slides[currentSlide].style.display = "none";
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].style.display = "block";
        slides[currentSlide].style.opacity = 1;
    }

    setInterval(nextSlide, 4000);
});
