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

// Backward-compatible handler for existing inline onchange attributes.
function toggleOtherInput(selectEl) {
    syncOtherInput(selectEl);
}

function bindOtherInputs() {
    document.querySelectorAll('select[data-other-input]').forEach((selectEl) => {
        syncOtherInput(selectEl);
        selectEl.addEventListener('change', () => syncOtherInput(selectEl));
    });
}

function setVisible(el, shouldShow) {
    if (!el) {
        return;
    }

    el.classList.toggle('is-hidden', !shouldShow);
}

function populateSchoolOptions(districtId) {
    const schoolSelect = document.getElementById('school_id');
    if (!schoolSelect) {
        return;
    }

    const schools = (window.schoolMap && window.schoolMap[districtId]) || [];
    schoolSelect.innerHTML = '<option value="" selected disabled>Select a school</option>';

    schools.forEach((school) => {
        const option = document.createElement('option');
        option.value = String(school.id);
        option.textContent = school.name;
        schoolSelect.appendChild(option);
    });

    schoolSelect.disabled = schools.length === 0;
}

function applyOrganizationMode() {
    const organizationType = document.getElementById('organization_type');
    const districtGroup = document.getElementById('districtGroup');
    const schoolGroup = document.getElementById('schoolGroup');
    const districtSelect = document.getElementById('district_id');
    const schoolSelect = document.getElementById('school_id');
    const organizationNameGroup = document.getElementById('organizationNameGroup');
    const organizationNameInput = document.getElementById('organization_name');

    if (!organizationType || !districtSelect || !schoolSelect || !organizationNameInput) {
        return;
    }

    const mode = organizationType.value;
    const useDistrictSchool = mode === 'district_school';
    const useOtherOrganization = mode === 'other';

    setVisible(districtGroup, useDistrictSchool);
    setVisible(schoolGroup, useDistrictSchool);
    setVisible(organizationNameGroup, useOtherOrganization);

    districtSelect.disabled = !useDistrictSchool;
    districtSelect.required = useDistrictSchool;

    schoolSelect.required = useDistrictSchool;
    if (!useDistrictSchool) {
        districtSelect.value = '';
        schoolSelect.innerHTML = '<option value="" selected disabled>Select a school</option>';
        schoolSelect.disabled = true;
        schoolSelect.value = '';
    } else {
        populateSchoolOptions(districtSelect.value);
    }

    organizationNameInput.disabled = !useOtherOrganization;
    organizationNameInput.required = useOtherOrganization;
    if (!useOtherOrganization) {
        organizationNameInput.value = '';
    }
}

function bindOrganizationSelection() {
    const organizationType = document.getElementById('organization_type');
    const districtSelect = document.getElementById('district_id');

    if (!organizationType || !districtSelect) {
        return;
    }

    organizationType.addEventListener('change', applyOrganizationMode);
    districtSelect.addEventListener('change', () => {
        populateSchoolOptions(districtSelect.value);
    });

    applyOrganizationMode();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        bindOtherInputs();
        bindOrganizationSelection();
    });
} else {
    bindOtherInputs();
    bindOrganizationSelection();
}

// Run date update once (no live clock).
updateDateTime();
