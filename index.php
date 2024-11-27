<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu HTML</title>
    <link rel="stylesheet" href="css/styles.css">
    <!-- Lien vers le CDN Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
</head>
<body>
<div id="menu">
    <ul>
        <?php if (!isset($_COOKIE['user_id'])): ?>
            <li class="element_menu"><i class="bi bi-box-arrow-in-right"></i> <a href="index.php?action=signin">Log-in</a></li>
        <?php else: ?>
            <li class="element_menu"><i class="bi bi-house-heart"></i> <a href="index.php?action=accueil">Accueil</a></li>
            <li class="element_menu"><i class="bi bi-person-square"></i><a href="index.php?action=profile"> Profil</a></li>
            <li class="element_menu"><i class="bi bi-chat-dots"></i> <a href="index.php?action=contrainte">Fiche contrainte</a></li>
            <li class="element_menu"><i class="bi bi-chat-dots"></i> <a href="index.php?action=service">Fiche service</a></li>
            <li class="element_menu"><i class="bi bi-hash"></i> <a href="index.php?action=ressource">Fiche ressource</a></li>
            <li class="element_menu"><i class="bi bi-people"></i> <a href="index.php?action=planning">Planning détaillé</a></li>
        <?php endif; ?>
    </ul>
    <div id="deconnexion">
        <li class="element_menu"><i class="bi bi-box-arrow-in-left"></i> Deconnexion</li>
    </div>
</div>
<div id="corps">
    <?php
    require_once 'vendor/autoload.php';
    use src\Db\connexionFactory;
    use src\Dispatcher\dispatcher;

    connexionFactory::setConfig('src\Db\db.config.ini');

    session_start();

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $_SESSION['user_id'] = intval($_GET['id_utilisateur']);
    }

    $action = isset($_GET['action']) ? $_GET['action'] : 'default';


    $dispatcher = new Dispatcher($action);
    $dispatcher->run();
    ?>
</div>
<script>
    const deconnexion = document.querySelector('#deconnexion');
    deconnexion.addEventListener('click', () => {
        document.cookie = 'user_id=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        window.location.href = 'index.php?';
    });
</script>
</body>
</html>
