// count down finction 
document.addEventListener("DOMContentLoaded", function () {
  // Select all countdown elements
  const countdownElements = document.querySelectorAll(".wbgacountdown");

  countdownElements.forEach(function(countdownEl) {
    // Read the end time from data attribute (assumed to be UNIX timestamp in seconds)
    const endTime = parseInt(countdownEl.dataset.endTime, 10) * 1000;

    function updateCountdown() {
      const now = new Date().getTime();
      const distance = endTime - now;

      if (distance < 0) {
        countdownEl.innerHTML = "EXPIRED";
        clearInterval(interval);
        return;
      }

      const days = Math.floor(distance / (1000 * 60 * 60 * 24));
      const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      const seconds = Math.floor((distance % (1000 * 60)) / 1000);

      countdownEl.innerHTML = `
        <div class="time-block"><div class="value">${days}</div><div class="label">Days</div></div> 
        <div class="time-block"><div class="value">${hours}</div><div class="label">Hour</div></div>
        <div class="time-block"><div class="value">${minutes}</div><div class="label">Minutes</div></div>
        <div class="time-block"><div class="value">${seconds}</div><div class="label">Seconds</div></div>
      `;
    }

    updateCountdown();
    const interval = setInterval(updateCountdown, 1000);
  });
});

// js code for template two
function startCountdown(element, targetDate) {
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
    const countdownElements = document.querySelectorAll(".wbgacountdown_2");

    countdownElements.forEach(el => {
        let timestamp = el.getAttribute("data-timestamp");
        if (timestamp) {
            let targetDate = parseInt(timestamp, 10);

            // Convert from seconds to milliseconds if needed
            if (targetDate < 1000000000000) {
                targetDate *= 1000;
            }

            if (!isNaN(targetDate)) {
                startCountdown(el, targetDate);
            } else {
                el.innerHTML = "Invalid date";
            }
        } else {
            el.innerHTML = "Missing timestamp";
        }
    });
});
