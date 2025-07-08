<?php
$senha = 'saskuke3253'; // substitua pela senha real do professor
$hash = password_hash($senha, PASSWORD_DEFAULT);
echo "Hash da senha: " . $hash;
