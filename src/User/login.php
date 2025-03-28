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

    .toast {
      visibility: hidden;
      max-width: 300px;
      color: #fff;
      text-align: center;
      border-radius: 5px;
      padding: 16px;
      position: fixed;
      right: 20px;
      bottom: 20px;
      opacity: 0;
      transition: opacity 0.5s, visibility 0.5s;
    }

    .toast.show {
      visibility: visible;
      opacity: 0.7;
    }

    .toast.success {
      background-color: rgba(0, 128, 0, 0.7); /* Vert transparent */
    }

    .toast.error {
      background-color: rgba(255, 0, 0, 0.7); /* Rouge transparent */
    }
  </style>
</head>
<body>
<div class="container">
  <h2>Connexion</h2>
  <form id="loginForm">
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

<script>
  class Toast {
    constructor() {
      this.toastElement = document.createElement('div');
      this.toastElement.className = 'toast';
      document.body.appendChild(this.toastElement);
    }

    show(message, type = 'success') {
      this.toastElement.textContent = message;
      this.toastElement.className = `toast show ${type}`;
      setTimeout(() => {
        this.toastElement.className = this.toastElement.className.replace('show', '');
      }, 3000); // Le toast disparaît après 3 secondes
    }
  }

  const toast = new Toast();

  document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);

    fetch('index.php?action=signin', {
      method: 'POST',
      body: formData
    })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            window.location.href = data.redirect;
          } else {
            toast.show(data.message, 'error');
          }
        })
        .catch(error => {
          toast.show('Une erreur est survenue. Veuillez réessayer.', 'error');
        });
  });
</script>
</body>
</html>
