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
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/form.css">
    <!-- Incluir Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <!-- Incluir jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="form-container">
        <?php if ($company && $company['logo']) : ?>
            <div>
                <img src="<?php echo $baseUrl . $company['logo']; ?>" alt="Logo de la Empresa">
            </div>
        <?php endif; ?>
        <h2>Reservar Cita</h2>
        <form id="appointmentForm" style="max-width: 400px;">
            <label for="service">Servicio:</label>
            <select id="service" name="service" required>
                <option value="">Selecciona un servicio</option>
                <?php foreach ($services as $service) : ?>
                    <option value="<?php echo $service['id']; ?>"><?php echo $service['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <input type="hidden" name="company_id" id="company_id" value="<?php echo $company['id']; ?>">
            <label for="name">Nombre:</label>
            <input type="text" id="name" name="name" required>

            <label for="phone">Teléfono:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="mail">Correo:</label>
            <input type="email" id="mail" name="mail" required>

            <label for="date">Fecha:</label>
            <input type="text" id="date" name="date" placeholder="Selecciona una fecha">

            <label for="time">Hora:</label>
            <input type="hidden" name="schedule_mode" id="schedule_mode" value="<?php echo $company['schedule_mode']; ?>">
            <select id="time" name="time" required>
                <option value="">Selecciona una hora</option>
            </select>
            <button class="submit-btn" type="submit">Reservar</button>
        </form>
        <div id="response"></div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const BASE_URL = "<?php echo $baseUrl; ?>";
            const workDays = <?php echo json_encode($work_days_numeric); ?>;
            const blockedDates = <?php echo json_encode($blocked_dates); ?>;
            const calendarDaysAvailable = <?php echo $company['calendar_days_available']; ?>;

            document.getElementById('service').addEventListener('change', function() {
                const serviceId = this.value;
                const companyId = document.getElementById('company_id').value;

                if (serviceId) {
                    fetch(`${BASE_URL}get_days_availability.php`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                service_id: serviceId,
                                calendar_days_available: calendarDaysAvailable,
                                company_id: companyId
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const availableDays = data.available_days;
                                flatpickr("#date", {
                                    enableTime: false,
                                    dateFormat: "Y-m-d",
                                    minDate: "today",
                                    maxDate: new Date().fp_incr(calendarDaysAvailable),
                                    disable: [
                                        function(date) {
                                            const dayOfWeek = date.getDay();
                                            const isBlockedDay = blockedDates.includes(date
                                                .toISOString().split('T')[0]);
                                            const isAvailableDay = availableDays.includes(
                                                date.toISOString().split('T')[0]);
                                            return !workDays.includes(dayOfWeek) ||
                                                isBlockedDay || !isAvailableDay;
                                        }
                                    ]
                                });
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch(error => console.error('Error:', error));
                } else {
                    flatpickr("#date", {
                        enableTime: false,
                        dateFormat: "Y-m-d",
                        minDate: "today",
                        maxDate: new Date().fp_incr(calendarDaysAvailable),
                        disable: [
                            function(date) {
                                const dayOfWeek = date.getDay();
                                const isBlockedDay = blockedDates.includes(date.toISOString()
                                    .split('T')[0]);
                                return !workDays.includes(dayOfWeek) || isBlockedDay;
                            }
                        ]
                    });
                }
            });
        });
    </script>
    <script src="<?php echo $baseUrl; ?>assets/js/form.js"></script>
    <!-- Incluir Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</body>

</html>