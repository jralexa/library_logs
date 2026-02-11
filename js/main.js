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

function syncOtherInput(selectEl) {
    const otherInputId = selectEl.dataset.otherInput;
    if (!otherInputId) {
        return;
    }

    const otherInput = document.getElementById(otherInputId);
    if (!otherInput) {
        return;
    }

    const isOther = selectEl.value === 'Other';
    otherInput.classList.toggle('is-hidden', !isOther);
    otherInput.required = isOther;

    if (!isOther) {
        otherInput.value = '';
    }
}

function bindOtherInputs() {
    document.querySelectorAll('select[data-other-input]').forEach((selectEl) => {
        syncOtherInput(selectEl);
        selectEl.addEventListener('change', () => syncOtherInput(selectEl));
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bindOtherInputs);
} else {
    bindOtherInputs();
}

// Run date update once (no live clock).
updateDateTime();
