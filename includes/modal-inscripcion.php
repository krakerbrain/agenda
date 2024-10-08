<!-- modal_form.php -->
<div class="modal fade" id="inscriptionModal" tabindex="-1" aria-labelledby="inscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="inscriptionModalLabel">Formulario de Inscripción</h5>
                    <small>Prueba Gratis por 15 dias. Después $9.000 al mes</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <form id="companyForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="business_name" class="form-label">Nombre del Negocio</label>
                        <input type="text" class="form-control" id="business_name" name="business_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="logo" class="form-label">Logo del Negocio</label>
                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="owner_name" class="form-label">Nombre del Dueño/Usuario Principal</label>
                        <input type="text" class="form-control" id="owner_name" name="owner_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo Electrónico</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Dirección (Opcional)</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Teléfono (Opcional)</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción de la Empresa (Opcional)</label>
                        <textarea class="form-control" id="description" name="description" maxlength="120"
                            rows="3"></textarea>
                        <small class="form-text text-muted">Máximo 120 caracteres.</small>
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>
            </div>
        </div>
    </div>
</div>