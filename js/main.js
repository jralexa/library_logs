// Client-side utilities for the public log form.
// Update the date/time display in the Asia/Manila timezone.
function updateDateTime() {
    var now = new Date();
    var dateStr = '';

    try {
        var options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            timeZone: 'Asia/Manila'
        };
        dateStr = now.toLocaleDateString('en-PH', options);
    } catch (e) {
        dateStr = now.toDateString();
    }

    var dateEl = document.getElementById('currentDate');
    if (dateEl) {
        dateEl.textContent = dateStr;
    }
}

// Auto-hide flash message after 3 seconds.
var flash = document.getElementById('flashMessage');
if (flash) {
    setTimeout(function () {
        flash.style.transition = "opacity 0.5s ease";
        flash.style.opacity = "0";
        setTimeout(function () {
            if (flash.parentNode) {
                flash.parentNode.removeChild(flash);
            }
        }, 500);
    }, 3000);
}

function setClassVisibility(el, hidden) {
    if (!el) {
        return;
    }

    if (el.classList && el.classList.toggle) {
        el.classList.toggle('is-hidden', hidden);
        return;
    }

    var className = el.className || '';
    var hasClass = new RegExp('(^|\\s)is-hidden(\\s|$)').test(className);
    if (hidden && !hasClass) {
        el.className = (className + ' is-hidden').replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
    }
    if (!hidden && hasClass) {
        el.className = className.replace(/(^|\s)is-hidden(\s|$)/g, ' ').replace(/\s+/g, ' ').replace(/^\s+|\s+$/g, '');
    }
}

function syncOtherInput(selectEl) {
    var otherInputId = selectEl.getAttribute('data-other-input');
    if (!otherInputId) {
        return;
    }

    var otherInput = document.getElementById(otherInputId);
    if (!otherInput) {
        return;
    }

    var isOther = selectEl.value === 'Other';
    setClassVisibility(otherInput, !isOther);
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
    var selectEls = document.querySelectorAll('select[data-other-input]');
    for (var i = 0; i < selectEls.length; i += 1) {
        var selectEl = selectEls[i];
        syncOtherInput(selectEl);
        selectEl.addEventListener('change', (function (el) {
            return function () {
                syncOtherInput(el);
            };
        })(selectEl));
    }
}

function populatePersonnelOptions(clientTypeId) {
    var nameSelect = document.getElementById('name');
    if (!nameSelect) {
        return;
    }

    nameSelect.innerHTML = '<option value="" selected disabled>Select a name</option>';
    var personnel = (window.personnelMap && window.personnelMap[clientTypeId]) || [];

    for (var i = 0; i < personnel.length; i += 1) {
        var person = personnel[i];
        var option = document.createElement('option');
        option.value = String(person.id);
        option.textContent = person.full_name;
        option.setAttribute('data-position-title', person.position_title || '');
        nameSelect.appendChild(option);
    }

    var otherOption = document.createElement('option');
    otherOption.value = 'Other';
    otherOption.textContent = 'Other';
    nameSelect.appendChild(otherOption);
}

function syncPositionFromPersonnel() {
    var nameSelect = document.getElementById('name');
    var positionInput = document.getElementById('position');

    if (!nameSelect || !positionInput) {
        return;
    }

    var selectedOption = nameSelect.options[nameSelect.selectedIndex];
    if (!selectedOption || selectedOption.value === 'Other') {
        return;
    }

    var suggestedPosition = selectedOption.getAttribute('data-position-title');
    if (suggestedPosition) {
        positionInput.value = suggestedPosition;
    }
}

function bindClientTypePersonnelSelection() {
    var clientTypeSelect = document.getElementById('client_type');
    var nameSelect = document.getElementById('name');
    if (!clientTypeSelect || !nameSelect) {
        return;
    }

    clientTypeSelect.addEventListener('change', function () {
        populatePersonnelOptions(clientTypeSelect.value);
        nameSelect.value = '';
        syncOtherInput(nameSelect);
    });

    nameSelect.addEventListener('change', syncPositionFromPersonnel);
}

function setVisible(el, shouldShow) {
    if (!el) {
        return;
    }

    setClassVisibility(el, !shouldShow);
}

function populateSchoolOptions(districtId) {
    var schoolSelect = document.getElementById('school_id');
    if (!schoolSelect) {
        return;
    }

    var schools = (window.schoolMap && window.schoolMap[districtId]) || [];
    schoolSelect.innerHTML = '<option value="" selected disabled>Select a school</option>';

    for (var i = 0; i < schools.length; i += 1) {
        var school = schools[i];
        var option = document.createElement('option');
        option.value = String(school.id);
        option.textContent = school.name;
        schoolSelect.appendChild(option);
    }

    schoolSelect.disabled = schools.length === 0;
}

function applyOrganizationMode() {
    var organizationType = document.getElementById('organization_type');
    var districtGroup = document.getElementById('districtGroup');
    var schoolGroup = document.getElementById('schoolGroup');
    var districtSelect = document.getElementById('district_id');
    var schoolSelect = document.getElementById('school_id');
    var organizationNameGroup = document.getElementById('organizationNameGroup');
    var organizationNameInput = document.getElementById('organization_name');

    if (!organizationType || !districtSelect || !schoolSelect || !organizationNameInput) {
        return;
    }

    var mode = organizationType.value;
    var useDistrictSchool = mode === 'district_school';
    var useOtherOrganization = mode === 'other';

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
    var organizationType = document.getElementById('organization_type');
    var districtSelect = document.getElementById('district_id');

    if (!organizationType || !districtSelect) {
        return;
    }

    organizationType.addEventListener('change', applyOrganizationMode);
    districtSelect.addEventListener('change', function () {
        populateSchoolOptions(districtSelect.value);
    });

    applyOrganizationMode();
}

function bindPurposeChoice() {
    var purposeChoice = document.getElementById('purpose_choice');
    var purposeText = document.getElementById('purpose');
    if (!purposeChoice || !purposeText) {
        return;
    }

    purposeChoice.addEventListener('change', function () {
        var selected = purposeChoice.value;
        if (!selected) {
            return;
        }

        if (selected === 'Other') {
            purposeText.focus();
            return;
        }

        // Always replace textarea content when a predefined purpose is selected.
        purposeText.value = selected;
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
        bindOtherInputs();
        bindClientTypePersonnelSelection();
        bindOrganizationSelection();
        bindPurposeChoice();
    });
} else {
    bindOtherInputs();
    bindClientTypePersonnelSelection();
    bindOrganizationSelection();
    bindPurposeChoice();
}

// Run date update once (no live clock).
updateDateTime();
