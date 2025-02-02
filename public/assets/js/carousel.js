let slidePosition = 0;
const slides = document.getElementsByClassName('carouse__item');
const totalSlides = slides.length;

document.getElementById('carouse__buttons-next').addEventListener('click', () =>{
    moveToNextSlide();
});

document.getElementById('carouse__buttons-prev').addEventListener('click', () =>{
    moveToPrevSlide();
});

function updateSlidePosition(){
    for ( let slide of slides){
        slide.classList.remove('carouse__item--visible');
        slide.classList.add('carouse__item--hidden');
    }
    slides[slidePosition].classList.add('carouse__item--visible');
}

function moveToNextSlide(){
    
    if(slidePosition == totalSlides - 1){
        slidePosition = 0;
    } else{
        slidePosition++;
    }
    updateSlidePosition();
};

function moveToPrevSlide(){
    
    if(slidePosition == 0){
        slidePosition = totalSlides - 1;
    } else{
        slidePosition--;
    }
    updateSlidePosition();
};