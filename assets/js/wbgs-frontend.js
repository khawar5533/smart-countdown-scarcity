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
        <div class="wpgs-glasses-time-box"><div class="wpgs-glasses-time-box-min"><span class="wpgs-glasses-time">${days}</span></div><div class="wpgs-glasses-label">Days</div></div> 
        <div class="wpgs-glasses-time-box"><div class="wpgs-glasses-time-box-min"><span class="wpgs-glasses-time">${hours}</span></div><div class="wpgs-glasses-label">Hour</div></div>
        <div class="wpgs-glasses-time-box"><div class="wpgs-glasses-time-box-min"><span class="wpgs-glasses-time">${minutes}</span></div><div class="wpgs-glasses-label">Minutes</div></div>
        <div class="wpgs-glasses-time-box"><div class="wpgs-glasses-time-box-min"><span class="wpgs-glasses-time">${seconds}</span></div><div class="wpgs-glasses-label">Seconds</div></div>
      `;
    }

    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);
  });
});


// js code for template two
function wbgs_startCountdown(element, targetDate) {
    const interval = setInterval(() => {
        const now = Date.now();
        const distance = targetDate - now;

        if (distance < 0) {
            clearInterval(interval);
            element.innerHTML = "EXPIRED";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        element.innerHTML = `
            <div class="time-block"><div class="circle"><div class="value">${days}</div><div class="label">Days</div></div></div>
            <div class="time-block"><div class="circle"><div class="value">${hours}</div><div class="label">Hours</div></div></div>
            <div class="time-block"><div class="circle"><div class="value">${minutes}</div><div class="label">Minutes</div></div></div>
            <div class="time-block"><div class="circle"><div class="value">${seconds}</div><div class="label">Seconds</div></div></div>`;
    }, 1000);
}

document.addEventListener("DOMContentLoaded", function () {
    const countdownElements = document.querySelectorAll(".wbgs-sale-counter");

    countdownElements.forEach((el, index) => {
        let timestamp = el.getAttribute("data-timestamp");

        // Generate a unique class using a random number
        const randomId = Math.floor(Math.random() * 100000);
        const dynamicClass = `wbgs-sale-counter-${randomId}`;
        el.classList.add("dunsmic", dynamicClass);

        if (timestamp) {
            let targetDate = parseInt(timestamp, 10);

            // Convert from seconds to milliseconds if it's likely in seconds
            if (targetDate < 1000000000000) {
                targetDate *= 1000;
            }

            if (!isNaN(targetDate)) {
                wbgs_startCountdown(el, targetDate);
            } else {
                el.innerHTML = "Invalid date";
            }
        } else {
            el.innerHTML = "Missing timestamp";
        }
    });
});

