(function () {
    'use strict';

    var AJAX_ACTIONS = {
        add_district: true,
        update_district: true,
        add_school: true,
        update_school: true,
        add_client_type: true,
        update_client_type: true,
        add_personnel: true,
        update_personnel: true
    };

    function getAction(form) {
        var actionInput = form.querySelector('input[name="action"]');
        return actionInput ? actionInput.value : '';
    }

    function findSubmitControls(form) {
        var controls = [];
        var id = form.getAttribute('id');

        if (id) {
            var linked = document.querySelectorAll('button[form="' + id + '"], input[form="' + id + '"]');
            for (var i = 0; i < linked.length; i += 1) {
                controls.push(linked[i]);
            }
        }

        var nested = form.querySelectorAll('button[type="submit"], input[type="submit"]');
        for (var j = 0; j < nested.length; j += 1) {
            controls.push(nested[j]);
        }

        return controls;
    }

    function getPrimarySubmitter(form, submitter) {
        if (submitter) {
            return submitter;
        }

        var controls = findSubmitControls(form);
        return controls.length > 0 ? controls[0] : null;
    }

    function setControlsBusy(controls, isBusy) {
        for (var i = 0; i < controls.length; i += 1) {
            controls[i].disabled = isBusy;
        }
    }

    function ensureToastContainer() {
        var container = document.getElementById('toastContainer');
        if (container) {
            return container;
        }

        container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container';
        document.body.appendChild(container);

        return container;
    }

    function showToast(type, message) {
        var container = ensureToastContainer();
        if (!container) {
            return;
        }

        var normalizedType = (type === 'success' || type === 'error' || type === 'info') ? type : 'info';
        var toast = document.createElement('div');
        toast.className = 'toast toast-' + normalizedType;
        toast.textContent = message;
        container.appendChild(toast);

        window.setTimeout(function () {
            toast.classList.add('is-hiding');
        }, 2500);

        window.setTimeout(function () {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 2900);
    }

    function setSubmitterState(submitter, label, busy) {
        if (!submitter) {
            return;
        }

        if (!submitter.dataset.originalText) {
            submitter.dataset.originalText = submitter.textContent;
        }

        submitter.textContent = label;
        submitter.classList.toggle('is-busy', !!busy);
        if (busy) {
            submitter.classList.remove('is-saved');
        }
    }

    function resetSubmitterState(submitter) {
        if (!submitter) {
            return;
        }

        var originalText = submitter.dataset.originalText || 'Update';
        submitter.textContent = originalText;
        submitter.classList.remove('is-busy');
        submitter.classList.remove('is-saved');
    }

    function submitUpdateForm(form, submitter) {
        var activeSubmitter = getPrimarySubmitter(form, submitter);
        var controls = findSubmitControls(form);
        var formData = new FormData(form);
        var action = getAction(form);
        var isAddAction = action.indexOf('add_') === 0;

        if (form.dataset.requestInFlight === '1') {
            showToast('info', 'Update already in progress...');
            return Promise.resolve();
        }

        form.dataset.requestInFlight = '1';
        formData.append('ajax', '1');
        setControlsBusy(controls, true);
        setSubmitterState(activeSubmitter, 'Saving...', true);

        return fetch(window.location.href, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('Server returned ' + response.status);
            }
            return response.json();
        }).then(function (payload) {
            if (!payload || typeof payload.ok === 'undefined') {
                throw new Error('Invalid response payload.');
            }

            showToast(payload.type || (payload.ok ? 'success' : 'error'), payload.message || 'Update completed.');
            if (payload.ok) {
                setSubmitterState(activeSubmitter, 'Saved', false);
                if (activeSubmitter) {
                    activeSubmitter.classList.add('is-saved');
                }
                if (isAddAction) {
                    form.reset();
                }
            } else {
                resetSubmitterState(activeSubmitter);
            }
        }).catch(function (error) {
            showToast('error', 'Update failed. Please try again.');
            resetSubmitterState(activeSubmitter);
            console.error(error);
        }).finally(function () {
            form.dataset.requestInFlight = '0';
            setControlsBusy(controls, false);
            if (activeSubmitter && activeSubmitter.textContent === 'Saved') {
                window.setTimeout(function () {
                    resetSubmitterState(activeSubmitter);
                }, 1200);
            }
        });
    }

    function bindAjaxUpdates() {
        var forms = document.querySelectorAll('form[method="POST"], form[method="post"]');
        for (var i = 0; i < forms.length; i += 1) {
            var form = forms[i];
            var action = getAction(form);

            if (!AJAX_ACTIONS[action]) {
                continue;
            }

            form.addEventListener('submit', function (event) {
                event.preventDefault();
                submitUpdateForm(event.currentTarget, event.submitter || null);
            });
        }
    }

    function bindMasterTabs() {
        var tabs = document.querySelectorAll('.master-tab[data-tab-target]');
        var panels = document.querySelectorAll('.tab-panel[data-tab-panel]');
        if (tabs.length === 0 || panels.length === 0) {
            return;
        }

        function activateTab(target) {
            var i;
            var hasMatch = false;

            for (i = 0; i < tabs.length; i += 1) {
                if (tabs[i].dataset.tabTarget === target) {
                    hasMatch = true;
                    break;
                }
            }
            if (!hasMatch) {
                target = tabs[0].dataset.tabTarget;
            }

            for (i = 0; i < tabs.length; i += 1) {
                var activeTab = tabs[i].dataset.tabTarget === target;
                tabs[i].classList.toggle('is-active', activeTab);
                tabs[i].setAttribute('aria-selected', activeTab ? 'true' : 'false');
            }

            for (i = 0; i < panels.length; i += 1) {
                var activePanel = panels[i].dataset.tabPanel === target;
                panels[i].classList.toggle('is-active', activePanel);
                panels[i].hidden = !activePanel;
            }

            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, '', '#tab-' + target);
            } else {
                window.location.hash = 'tab-' + target;
            }
        }

        var hash = window.location.hash || '';
        var initialTarget = '';
        if (hash.indexOf('#tab-') === 0) {
            initialTarget = hash.slice(5);
        }
        activateTab(initialTarget || tabs[0].dataset.tabTarget);

        for (var i = 0; i < tabs.length; i += 1) {
            tabs[i].addEventListener('click', function (event) {
                activateTab(event.currentTarget.dataset.tabTarget);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            bindMasterTabs();
            bindAjaxUpdates();
        });
    } else {
        bindMasterTabs();
        bindAjaxUpdates();
    }
})();
