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

if (!$company) {
    header("Location: " . $baseUrl . "error.html");
    exit();
}


$sql_services = $conn->prepare("SELECT * FROM services WHERE company_id = " . $company['id']);
$sql_services->execute();
$services = $sql_services->fetchAll(PDO::FETCH_ASSOC);

// Convertir work_days y blocked_dates a arrays
$work_days = explode(',', $company['work_days']);
$blocked_dates = explode(',', $company['blocked_dates']);

// Mapeo de días de la semana
$work_days_map = [
    "Sunday" => 0,
    "Monday" => 1,
    "Tuesday" => 2,
    "Wednesday" => 3,
    "Thursday" => 4,
    "Friday" => 5,
    "Saturday" => 6
];

// Convertir work_days a números
$work_days_numeric = array_map(function ($day) use ($work_days_map) {
    return $work_days_map[$day];
}, $work_days);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Incluir Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">

        <form id="appointmentForm" style="max-width: 600px; margin: 0 auto;">
            <!-- Mostrar el logo de la empresa si está disponible -->
            <?php if ($company && $company['logo']) : ?>
            <div class="mb-4">
                <img src="<?php echo $baseUrl . $company['logo']; ?>" alt="Logo de la Empresa" class="img-fluid w-25">
            </div>
            <?php endif; ?>
            <h2 class="text-center mb-4">Reservar Cita</h2>
            <div class="mb-3">
                <label for="service" class="form-label">Servicio:</label>
                <select id="service" name="service" class="form-select" onchange="getServiceCategory()" required>
                    <option value="" selected>Selecciona un servicio</option>
                    <?php foreach ($services as $service) : ?>
                    <option value="<?php echo $service['id']; ?>"
                        data-observation="<?php echo htmlspecialchars($service['observations']); ?>">
                        <?php echo htmlspecialchars($service['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div id="serviceObservation" class="mb-3 d-none">
                <span id="observation" class="form-control-plaintext bg-danger-subtle text-danger-emphasis p-2"></span>
            </div>

            <div id="categoryContainer" class="mb-3 d-none">
                <label for="category" class="form-label">Categoría:</label>
                <select id="category" name="category" class="form-select" required>
                    <option value="" selected>Selecciona una categoría</option>
                </select>
            </div>

            <input type="hidden" name="company_id" id="company_id"
                value="<?php echo htmlspecialchars($company['id']); ?>">

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

            <div class="mb-3">
                <label for="date" class="form-label">Fecha:</label>
                <input type="date" id="date" name="date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="time" class="form-label">Hora:</label>
                <input type="hidden" name="schedule_mode" id="schedule_mode"
                    value="<?php echo htmlspecialchars($company['schedule_mode']); ?>">
                <select id="time" name="time" class="form-select" required>
                    <option value="" selected>Selecciona una hora</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Reservar</button>
        </form>

        <div id="response" class="mt-4"></div>
    </div>
    <script>
    // document.addEventListener("DOMContentLoaded", function() {
    //     const BASE_URL = "<?php echo $baseUrl; ?>";
    //     const workDays = <?php echo json_encode($work_days_numeric); ?>;
    //     const blockedDates = <?php echo json_encode($blocked_dates); ?>;
    //     const calendarDaysAvailable = <?php echo $company['calendar_days_available']; ?>;

    //     document.getElementById('service').addEventListener('change', function() {
    //         const serviceId = this.value;
    //         const companyId = document.getElementById('company_id').value;

    //         if (serviceId) {
    //             fetch(`${BASE_URL}get_days_availability.php`, {
    //                     method: 'POST',
    //                     headers: {
    //                         'Content-Type': 'application/json'
    //                     },
    //                     body: JSON.stringify({
    //                         service_id: serviceId,
    //                         calendar_days_available: calendarDaysAvailable,
    //                         company_id: companyId
    //                     })
    //                 })
    //                 .then(response => response.json())
    //                 .then(data => {
    //                     if (data.success) {
    //                         const availableDays = data.available_days;
    //                         flatpickr("#date", {
    //                             enableTime: false,
    //                             dateFormat: "Y-m-d",
    //                             minDate: "today",
    //                             maxDate: new Date().fp_incr(calendarDaysAvailable),
    //                             disable: [
    //                                 function(date) {
    //                                     const dayOfWeek = date.getDay();
    //                                     const isBlockedDay = blockedDates.includes(date
    //                                         .toISOString().split('T')[0]);
    //                                     const isAvailableDay = availableDays.includes(
    //                                         date.toISOString().split('T')[0]);
    //                                     return !workDays.includes(dayOfWeek) ||
    //                                         isBlockedDay || !isAvailableDay;
    //                                 }
    //                             ]
    //                         });
    //                     } else {
    //                         alert(data.message);
    //                     }
    //                 })
    //                 .catch(error => console.error('Error:', error));
    //         } else {
    //             flatpickr("#date", {
    //                 enableTime: false,
    //                 dateFormat: "Y-m-d",
    //                 minDate: "today",
    //                 maxDate: new Date().fp_incr(calendarDaysAvailable),
    //                 disable: [
    //                     function(date) {
    //                         const dayOfWeek = date.getDay();
    //                         const isBlockedDay = blockedDates.includes(date.toISOString()
    //                             .split('T')[0]);
    //                         return !workDays.includes(dayOfWeek) || isBlockedDay;
    //                     }
    //                 ]
    //             });
    //         }
    //     });
    // });

    document.getElementById('service').addEventListener('change', function() {
        getServiceObservation();
        getServiceCategory();
    });

    function getServiceObservation() {
        var serviceSelect = document.getElementById('service');
        var observation = serviceSelect.options[serviceSelect.selectedIndex].getAttribute('data-observation');
        var observationField = document.getElementById('serviceObservation');
        var observationSpan = document.getElementById('observation');

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
                if (data.success) {
                    var categories = data.categories;
                    var select = document.getElementById('category');
                    document.getElementById('categoryContainer').classList.remove('d-none');
                    select.innerHTML = '<option value="" selected>Selecciona una categoría</option>';
                    categories.forEach(function(category) {
                        var option = document.createElement('option');
                        option.value = category.id;
                        option.text = category.category_name;
                        select.appendChild(option);
                    });
                } else {
                    document.getElementById('categoryContainer').classList.add('d-none');
                }
            })
            .catch(error => console.error('Error:', error));
    }
    </script>
    <script src="<?php echo $baseUrl; ?>assets/js/form.js"></script>
    <!-- Incluir Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>