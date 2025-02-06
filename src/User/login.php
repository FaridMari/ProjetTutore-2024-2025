<?php

function returnHTML(): string {
  return <<<END
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Accueil - Connexion</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;

            background-color: #f9f9f9; 
            
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            
            height: 100vh;
            color: #000;
        }

        .container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            width: 350px;
            max-width: 90%;
            padding: 2rem;
            text-align: center;
        }

        h2 {
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 600;
            color: #000;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
        }

        label {
            text-align: left;
            font-weight: 500;
            margin-bottom: 0.4em;
            color: #444;
        }

        input[type="email"],
        input[type="password"] {
            padding: 12px;
            margin-bottom: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            background-color: #fff;
            color: #000;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #FFD400;
            outline: none;
            box-shadow: 0 0 5px rgba(255,212,0,0.4);
        }

        input[type="submit"] {
            background-color: #FFEF65;
            color: #000;
            border: none;
            border-radius: 5px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #FFE74A;
            color: #000;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Connexion</h2>
        <form action="index.php?action=signin" method="POST">
            <label for="email">Email :</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                placeholder="exemple@domaine.com" 
                required
            />

            <label for="password">Mot de passe :</label>
            <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Votre mot de passe" 
                required
            />

            <input type="submit" value="Se connecter">
        </form>
    </div>
</body>
</html>
END;
}
?>
