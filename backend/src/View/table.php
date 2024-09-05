<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>League Table</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<h1>League Table</h1>
<table>
    <thead>
    <tr>
        <th>Team</th>
        <th>Points</th>
        <th>Goal Difference</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($table as $team): ?>
        <tr>
            <td><?= $team->getName(); ?></td>
            <td><?= $team->getPoints(); ?></td>
            <td><?= $team->getGoalDifference(); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<button onclick="playMatches()">Play All Matches</button>

<script>
    function playMatches() {
        fetch('/play')
            .then(response => response.json())
            .then(data => {
                alert('Matches played: ' + JSON.stringify(data));
                window.location.reload();
            });
    }
</script>
</body>
</html>
