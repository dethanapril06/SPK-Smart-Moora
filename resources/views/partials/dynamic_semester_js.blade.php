<script>
document.addEventListener('DOMContentLoaded', function() {
    function initDynamicSemesterDropdown(taSelect, semSelect) {
        if (!taSelect || !semSelect) return;

        // Store original options if not stored yet
        if (!semSelect._allOptions) {
            semSelect._allOptions = [];
            Array.from(semSelect.options).forEach(opt => {
                if (opt.value !== '') {
                    semSelect._allOptions.push({
                        value: opt.value,
                        text: opt.text.trim(),
                        idTa: opt.getAttribute('data-id-ta') || opt.dataset.idTa,
                        selected: opt.selected || opt.hasAttribute('selected')
                    });
                }
            });
        }

        function filterSem() {
            const selectedTa = taSelect.value;
            const currentSem = semSelect.value || semSelect.getAttribute('data-selected') || '';
            
            semSelect.innerHTML = '<option value="">-- Pilih Semester --</option>';

            if (!selectedTa) {
                semSelect.disabled = true;
                semSelect.value = '';
            } else {
                semSelect.disabled = false;
                const validOpts = semSelect._allOptions.filter(o => o.idTa == selectedTa);
                validOpts.forEach(o => {
                    const opt = document.createElement('option');
                    opt.value = o.value;
                    opt.textContent = o.text;
                    opt.setAttribute('data-id-ta', o.idTa);
                    if (o.value == currentSem || (currentSem === '' && o.selected)) {
                        opt.selected = true;
                    }
                    semSelect.appendChild(opt);
                });
                
                // If after populating, current selection is invalid for this TA, clear value
                if (!semSelect.value && currentSem) {
                    semSelect.value = '';
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

        // 2. By form pairing (name="tahun_ajaran" paired with name="semester", or name="id_ta" paired with name="id_semester")
        document.querySelectorAll('form').forEach(form => {
            const taFilter = form.querySelector('select[name="tahun_ajaran"]');
            const semFilter = form.querySelector('select[name="semester"]');
            if (taFilter && semFilter) initDynamicSemesterDropdown(taFilter, semFilter);

            const taInput = form.querySelector('select[name="id_ta"]');
            const semInput = form.querySelector('select[name="id_semester"]');
            if (taInput && semInput) initDynamicSemesterDropdown(taInput, semInput);
        });

        // 3. By ID pairing outside form or in modals (#tahun_ajaran / #semester, #swal-ta / #swal-sem)
        const taById = document.getElementById('tahun_ajaran');
        const semById = document.getElementById('semester');
        if (taById && semById) initDynamicSemesterDropdown(taById, semById);

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
