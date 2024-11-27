<?php

function returnHTML(): string {
    return <<<END
<head>
    <style>
        body {
            margin-left: 200px;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #333;
        }

        .container {
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 350px;
            text-align: center;
            margin-top: 8em;
            border: 1px solid #e0e0e0;
        }

        h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 24px;
            font-weight: bold;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 8px;
            font-weight: 500;
            text-align: left;
            color: #2c3e50;
        }

        input[type="email"],
        input[type="password"] {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #dcdcdc;
            border-radius: 5px;
            font-size: 16px;
            background-color: #fafafa;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #5dade2;
            outline: none;
            box-shadow: 0 0 5px rgba(93, 173, 226, 0.4);
        }

        input[type="submit"] {
            background-color: #5dade2;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 15px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #2e86c1;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Connexion</h2>
        <form action="index.php?action=signin" method="POST">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" placeholder="exemple@domaine.com" required><br>

            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" placeholder="Votre mot de passe" required><br>

            <input type="submit" value="Se connecter">
        </form>
    </div>
</body>

</html>
END;
}
?>
