// count down finction 
document.addEventListener("DOMContentLoaded", function () {
  const countdownElements = document.querySelectorAll(".wbgscountdown");

  countdownElements.forEach(function (countdownEl, index) {
    // Generate random number for unique class (can also use Date.now() + index)
    const randomNum = Math.floor(Math.random() * 100000);
    const uniqueClass = `wbgscountdown-${randomNum}`;

    // Add the unique class to the element
    countdownEl.classList.add("dunsmic", uniqueClass);

    // Read the end time from the data attribute
    const endTime = parseInt(countdownEl.dataset.endTime, 10) * 1000;

    function updateCountdown() {
      const now = Date.now();
      const distance = endTime - now;

      if (distance <= 0) {
        countdownEl.innerHTML = "EXPIRED";
        clearInterval(interval);
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      countdownEl.innerHTML = `
        <div class="wbgs-glasses-time-box"><div class="wbgs-glasses-time-box-min"><span class="wbgs-glasses-time">${days}</span></div><div class="wbgs-glasses-label">Days</div></div> 
        <div class="wbgs-glasses-time-box"><div class="wbgs-glasses-time-box-min"><span class="wbgs-glasses-time">${hours}</span></div><div class="wbgs-glasses-label">Hour</div></div>
        <div class="wbgs-glasses-time-box"><div class="wbgs-glasses-time-box-min"><span class="wbgs-glasses-time">${minutes}</span></div><div class="wbgs-glasses-label">Minutes</div></div>
        <div class="wbgs-glasses-time-box"><div class="wbgs-glasses-time-box-min"><span class="wbgs-glasses-time">${seconds}</span></div><div class="wbgs-glasses-label">Seconds</div></div>
      `;
    }

    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);
  });
});


// js code for template two
document.addEventListener("DOMContentLoaded", function () {
  const countdownElements = document.querySelectorAll(".wbgscountdown-laptop");

  countdownElements.forEach(function (countdownEl, index) {
    // Generate random number for unique class (can also use Date.now() + index)
    const randomNum = Math.floor(Math.random() * 100000);
    const uniqueClass = `wbgscountdown-laptop-${randomNum}`;

    // Add the unique class to the element
    countdownEl.classList.add("dunsmic", uniqueClass);

    // Read the end time from the data attribute
    const endTime = parseInt(countdownEl.dataset.endTime, 10) * 1000;

    function updateCountdown() {
      const now = Date.now();
      const distance = endTime - now;

      if (distance <= 0) {
        countdownEl.innerHTML = "EXPIRED";
        clearInterval(interval);
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      countdownEl.innerHTML = `
        <div class="wbgs-laptop-time-box"><div class="wbgs-laptop-time-box-min"><span class="wbgs-laptop-time">${days}</span></div><div class="wbgs-laptop-label">Days</div></div>
        <div class="wbgs-laptop-time-box"><div class="wbgs-laptop-time-box-min"><span class="wbgs-laptop-time">${hours}</span></div><div class="wbgs-laptop-label">Hour</div></div>
        <div class="wbgs-laptop-time-box"><div class="wbgs-laptop-time-box-min"><span class="wbgs-laptop-time">${minutes}</span></div><div class="wbgs-laptop-label">Minutes</div></div>
        <div class="wbgs-laptop-time-box"><div class="wbgs-laptop-time-box-min"><span class="wbgs-laptop-time">${seconds}</span></div><div class="wbgs-laptop-label">Seconds</div></div>
      `;
    }

    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);
  });
});
//use to redirect when click on shop button 
jQuery(document).ready(function($) {
    $('.wbgs-glasses-shop-button').on('click', function(e) {
        e.preventDefault();
        var href = $(this).data('href');
        if (href) {
            window.location.href = href;
        }
    });
});