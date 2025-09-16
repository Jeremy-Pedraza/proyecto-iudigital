@if ($errors->any())
    <div class="modal fade" id="errorsModal" tabindex="-1" aria-labelledby="errorsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="errorsModalLabel">
                        <i class="ri-alert-line me-2"></i> Se encontraron errores
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach ($errors->all() as $error)
                            <li class="list-group-item d-flex align-items-start">
                                <i class="ri-error-warning-line me-2 ri-20px text-danger"></i>
                                <span>{{ $error }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bootstrap 5: asume que el bundle ya está cargado por el layout Materio
            if (window.bootstrap) {
                const el = document.getElementById('errorsModal');
                if (el) new bootstrap.Modal(el).show();
            }
        });
    </script>
@endif
