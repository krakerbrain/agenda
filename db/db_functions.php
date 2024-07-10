<?php
function getAppointment($conn, $id)
{
    $sql = $conn->prepare("SELECT s.name as service, a.* FROM appointments a
                            JOIN services s
                            ON a.id_service = s.id
                            WHERE a.id = :id 

    ");
    $sql->bindParam(':id', $id);
    $sql->execute();
    return $sql->fetch(PDO::FETCH_ASSOC);
}

function confirmAppointment($conn, $id)
{
    $update = $conn->prepare("UPDATE appointments SET status = 1 WHERE id = :id");
    $update->bindParam(':id', $id);
    $update->execute();
}
