<?php
require_once dirname(__DIR__) . '/classes/DatabaseSessionManager.php';
require_once dirname(__DIR__) . '/classes/ConfigUrl.php';
$baseUrl = ConfigUrl::get();
$manager = new DatabaseSessionManager();
$manager->startSession();

$sesion = isset($_SESSION['company_id']);

if (!$sesion) {
    header("Location: " . $baseUrl . "login/index.php");
}

include $baseUrl . 'partials/head.php';
?>

<body>
    <!-- Cerrar sesion -->
    <div class="nav-container">
        <div class="logout">
            <a href="<?php echo $baseUrl; ?>login/logout.php">Cerrar sesión</a>
        </div>
        <nav class="navbar">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="#" class="nav-link" id="admin">
                        <i class="fas fa-calendar"></i>
                        <span>Lista Citas</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" id="configuracion">
                        <i class="fas fa-cog"></i>
                        <span>Configura Empresa</span>
                    </a>
                </li>
            </ul>
            <div class="navbar-toggle" id="navbar-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
        <div id="main-content"></div>
    </div>
    <script src="<?php echo $baseUrl; ?>assets/js/navbar.js"></script>
    <script>
        const BASE_URL = '<?php echo $baseUrl; ?>' + 'user_admin/';
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('admin').addEventListener('click', function(event) {
                event.preventDefault();
                loadContent('admin.php');
            });

            document.getElementById('configuracion').addEventListener('click', function(event) {
                event.preventDefault();
                loadContent('configuracion.php');
            });
        });

        function loadContent(url) {
            fetch(BASE_URL + url)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('main-content').innerHTML = data;
                    attachEventListeners(); // Attach event listeners after content is loaded
                })
                .catch(error => console.error('Error loading content:', error));
        }

        function attachEventListeners() {
            document.querySelectorAll('.confirm-button').forEach(button => {
                button.addEventListener('click', function() {
                    confirmReservation(this.dataset.id);
                });
            });

            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    deleteAppointment(this.dataset.eventId, this.dataset.id);
                });
            });

            const companyConfigForm = document.getElementById('companyConfigForm');
            if (companyConfigForm) {
                companyConfigForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    const formData = new FormData(this);
                    fetch(BASE_URL + 'update_company_config.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Configuración actualizada exitosamente');
                                location.reload();
                            } else {
                                alert('Error al actualizar la configuración');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                });
            }

            const addBlockedDate = document.getElementById('addBlockedDate');
            if (addBlockedDate) {
                addBlockedDate.addEventListener("click", function() {
                    try {
                        const container = document.getElementById("blockedDatesContainer");
                        const newDate = document.createElement("div");
                        newDate.className = "blocked-date";
                        newDate.style.display = "flex";
                        newDate.style.alignItems = "end";
                        newDate.innerHTML = `
                    <input type="date" name="blocked_dates[]" required>
                    <button type="button" class="remove-date" style="margin-left: 10px;">Eliminar</button>
                    `;
                        container.appendChild(newDate);
                    } catch (error) {
                        console.error("Error:", error);
                    }
                });

            }

            const blockedDatesContainer = document.getElementById('blockedDatesContainer');
            if (blockedDatesContainer) {
                blockedDatesContainer.addEventListener("click", function(e) {
                    if (e.target && e.target.classList.contains("remove-date")) {
                        e.target.parentElement.remove();
                    }
                });

            }
        }

        function confirmReservation(id) {
            fetch(BASE_URL + 'confirm.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        }
                    } else {
                        alert('Error desconocido al confirmar la reserva.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al confirmar la reserva.');
                });
        }

        function deleteAppointment(eventId, id) {
            fetch(BASE_URL + 'delete_calendar_event.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: id,
                        event_id: eventId
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data)
                    if (data.success) {
                        alert("Cita eliminada con éxito.");
                        location.reload();
                    } else {
                        alert("Error al eliminar la cita: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                });

        }
        document.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('delete-service')) {
                const serviceId = e.target.getAttribute('data-id');

                if (confirm('¿Seguro que quieres eliminar este servicio?')) {
                    fetch(BASE_URL + 'delete_service.php', {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                id: serviceId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Servicio eliminado exitosamente');
                                location.reload();
                            } else {
                                alert('Error al eliminar el servicio');
                            }
                        })
                        .catch(error => console.error('Error:', error));
                }
            }
        });
    </script>
</body>

</html>