<?php

require_once __DIR__ . '/classes/DatabaseSessionManager.php';
require_once __DIR__ . '/classes/ConfigUrl.php';
$manager = new DatabaseSessionManager();
$manager->startSession();
$conn = $manager->getDB();
$baseUrl = ConfigUrl::get();


$token = isset($_GET['path']) ? $_GET['path'] : null;
$sql = $conn->prepare("SELECT * FROM companies WHERE token = :token AND is_active = 1");
$sql->bindParam(':token', $token);
$sql->execute();
$company = $sql->fetch(PDO::FETCH_ASSOC);
$manager->setCompanyId($company['id']);
$primary_color = $company['primary_color'];
$secondary_color = $company['secondary_color'];
$background_color = $company['background_color'];

if (!$company) {
    header("Location: " . $baseUrl . "error.html");
    exit();
}

$sql_services = $conn->prepare("SELECT * FROM services WHERE company_id = " . $company['id']);
$sql_services->execute();
$services = $sql_services->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    body {
        background-color: <?php echo htmlspecialchars($background_color);
                            ?>;
    }

    .btn-primary {
        background-color: <?php echo htmlspecialchars($primary_color);
                            ?>;
        border-color: <?php echo htmlspecialchars($primary_color);
                        ?>;
    }

    .btn-secondary {
        background-color: <?php echo htmlspecialchars($secondary_color);
                            ?>;
        border-color: <?php echo htmlspecialchars($secondary_color);
                        ?>;
    }

    .form-control,
    .form-select {
        border-color: <?php echo htmlspecialchars($primary_color);
                        ?>;
    }

    .form-label {
        color: <?php echo htmlspecialchars($primary_color);
                ?>;
    }
</style>

<body>
    <div class="container mt-5">
        <form id="appointmentForm" style="max-width: 600px; margin: 0 auto;">
            <?php if ($company && $company['logo']) : ?>
                <div class="mb-4 d-flex justify-content-between align-items-baseline">
                    <img src="<?php echo $baseUrl . $company['logo']; ?>" alt="Logo de la Empresa" class="img-fluid w-25">
                    <h5 class="text-center mb-4">Reserva de Cita</h5>
                </div>
            <?php endif; ?>

            <div id="step1" class="step">
                <h2 class="text-center mb-4">Paso 1: Escoge el Servicio</h2>
                <div class="mb-3">
                    <label for="service" class="form-label">Servicio:</label>
                    <select id="service" name="service" class="form-select" onchange="getServiceCategory()" required>
                        <option value="" selected>Selecciona un servicio</option>
                        <?php foreach ($services as $service) : ?>
                            <option value="<?php echo $service['id']; ?>" data-observation="<?php echo htmlspecialchars($service['observations']); ?>">
                                <?php echo htmlspecialchars($service['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="serviceObservation" class="mb-3 d-none">
                    <span id="serviceTextObservation" class="form-control-plaintext bg-danger-subtle text-danger-emphasis p-2"></span>
                </div>
                <div id="categoryContainer" class="mb-3 d-none">
                    <label for="category" class="form-label">Categoría:</label>
                    <select id="category" name="category" class="form-select" required>
                        <option value="" selected>Selecciona una categoría</option>
                    </select>
                </div>
                <div id="categoryObservation" class="mb-3 d-none">
                    <span id="categoryTextObservation" class="form-control-plaintext bg-danger-subtle text-danger-emphasis p-2"></span>
                </div>

                <button type="button" class="btn btn-primary" onclick="showStep(2)">Siguiente</button>
            </div>

            <div id="step2" class="step d-none">
                <h2 class="text-center mb-4">Paso 2: Escoge Fecha y Hora</h2>
                <div class="mb-3">
                    <label for="date" class="form-label">Fecha:</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="time" class="form-label">Hora:</label>
                    <input type="hidden" name="schedule_mode" id="schedule_mode" value="<?php echo htmlspecialchars($company['schedule_mode']); ?>">
                    <select id="time" name="time" class="form-select" required>
                        <option value="" selected>Selecciona una hora</option>
                    </select>
                </div>
                <button type="button" class="btn btn-secondary" onclick="showStep(1)">Anterior</button>
                <button type="button" class="btn btn-primary" onclick="showStep(3)">Siguiente</button>
            </div>

            <div id="step3" class="step d-none">
                <h2 class="text-center mb-4">Paso 3: Llena tus Datos</h2>
                <input type="hidden" name="company_id" id="company_id" value="<?php echo htmlspecialchars($company['id']); ?>">
                <div class="mb-3">
                    <label for="name" class="form-label">Nombre:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Teléfono:</label>
                    <input type="tel" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="mail" class="form-label">Correo:</label>
                    <input type="email" id="mail" name="mail" class="form-control" required>
                </div>
                <button type="button" class="btn btn-secondary" onclick="showStep(2)">Anterior</button>
                <button type="submit" class="btn btn-primary">Reservar</button>
            </div>
        </form>
        <div id="response" class="mt-4"></div>
    </div>

    <script>
        function showStep(step) {
            document.querySelectorAll('.step').forEach(function(element) {
                element.classList.add('d-none');
            });
            document.getElementById('step' + step).classList.remove('d-none');
        }
        document.getElementById('service').addEventListener('change', function() {
            getObservation('service');
            getServiceCategory();
            getAvailableDays();
        });
        document.getElementById('category').addEventListener('change', function() {
            getObservation('category');
        });
        document.getElementById('date').addEventListener('change', function() {
            fetchAvailableTimes();
        });

        function getObservation(id) {
            var serviceSelect = document.getElementById(id);
            var observation = serviceSelect.options[serviceSelect.selectedIndex].getAttribute('data-observation');
            var observationField = document.getElementById(id + 'Observation');
            var observationSpan = document.getElementById(id + 'TextObservation');

            if (observation) {
                observationField.classList.remove('d-none');
                observationSpan.textContent = observation;
            } else {
                observationField.classList.add('d-none');
                observationSpan.textContent = '';
            }
        }

        function getServiceCategory() {
            var service_id = document.getElementById('service').value;
            var url = "<?php echo $baseUrl; ?>reservas/controller/reservaController.php";
            var data = {
                service_id: service_id,
            };


            fetch(url, {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    var select = document.getElementById('category');
                    if (data.success) {
                        var categories = data.categories;
                        document.getElementById('categoryContainer').classList.remove('d-none');
                        select.innerHTML = '<option value="" selected>Selecciona una categoría</option>';
                        categories.forEach(function(category) {
                            var option = document.createElement('option');
                            option.value = category.id;
                            option.text = category.category_name;
                            option.setAttribute('data-observation', category.category_description);
                            select.appendChild(option);
                        });
                    } else {
                        document.getElementById('categoryContainer').classList.add('d-none');
                        select.disabled = true;
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function getAvailableDays() {
            const BASE_URL = "<?php echo $baseUrl; ?>reservas/controller/";
            const calendarDaysAvailable = <?php echo $company['calendar_days_available']; ?>;
            const serviceId = document.getElementById('service').value;
            const companyId = document.getElementById('company_id').value;
            const url = BASE_URL + 'get_days_availability.php';

            const data = {
                service_id: serviceId,
                calendar_days_available: calendarDaysAvailable,
                company_id: companyId
            };

            fetch(url, {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const availableDates = data.available_days;
                        flatpickr("#date", {
                            enableTime: false,
                            dateFormat: "Y-m-d",
                            minDate: "today",
                            maxDate: new Date().fp_incr(
                                calendarDaysAvailable), // Puedes ajustar este valor según necesites
                            enable: [
                                function(date) {
                                    return availableDates.includes(date.toISOString().split('T')[0]);
                                }
                            ]
                        });
                    } else {
                        console.error('Error:', data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function fetchAvailableTimes() {
            const BASE_URL = "<?php echo $baseUrl; ?>reservas/controller/";
            const timeInput = document.getElementById("time");
            const companyID = document.getElementById("company_id").value;

            const date = document.getElementById("date").value;
            const serviceId = document.getElementById("service").value;
            const scheduleMode = document.getElementById("schedule_mode").value === "blocks" ?
                "get_available_hours_blocks.php" : "get_available_hours_free.php";

            if (date && serviceId) {
                fetch(BASE_URL + scheduleMode, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                        body: JSON.stringify({
                            date: date,
                            service_id: serviceId,
                            company_id: companyID
                        }),
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        timeInput.innerHTML = ""; // Clear previous options
                        if (data.success) {
                            if (data.available_times.length > 0) {
                                timeInput.innerHTML = '<option value="">Selecciona una hora</option>';
                                data.available_times.forEach((time) => {
                                    const option = document.createElement("option");
                                    option.value = `${time.start} - ${time.end}`;
                                    option.textContent = `${time.start} - ${time.end}`;
                                    timeInput.appendChild(option);
                                });
                            } else {
                                timeInput.innerHTML = '<option value="">No hay horas disponibles</option>';
                            }
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch((error) => console.error("Error:", error));
            }
        }

        document.getElementById("appointmentForm").addEventListener("submit", function(event) {
            event.preventDefault();
            const form = document.querySelector('#appointmentForm'); // Selecciona un formulario del DOM
            const formData = new FormData(form);
            const scheduleMode = document.getElementById("schedule_mode").value;

            if (scheduleMode === "blocks") {
                sendAppointment(formData);
            }
        });

        function sendAppointment(formData) {
            const BASE_URL = "<?php echo $baseUrl; ?>reservas/controller/";
            fetch(BASE_URL + "appointment.php", {
                    method: "POST",
                    body: formData,
                })
                .then((response) => {
                    if (!response.ok) {
                        return response.json().then((error) => {
                            throw new Error(error.message);
                        });
                    }
                    return response.json();
                })
                .then((data) => {
                    document.getElementById("response").innerText = data.message;
                    if (data.message === "Cita reservada exitosamente!") {
                        document.getElementById("appointmentForm").reset();
                    }
                })
                .catch((error) => {
                    document.getElementById("response").innerText = "Error al reservar la cita.";
                    console.error("Error:", error);
                });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>