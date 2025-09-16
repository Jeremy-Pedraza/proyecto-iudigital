@if (session('success'))
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="ri-check-line me-2"></i> Operación exitosa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-start">
                        <i class="ri-checkbox-circle-line me-2 ri-20px text-success"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.bootstrap) {
                const el = document.getElementById('successModal');
                if (el) new bootstrap.Modal(el).show();
            }
        });
    </script>
@endif
