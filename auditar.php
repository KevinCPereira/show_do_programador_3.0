<?php
function registrarAcao($conn, $usuario_id, $acao, $detalhes = null) {
    $sql = "INSERT INTO auditoria (usuario_id, acao, detalhes) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $usuario_id, $acao, $detalhes);
    $stmt->execute();
    $stmt->close();
}
?>
