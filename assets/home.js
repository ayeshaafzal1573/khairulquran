    // BANNER SLIDER
            const upBtn = document.querySelector('.up')
            const downBtn = document.querySelector('.down')
            const sidebar = document.querySelector('.left-slider')
            const mainSlide = document.querySelector('.img-slide')
            const slidesCount = mainSlide.querySelectorAll('div').length
            const container = document.querySelector('.slide-con')

            let activeSlideIndex = 0

            sidebar.style.top = `-${(slidesCount - 1) * 100}vh`

            upBtn.addEventListener('click', () => {
                changeSlide('up')
            })

            downBtn.addEventListener('click', () => {
                changeSlide('down')
            })

            document.addEventListener('keydown', event => {
                if (event.key === 'ArrowUp') {
                    changeSlide('up')
                } else if (event.key === 'ArrowDown') {
                    changeSlide('down')
                }
            })

            function changeSlide(direction) {
                if (direction === 'up') {
                    activeSlideIndex++
                    if (activeSlideIndex === slidesCount) {
                        activeSlideIndex = 0
                    }
                } else if (direction === 'down') {
                    activeSlideIndex--
                    if (activeSlideIndex < 0) {
                        activeSlideIndex = slidesCount - 1
                    }
                }
                const height = container.clientHeight

                mainSlide.style.transform = `translateY(-${activeSlideIndex * height}px)`

                sidebar.style.transform = `translateY(${activeSlideIndex * height}px)`
}
            
// COUNTER
        
            let valueDisplays = document.querySelectorAll(".c-no");
            let interval = 22000;

            valueDisplays.forEach((valueDisplay) => {
                let startValue = 0;
                let endValue = parseInt(valueDisplay.getAttribute("data-val"));
                let duration = Math.floor(interval / endValue);
                let counter = setInterval(function() {
                    startValue += 1;
                    valueDisplay.textContent = startValue;
                    if (startValue == endValue) {
                        clearInterval(counter);
                    }
                }, duration);
            });
          // Gallery JavaScript
document.addEventListener("DOMContentLoaded", function () {
  const fullImgBox = document.getElementById("fullImgBox");
  const fullImg = document.getElementById("fullImg");
  const galleryProducts = document.querySelectorAll(".gallery-product");

  function openfullImg(pic) {
  fullImgBox.classList.add("responsive-full-img"); // Add responsive class
  fullImg.style.maxHeight = "100%"; // Allow image to take full height
  fullImgBox.style.display = "flex";
  fullImg.src = pic;
}

function closefullImg() {
  fullImgBox.classList.remove("responsive-full-img"); // Remove responsive class
  fullImgBox.style.display = "none";
  fullImg.style.maxHeight = "none"; // Reset max height
}


  // Attach click event listeners to each gallery product
  galleryProducts.forEach((product) => {
    const img = product.querySelector("img");
    img.addEventListener("click", () => {
      openfullImg(img.src);
    });
  });

  // Attach click event listener to close button
  const closeButton = document.querySelector(".full-img span");
  closeButton.addEventListener("click", closefullImg);
});
