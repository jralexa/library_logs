// Client-side utilities for the public log form.
// Update the date/time display in the Asia/Manila timezone.
function updateDateTime() {
    const now = new Date();
    
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric',
        timeZone: 'Asia/Manila'
    };

    const dateStr = now.toLocaleDateString('en-PH', options);
    document.getElementById('currentDate').textContent = dateStr;
}

// Auto-hide flash message after 3 seconds.
const flash = document.getElementById('flashMessage');
if (flash) {
    setTimeout(() => {
        flash.style.transition = "opacity 0.5s ease";
        flash.style.opacity = "0";
        setTimeout(() => flash.remove(), 500);
    }, 3000);
}

// Run date update once (no live clock).
updateDateTime();
