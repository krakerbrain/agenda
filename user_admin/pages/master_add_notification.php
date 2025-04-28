<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-megaphone me-2"></i>Nueva Notificación</h4>
                </div>
                <div class="card-body">
                    <form id="notificationForm">
                        <div class="mb-3">
                            <label for="notificationType" class="form-label">Tipo</label>
                            <select class="form-select" id="notificationType" required>
                                <option value="feature">Nueva Funcionalidad</option>
                                <option value="bugfix">Corrección de Error</option>
                                <option value="announcement">Aviso Importante</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notificationTitle" class="form-label">Título</label>
                            <input type="text" class="form-control" id="notificationTitle" required maxlength="100">
                        </div>

                        <div class="mb-3">
                            <label for="notificationDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="notificationDescription" rows="5" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="notificationVersion" class="form-label">Versión (opcional)</label>
                            <input type="text" class="form-control" id="notificationVersion" placeholder="Ej: 1.2.0">
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="sendEmail">
                            <label class="form-check-label" for="sendEmail">Enviar por correo a todos los
                                usuarios</label>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="button" class="btn btn-secondary" onclick="history.back()">
                                <i class="bi bi-arrow-left me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i> Publicar Notificación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include dirname(__DIR__, 2) . '/includes/modal-info.php';
?>