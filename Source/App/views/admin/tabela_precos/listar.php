<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Tabela de Preços</title>
</head>
<body>
    <h1>Tabela de Preços</h1>
    <table border="1">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Grupo</th>
                <th>Preço</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dados as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nome']) ?></td>
                    <td><?= htmlspecialchars($item['grupo']) ?></td>
                    <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
