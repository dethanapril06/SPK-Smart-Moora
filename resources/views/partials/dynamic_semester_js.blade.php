<script>
document.addEventListener('DOMContentLoaded', function() {
    function initDynamicSemesterDropdown(taSelect, semSelect) {
        if (!taSelect || !semSelect) return;

        // Remember initial placeholder text
        if (!semSelect._firstOptionText) {
            const firstOpt = semSelect.querySelector('option[value=""]');
            semSelect._firstOptionText = firstOpt ? firstOpt.textContent.trim() : 'Pilih Semester';
        }

        // Store original options if not stored yet
        if (!semSelect._allOptions) {
            semSelect._allOptions = [];
            Array.from(semSelect.options).forEach(opt => {
                if (opt.value !== '') {
                    const idTa = opt.getAttribute('data-id-ta') 
                              || opt.getAttribute('data-ta') 
                              || (opt.dataset ? (opt.dataset.idTa || opt.dataset.ta) : null);
                    semSelect._allOptions.push({
                        value: opt.value,
                        text: opt.textContent.trim(),
                        idTa: idTa ? String(idTa).trim() : '',
                        selected: opt.selected || opt.hasAttribute('selected')
                    });
                }
            });
        }

        function filterSem() {
            const selectedTa = String(taSelect.value || '').trim();
            const currentSem = String(semSelect.value || semSelect.getAttribute('data-selected') || '').trim();
            const placeholder = semSelect._firstOptionText || 'Pilih Semester';
            const isFilter = placeholder.toLowerCase().includes('semua');

            semSelect.innerHTML = '';
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = placeholder;
            semSelect.appendChild(defaultOpt);

            if (!selectedTa) {
                if (isFilter) {
                    semSelect.disabled = false;
                    semSelect._allOptions.forEach(o => {
                        const opt = document.createElement('option');
                        opt.value = o.value;
                        opt.textContent = o.text;
                        opt.setAttribute('data-id-ta', o.idTa);
                        opt.setAttribute('data-ta', o.idTa);
                        if (String(o.value) === currentSem) {
                            opt.selected = true;
                        }
                        semSelect.appendChild(opt);
                    });
                } else {
                    semSelect.disabled = false;
                    defaultOpt.textContent = '-- Pilih Tahun Ajaran Terlebih Dahulu --';
                }
            } else {
                semSelect.disabled = false;
                const validOpts = semSelect._allOptions.filter(o => o.idTa === selectedTa || !o.idTa);
                
                let matchedSelected = false;
                validOpts.forEach(o => {
                    const opt = document.createElement('option');
                    opt.value = o.value;
                    opt.textContent = o.text;
                    opt.setAttribute('data-id-ta', o.idTa);
                    opt.setAttribute('data-ta', o.idTa);
                    
                    if (String(o.value) === currentSem) {
                        opt.selected = true;
                        matchedSelected = true;
                    } else if (!matchedSelected && currentSem === '' && o.selected) {
                        opt.selected = true;
                        matchedSelected = true;
                    }
                    semSelect.appendChild(opt);
                });

                // If nothing was selected and we have valid options in a non-filter form (form create/edit),
                // auto-select the active or first option so user always has a valid selection
                if (!matchedSelected && !isFilter && validOpts.length > 0) {
                    const activeOpt = validOpts.find(o => o.selected) || validOpts[0];
                    if (activeOpt) {
                        semSelect.value = activeOpt.value;
                    }
                }
            }
        }

        // Attach event listener
        taSelect.removeEventListener('change', filterSem);
        taSelect.addEventListener('change', filterSem);

        // Run initially
        filterSem();
    }

    // Auto-detect and bind standard pairs across forms / page
    function bindAllDynamicDropdowns() {
        // 1. By data-dynamic-target attribute on TA select
        document.querySelectorAll('select[data-dynamic-target]').forEach(taSelect => {
            const targetSelector = taSelect.getAttribute('data-dynamic-target');
            const semSelect = document.querySelector(targetSelector);
            if (semSelect) initDynamicSemesterDropdown(taSelect, semSelect);
        });

        // 2. By form pairing (name="tahun_ajaran" / name="ta" paired with name="semester", or name="id_ta" paired with name="id_semester")
        document.querySelectorAll('form').forEach(form => {
            const taFilter = form.querySelector('select[name="tahun_ajaran"], select[name="ta"], select#filter_ta');
            const semFilter = form.querySelector('select[name="semester"], select#filter_semester');
            if (taFilter && semFilter) initDynamicSemesterDropdown(taFilter, semFilter);

            const taInput = form.querySelector('select[name="id_ta"]');
            const semInput = form.querySelector('select[name="id_semester"]');
            if (taInput && semInput) initDynamicSemesterDropdown(taInput, semInput);
        });

        // 3. By ID pairing outside form or in modals (#tahun_ajaran / #semester, #filter_ta / #filter_semester, #swal-ta / #swal-sem)
        const taById = document.getElementById('tahun_ajaran');
        const semById = document.getElementById('semester');
        if (taById && semById) initDynamicSemesterDropdown(taById, semById);

        const filterTaById = document.getElementById('filter_ta');
        const filterSemById = document.getElementById('filter_semester');
        if (filterTaById && filterSemById) initDynamicSemesterDropdown(filterTaById, filterSemById);

        const swalTa = document.getElementById('swal-ta');
        const swalSem = document.getElementById('swal-sem');
        if (swalTa && swalSem) initDynamicSemesterDropdown(swalTa, swalSem);
    }

    bindAllDynamicDropdowns();

    // Export global helpers so dynamic modals (like SweetAlert2 swal-ta/swal-sem) can call them anytime
    window.initDynamicSemesterDropdown = initDynamicSemesterDropdown;
    window.bindAllDynamicDropdowns = bindAllDynamicDropdowns;
});
</script>
